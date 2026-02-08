<?php

include_once(dirname(__FILE__) . '/../wc-tbank.php');

class SupportPaymentTBank
{

    public static $settings;

    function __construct($setting)
    {
        self::$settings = array(
            'payment_method_ffd'    => $setting['payment_method_ffd'],
            'payment_object_ffd'    => $setting['payment_object_ffd'],
            'email_company'         => $setting['email_company'],
            'check_data_tax'        => $setting['check_data_tax'],
            'taxation'              => $setting['taxation'],
            'payment_form_language' => $setting['payment_form_language'],
            'ffd'                   => $setting['ffd'],
        );
    }

    public static function logs($arrFields, $request)
    {
        // log send
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= json_encode($arrFields, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/../log.php", $log, FILE_APPEND);

        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .=  json_encode($request, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/../log.php", $log, FILE_APPEND);
       
    }

    public static function send_data($order, $order_id)
    {
        $arrCartItems = $order->get_items();
        $description = self::description_tbank($arrCartItems);

        $arrFields = array(
            'OrderId' => (string) $order_id,
            'Amount' => round($order->get_total() * 100),
            'Description' => $description,
            'DATA' => array('Email' => $order->get_billing_email(), 'Phone' => $order->get_billing_phone(), 'Connection_type' => 'wp-woocommerce-3.0.7'),
        );
        $emailCompany = mb_substr(self::$settings['email_company'], 0, 64);
        if (!$emailCompany){
            $emailCompany = null;
        }
        if (self::$settings['check_data_tax'] == 'yes') {
            $arrFields['Receipt'] = array(
                'EmailCompany' => $emailCompany,
                'Email' => $order->get_billing_email(),
                'Phone' => $order->get_billing_phone(),
                'Taxation' => self::$settings['taxation'],
                'Items' => self::get_receipt_items($order_id)
            );
            if (self::$settings['ffd'] == 'ffd12')  {
                $arrFields['Receipt']["FfdVersion"] = "1.2";
            }
            if (self::$settings['ffd'] == 'ffd105')  {
                $arrFields['Receipt']["FfdVersion"] = "1.05";
            }
        }

        return $arrFields;
    }

    public static function get_receipt_items($order_id)
    {
        global $wpdb;

        $order = new WC_Order($order_id);
        $vat = '';
        $items = array();
        foreach ($order->get_items() as $item) {
            $_tax = new WC_Tax();
            $ratesData = $_tax->get_rates($item->get_product()->get_tax_class());
            $rates = array_shift($ratesData);
            $ratOne = $rates['rate'];
            $compoundOne = $rates['compound'];

            if ($item->get_product()->get_tax_status() != 'none') {

                $_tax = new WC_Tax();
                $ratesData = $_tax->get_rates($item->get_product()->get_tax_class());
                $rates = array_shift($ratesData);
                $ratOne = $rates['rate'];
                $compoundOne = $rates['compound'];
                //Take only the item rate and round it.

                if ($rates)
                    $item_rate = round(array_shift($rates));
                $vat = self::getTaxForSend($item_rate);
                $rate = array(
                    array(
                        'rate' => $ratOne,
                        'compound' => $compoundOne,
                    )
                );
                $price = $item->get_product()->get_price();

                $tax = 0;

                /* если настройка на Нет */
                if (!wc_prices_include_tax()) {
                    $tax = WC_Tax::calc_tax($price, $rate, false);

                    foreach ($tax as $tax) {
                        $price += $tax;
                    }

                }
            } else {
                $price = $item->get_product()->get_price();
                $vat = 'none';
            }

            $quantity = $item->get_quantity();
            $newItem = array(
                'Name' => mb_substr($item->get_product()->get_name(), 0, 128),
                'Price' => round($price * 100),
                'Quantity' => round($quantity, 2),
                'Amount' => round($price * $quantity * 100),
                'PaymentMethod' => trim(self::$settings['payment_method_ffd']),
                'PaymentObject' => trim(self::$settings['payment_object_ffd']),
                'Tax' => $vat,
            );
            if (self::$settings['ffd'] === "ffd12")  {
                $newItem['MeasurementUnit'] = "pc";
            }
            array_push($items, $newItem);
        }

        $shippingPrice = $order->get_shipping_total();
        $isShipping = false;
        if ($shippingPrice > 0) {
            $shippingPriceTax = round($order->get_shipping_tax() * 100);
            $shippingPrice = round($shippingPrice * 100);
            $shippingPriceTax += $shippingPrice;

            $orderItemId = $wpdb->get_row("
                  SELECT order_item_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_item_type = 'tax' and order_id = $order_id
                ");

            if (!empty($orderItemId)) {
                $taxRateData = $wpdb->get_row("
                    SELECT * FROM " . $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_id =
                        (SELECT meta_value FROM " . $wpdb->prefix . "woocommerce_order_itemmeta WHERE meta_key = 'rate_id' and order_item_id = $orderItemId->order_item_id)
                    ");
                // налог на доставку вкл
                if ($taxRateData->tax_rate_shipping == 1) {
                    $shippingTax = self::getTaxForSend(round($taxRateData->tax_rate));
                } else {
                    $shippingTax = 'none';
                }
            } else {
                $shippingTax = 'none';
            }

            $shippingItem = array(
                'Name' => mb_substr($order->get_shipping_method(), 0, 128),
                'Price' => $shippingPriceTax,
                'Quantity' => 1,
                'Amount' => $shippingPriceTax,
                'PaymentMethod' => trim(self::$settings['payment_method_ffd']),
                'PaymentObject' => 'service',
                'Tax' => $shippingTax,
            );
            if (self::$settings['ffd'] === "ffd12")  {
                $shippingItem['MeasurementUnit'] = "pc";
            }
            array_push($items, $shippingItem);
            $isShipping = true;
        }

        $amount = round($order->get_total() * 100);

        return self::balance_amount($isShipping, $items, $amount);
    }

    protected static function getTaxForSend($tax)
    {
        if (!isset($tax))
            return 'none';
        switch ($tax) {
            case 22:
                $vat = 'vat22';
                break;
            case 20:
                $vat = 'vat20';
                break;
            case 10:
                $vat = 'vat10';
                break;
            case 7:
                $vat = 'vat7';
                break;
            case 5:
                $vat = 'vat5';
                break;
            case 0:
                $vat = 'vat0';
                break;
            case !$tax:
                $vat = 'none';
            default:
                $vat = "vat" . (string) $tax;
        }
        return $vat;
    }

    public static function balance_amount($isShipping, $items, $amount)
    {
        $itemsWithoutShipping = $items;

        if ($isShipping) {
            $shipping = array_pop($itemsWithoutShipping);
        }

        $sum = 0;

        foreach ($itemsWithoutShipping as $item) {
            $sum += $item['Amount'];
        }

        if (isset($shipping)) {
            $sum += $shipping['Amount'];
        }

        if ($sum != $amount) {
            $sumAmountNew = 0;
            $difference = $amount - $sum;
            $amountNews = array();

            foreach ($itemsWithoutShipping as $key => $item) {
                $itemsAmountNew = $item['Amount'] + floor($difference * $item['Amount'] / $sum);
                $amountNews[$key] = $itemsAmountNew;
                $sumAmountNew += $itemsAmountNew;
            }

            if (isset($shipping)) {
                $sumAmountNew += $shipping['Amount'];
            }

            if ($sumAmountNew != $amount) {
                $max_key = array_keys($amountNews, max($amountNews))[0];    // ключ макс значения
                $amountNews[$max_key] = max($amountNews) + ($amount - $sumAmountNew);
            }

            foreach ($amountNews as $key => $item) {
                $items[$key]['Amount'] = $amountNews[$key];
            }

            // работа с купонами

            $couponValue = WC()->cart->applied_coupons;

            foreach ($amountNews as $key => $item) {
                $items[$key]['Amount'] = $amountNews[$key];
                if(isset($couponValue)) {
                    $finalPrice = $amountNews[$key] / $items[$key]['Quantity'];
                    $items[$key]['Price'] = $finalPrice;
                }
            }

        }
        return $items;
    }

    public static function get_setting_language($arrFields)
    {
        if (self::$settings['payment_form_language'] == 'en') {
            $arrFields['Language'] = "en";
        }

        return $arrFields;
    }

    public static function description_tbank($arrCartItems)
    {
        $strDescription = '';

        foreach ($arrCartItems as $arrItem) {
            $strDescription .= $arrItem['name'];
            if ($arrItem['qty'] > 1) {
                $strDescription .= '*' . $arrItem['qty'] . "; ";
            } else {
                $strDescription .= "; ";
            }
        }

        if (strlen($strDescription) > 250) {
            $strDescription = mb_substr($strDescription, 0, 247) . '...';
        }

        return $strDescription;
    }

}
