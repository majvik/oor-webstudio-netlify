<?php

require_once('TBankMerchantAPI.php');
require_once('SupportPaymentTBank.php');
include_once(dirname(__FILE__) . '/../wc-tbank.php');

class RecurrentPaymentTBank
{
    private $amount;
    private $order;

    public function __construct($amount, $order)
    {
        $this->amount = $amount;
        $this->order = $order;
    }


    public function recurrentTBank($setting)
    {
        global $wpdb;

        $supportPaymentTBank = new SupportPaymentTBank($setting);
        $amount = $this->amount;

        $order = $this->order;
        if (0 == $amount) {
            $order->payment_complete();
            return;
        }

        $order_id = $order->id;
        $order = new WC_Order($order_id);

        $arrFields = SupportPaymentTBank::send_data($order, $order_id);
        $arrFields = SupportPaymentTBank::get_setting_language($arrFields);

        $TBank = new TBankMerchantAPI($setting['merchant_id'], $setting['secret_key']);
        $request = $TBank->buildQuery('Init', $arrFields);
        SupportPaymentTBank::logs($arrFields, $request);
        if ($paymentId = json_decode($request)->PaymentId) {
            // поиск родительского заказа у рекуррентоного платежа
            $subscriptions_for_order = wcs_get_subscriptions_for_order($order_id, array('order_type' => 'any'));

            if (!empty($subscriptions_for_order)) {
                $subscription = array_pop($subscriptions_for_order);
                $parentOrder = $subscription->get_parent_id();

                $table_name = $wpdb->prefix . "recurrent_tbank";
                $rebillId = $wpdb->get_var(
                    " SELECT rebillId
                            FROM $table_name WHERE paymentId = $parentOrder "
                );

                $chargeFields = array(
                    'PaymentId' => $paymentId,
                    'RebillId' => $rebillId,
                );

                $TBankCharge = new TBankMerchantAPI($setting['merchant_id'], $setting['secret_key']);
                $request1 = $TBankCharge->buildQuery('Charge', $chargeFields);
                SupportPaymentTBank::logs($chargeFields, $request1);

            }
        }
    }

    public function logs($arrFields, $request)
    {
        // log send
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= json_encode($arrFields, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        $log .= "----";
        file_put_contents(dirname(__FILE__) . "/log.php", $log, FILE_APPEND);

        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= $request;
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/log.php", $log, FILE_APPEND);
    }

    public function send_data($order, $order_id)
    {
        $arrCartItems = $order->get_items();
        $description = $this->description_tbank($arrCartItems);

        $arrFields = array(
            'OrderId' => $order_id,
            'Amount' => round($order->get_total() * 100),
            'Description' => $description,
            'DATA' => array('Email' => $order->get_billing_email(), 'Connection_type' => 'wp-woocommerce-subscriptions',),
        );

        $emailCompany = mb_substr($this->email_company, 0, 64);
        if (!$emailCompany){
            $emailCompany = null;
        }
        if ($this->check_data_tax == 'yes') {
            $arrFields['Receipt'] = array(
                'EmailCompany' => $emailCompany,
                'Email' => $order->get_billing_email(),
                'Phone' => $order->get_billing_phone(),
                'Taxation' => $this->taxation,
                'Items' => $this->get_receipt_items($order_id),
            );
        }

        return $arrFields;
    }

    public function get_setting_language($arrFields)
    {
        if ($this->get_option('payment_form_language') == 'en') {
            $arrFields['Language'] = "en";
        }

        return $arrFields;
    }

    function description_tbank($arrCartItems)
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
