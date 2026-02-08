<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

require_once('tbank/TBankMerchantAPI.php');
require_once('tbank/language/ru/RuLanguageTBank.php');
require_once('tbank/language/LanguageTBank.php');
require_once('tbank/RecurrentPaymentTBank.php');

/**
 * Plugin Name: Т-Банк
 * Plugin URI: https://tbank.ru/
 * Description: Проведение платежей через T-Bank
 * Version: 3.0.7
 * Author: T-Bank
 */


/* Add a custom payment class to WC
  ------------------------------------------------------------ */
register_activation_hook(__FILE__, 'create_table_recurrent_tbank');

add_action('plugins_loaded', 'woocommerce_tbank', 0);

function create_table_recurrent_tbank()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "recurrent_tbank";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
              id int(10) NOT NULL AUTO_INCREMENT,
              rebillId VARCHAR (15) NOT NULL,
              paymentId int(10) NOT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function woocommerce_tbank()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    } // if the WC payment gateway class is not available, do nothing
    if (class_exists('WC_TBank')) {
        return;
    }

    class WC_TBank extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $plugin_dir = plugin_dir_url(__FILE__);

            global $woocommerce;

            $this->id = 'tbank';
            $this->icon = apply_filters('woocommerce_tbank_icon', '' . $plugin_dir . 'tbank/tbank.png');
            $this->has_fields = false;

            // Load the settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            if ($this->get_option('payment_form_language') == 'en') {
                $this->icon = apply_filters('woocommerce_tbank_icon', '' . $plugin_dir . 'tbank/tbank-en.png');
                $this->title = 'T-Bank';
                $this->description = 'Payment via T-Bank';
            }
            $this->email_company = $this->get_option('email_company');
            $this->payment_method_ffd = $this->get_option('payment_method_ffd');
            $this->payment_object_ffd = $this->get_option('payment_object_ffd');
            $this->taxation = $this->get_option('taxation');
            $this->instructions = $this->get_option('instructions');
            $this->check_data_tax = $this->get_option('check_data_tax');

            // Actions
            add_action('woocommerce_receipt_tbank', array($this, 'receipt_page'));

            // Save options
            add_action('woocommerce_update_options_payment_gateways_tbank', array($this, 'process_admin_options'));

            // Payment listener/API hook
            add_action('woocommerce_api_wc_tbank', array($this, 'check_assistant_response'));

            if (!$this->is_valid_for_use()) {
                $this->enabled = false;
            }

            $this->supports = array_merge(
                $this->supports,
                array(
                    'subscriptions',
                    'subscription_cancellation',
                    'subscription_reactivation',
                    'subscription_suspension',
                    'multiple_subscriptions',
                    'subscription_payment_method_change_customer',
                    'subscription_payment_method_change_admin',
                    'subscription_amount_changes',
                    'subscription_date_changes',
                )
            );

            $this->_maybe_register_callback_in_subscriptions_t();
        }

        /**
         * Check if this gateway is enabled and available in the user's country
         */
        function is_valid_for_use()
        {
            return true;
        }

        protected function _maybe_register_callback_in_subscriptions_t()
        {
            add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array($this, 'scheduled_subscription_payment'), 10, 2);
        }

        public function scheduled_subscription_payment($amount, $order)
        {
            $setting = array(
                "email_company" => $this->get_option('email_company'),
                "payment_method_ffd" => $this->get_option('payment_method_ffd'),
                "check_data_tax" => $this->get_option('check_data_tax'),
                "taxation" => $this->get_option('taxation'),
                "payment_form_language" => $this->get_option('payment_form_language'),
                "merchant_id" => $this->get_option('merchant_id'),
                "secret_key" => $this->get_option('secret_key'),
            );
            $recurrentPayment = new RecurrentPaymentTBank($amount, $order);
            $recurrentPayment->recurrentTBank($setting);
        }

        /**
         * Форма оплаты
         **/
        function receipt_page($order_id)
        {
            $order = new WC_Order($order_id);
            $setting = array(
                "email_company" => $this->get_option('email_company'),
                "payment_method_ffd" => $this->get_option('payment_method_ffd'),
                "payment_object_ffd" => $this->get_option('payment_object_ffd'),
                "check_data_tax" => $this->get_option('check_data_tax'),
                "taxation" => $this->get_option('taxation'),
                "payment_form_language" => $this->get_option('payment_form_language'),
                "ffd" => $this->get_option('ffd')
            );

            $supportPaymentTBank = new SupportPaymentTBank($setting);
            $arrFields = SupportPaymentTBank::send_data($order, $order_id);

            if (!function_exists('get_plugins')) {
                // подключим файл с функцией get_plugins()
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            // получим данные плагинов
            $all_plugins = get_plugins();

            foreach ($all_plugins as $key => $plugin) {
                preg_match_all('#woocommerce-subscriptions-[0-9.-]+\/woocommerce-subscriptions.php#uis', $key, $pluginSubscriptions);

                if (!empty($pluginSubscriptions[0])) {
                    // активирован ли плагин woocommerce-subscriptions
                    if (is_plugin_active($key)) {

                        if (wcs_order_contains_subscription($order)) {
                            $arrFields['Recurrent'] = "Y";
                            $arrFields['CustomerKey'] = (string)$order->get_user_id();
                        }
                    }
                }
            }

            $arrFields = SupportPaymentTBank::get_setting_language($arrFields);

            $TBank = new TBankMerchantAPI($this->get_option('merchant_id'), $this->get_option('secret_key'));
            $request = $TBank->buildQuery('Init', $arrFields);
            $request = json_decode($request);
            if (!$request->Success) {
                $log = ["OrderId" => $arrFields["OrderId"]];
                SupportPaymentTBank::logs($arrFields["OrderId"], $request);
            }

            if (!empty($this->payment_system_name)) {
                $arrFields['payment_system_name'] = $this->payment_system_name;
            }

            foreach ($arrFields as $strFieldName => $strFieldValue) {
                $args_array[] = '<input type="hidden" name="' . esc_attr($strFieldName) . '" value="' . esc_attr($strFieldValue) . '" />';
            }

            if (isset($request->PaymentURL)) {
                $reduce_stock_levels = $this->get_option('reduce_stock_levels');
                if ($reduce_stock_levels !== 'yes') {
                    try {
                        wc_reduce_stock_levels($order_id);
                    } catch (Exception $e) {

                    }
                }
                setcookie('tbankReturnUrl', $this->get_return_url($order), time() + 3600, "/");
                wp_redirect($request->PaymentURL);
            } else {
                echo '<p>' . LanguageTBank::get(LanguageTBank::REQUEST_TO_PAYMENT) . '</p>';
            }
        }


        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @since 0.1
         **/
        public function admin_options()
        {
            ?>
            <h3><?php _e('T-Bank', 'woocommerce'); ?></h3>
            <p><?php _e(LanguageTBank::get(LanguageTBank::SETUP_OF_RECEIVING), 'woocommerce'); ?></p>

            <?php if ($this->is_valid_for_use()) : ?>

            <table class="form-table">
                <?php
                // Generate the HTML For the settings form.
                $this->generate_settings_html();
                ?>
            </table>

        <?php else : ?>
            <div class="inline error"><p>
                    <strong><?php _e(LanguageTBank::get(LanguageTBank::GATEWAY_IS_DISABLED),
                            'woocommerce'); ?></strong>: <?php _e(LanguageTBank::get(LanguageTBank::T_DOES_NOT_SUPPORT),
                        'woocommerce'); ?>
                </p></div>
        <?php
        endif;

        } //End admin_options()

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'general_settings_section' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::GENERAL_SETTINGS_SECTION), 'woocommerce'),
                    'type' => 'title',
                ),
                'enabled' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD), 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __(LanguageTBank::get(LanguageTBank::ACTIVE), 'woocommerce'),
                    'default' => 'yes'
                ),
                'merchant_id' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::TERMINAL), 'woocommerce'),
                    'type' => 'text',
                    'description' => __(LanguageTBank::get(LanguageTBank::SPECIFIED_PERSONAL), 'woocommerce'),
                    'default' => ''
                ),
                'secret_key' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PASSWORD), 'woocommerce'),
                    'type' => 'text',
                    'description' => __(LanguageTBank::get(LanguageTBank::SPECIFIED_PERSONAL), 'woocommerce'),
                    'default' => ''
                ),
                'auto_complete' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::ORDER_COMPLETION), 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __(LanguageTBank::get(LanguageTBank::AUTOMATIC_SUCCESSFUL),
                        'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::AUTOMATIC_DESCRIPTION), 'woocommerce'),
                    'default' => '0'
                ),
                'reduce_stock_levels' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::REDUCE_STOCK_LEVELS), 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __(LanguageTBank::get(LanguageTBank::REDUCE_STOCK_LEVELS_AFTER_PAYMENT),
                        'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::REDUCE_STOCK_LEVELS_DESCRIPTION), 'woocommerce'),
                    'default' => 'yes'
                ),
                'payment_form_settings' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_FORM_SETTINGS), 'woocommerce'),
                    'type' => 'title',
                ),
                'title' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD_NAME), 'woocommerce'),
                    'type' => 'text',
                    'description' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD_USER), 'woocommerce'),
                    'default' => __(LanguageTBank::get(LanguageTBank::T_BANK), 'woocommerce')
                ),
                'description' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD_DESCRIPTION), 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __(LanguageTBank::get(LanguageTBank::DESCRIPTION_PAYMENT_METHOD),
                        'woocommerce'),
                    'default' => LanguageTBank::get(LanguageTBank::PAYMENT_THROUGH)
                ),
                'payment_form_language' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_LANGUAGE), 'woocommerce'),
                    'type' => 'select',
                    'description' => __(LanguageTBank::get(LanguageTBank::CHOOSE_PAYMENT_LANGUAGE)),
                    'default' => 'ru',
                    'options' => array(
                        'ru' => __(LanguageTBank::get(LanguageTBank::RUSSIA), 'woocommerce'),
                        'en' => __(LanguageTBank::get(LanguageTBank::ENGLISH), 'woocommerce'),
                    ),
                ),
                'setting_check_sending' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::SETTING_CHECK_SENDING), 'woocommerce'),
                    'type' => 'title',
                ),
                'check_data_tax' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::SEND_DATA_CHECK), 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __(LanguageTBank::get(LanguageTBank::DATA_TRANSFER), 'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::SEND_DATA_CHECK_DESCRIPTON), 'woocommerce'),
                    'default' => '0'
                ),
                'ffd' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::FFD), 'woocommerce'),
                    'type' => 'select',
                    'label' => __(LanguageTBank::get(LanguageTBank::FFD_DESCRIPTION),
                        'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::FFD_ADVICE), 'woocommerce'),
                    'default' => 'error',
                    'options' => array(
                        'error' => __('', 'woocommerce'),
                        'ffd105' => __(LanguageTBank::get(LanguageTBank::FFD105), 'woocommerce'),
                        'ffd12' => __(LanguageTBank::get(LanguageTBank::FFD12), 'woocommerce'),
                    ),
                ),
                'taxation' => array(
                    'title' => __(LanguageTBank::get(LanguageTBank::TAX_SYSTEM), 'woocommerce'),
                    'type' => 'select',
                    'description' => __(LanguageTBank::get(LanguageTBank::CHOOSE_SYSTEM_STORE)),
                    'default' => 'error',
                    'options' => array(
                        'error' => __('', 'woocommerce'),
                        'osn' => __(LanguageTBank::get(LanguageTBank::TOTAL_CH), 'woocommerce'),
                        'usn_income' => __(LanguageTBank::get(LanguageTBank::SIMPLIFIED_CH), 'woocommerce'),
                        'usn_income_outcome' => __(LanguageTBank::get(LanguageTBank::SIMPLIFIED__COSTS), 'woocommerce'),
                        'esn' => __(LanguageTBank::get(LanguageTBank::UNIFIED_AGRICULTURAL_TAX), 'woocommerce'),
                        'patent' => __(LanguageTBank::get(LanguageTBank::PATENT_CH), 'woocommerce'),
                    ),
                ),
                'email_company' => array(
                    'type' => 'text',
                    'title' => __(LanguageTBank::get(LanguageTBank::EMAIL_COMPANY), 'woocommerce'),
                    'default' => '',
                ),
                'payment_object_ffd' => array(
                    'type' => 'select',
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_OBJECT_FFD), 'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::PAYMENT_OBJECT_FFD_DESCRIPTION)),
                    'default' => 'error',
                    'options' => array(
                        'error' => __('', 'woocommerce'),
                        'commodity' => __(LanguageTBank::get(LanguageTBank::COMMODITY), 'woocommerce'),
                        'excise' => __(LanguageTBank::get(LanguageTBank::EXCISE), 'woocommerce'),
                        'job' => __(LanguageTBank::get(LanguageTBank::JOB), 'woocommerce'),
                        'service' => __(LanguageTBank::get(LanguageTBank::SERVICE), 'woocommerce'),
                        'gambling_bet' => __(LanguageTBank::get(LanguageTBank::GAMBLING_BET), 'woocommerce'),
                        'gambling_prize' => __(LanguageTBank::get(LanguageTBank::GAMBLING_PRIZE), 'woocommerce'),
                        'lottery' => __(LanguageTBank::get(LanguageTBank::LOTTERY), 'woocommerce'),
                        'lottery_prize' => __(LanguageTBank::get(LanguageTBank::LOTTERY_PRIZE), 'woocommerce'),
                        'intellectual_activity' => __(LanguageTBank::get(LanguageTBank::INTELLECTUAL_ACTIVITY), 'woocommerce'),
                        'payment' => __(LanguageTBank::get(LanguageTBank::PAYMENT), 'woocommerce'),
                        'agent_commission' => __(LanguageTBank::get(LanguageTBank::AGENT_COMMISSION), 'woocommerce'),
                        'composite' => __(LanguageTBank::get(LanguageTBank::COMPOSITE), 'woocommerce'),
                        'another' => __(LanguageTBank::get(LanguageTBank::ANOTHER), 'woocommerce'),
                    ),
                ),
                'payment_method_ffd' => array(
                    'type' => 'select',
                    'title' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD_FFD), 'woocommerce'),
                    'description' => __(LanguageTBank::get(LanguageTBank::PAYMENT_METHOD_FFD_DESCRIPTION)),
                    'default' => 'error',
                    'options' => array(
                        'error' => __('', 'woocommerce'),
                        'full_prepayment' => __(LanguageTBank::get(LanguageTBank::FULL_PREPAYMENT), 'woocommerce'),
                        'prepayment' => __(LanguageTBank::get(LanguageTBank::PREPAYMENT), 'woocommerce'),
                        'advance' => __(LanguageTBank::get(LanguageTBank::ADVANCE), 'woocommerce'),
                        'full_payment' => __(LanguageTBank::get(LanguageTBank::FULL_PAYMENT), 'woocommerce'),
                        'partial_payment' => __(LanguageTBank::get(LanguageTBank::PARTIAL_PAYMENT), 'woocommerce'),
                        'credit' => __(LanguageTBank::get(LanguageTBank::CREDIT), 'woocommerce'),
                        'credit_payment' => __(LanguageTBank::get(LanguageTBank::CREDIT_PAYMENT), 'woocommerce'),
                    ),
                ),
            );

        }

        /**
         * Дополнительная информация в форме выбора способа оплаты
         **/
        function payment_fields()
        {
            if ($this->description) {
                echo wpautop(wptexturize($this->description));
            }
        }

        /**
         * Process the payment and return the result
         **/
        function process_payment($order_id)
        {
            $order = new WC_Order($order_id);

            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url($order)
            );
        }

        /**
         * Check Response
         **/
        function check_assistant_response()
        {
            global $woocommerce;

            if (!empty($_POST)) {
                $arrRequest = $_POST;
            } else {
                $arrRequest = $_GET;
            }

            $objOrder = new WC_Order($arrRequest['pg_order_id']);

            $arrResponse = array();
            $aGoodCheckStatuses = array('pending', 'processing');
            $aGoodResultStatuses = array('pending', 'processing', 'completed');

            switch ($_GET['type']) {
                case 'check':
                    $bCheckResult = 1;
                    if (empty($objOrder) || !in_array($objOrder->status, $aGoodCheckStatuses)) {
                        $bCheckResult = 0;
                        $error_desc = 'Order status ' . $objOrder->status . ' or deleted order';
                    }
                    if (intval($objOrder->order_total) != intval($arrRequest['pg_amount'])) {
                        $bCheckResult = 0;
                        $error_desc = 'Wrong amount';
                    }

                    $arrResponse['pg_salt'] = $arrRequest['pg_salt'];
                    $arrResponse['pg_status'] = $bCheckResult ? 'ok' : 'error';
                    $arrResponse['pg_error_description'] = $bCheckResult ? "" : $error_desc;

                    $objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
                    $objResponse->addChild('pg_salt', $arrResponse['pg_salt']);
                    $objResponse->addChild('pg_status', $arrResponse['pg_status']);
                    $objResponse->addChild('pg_error_description', $arrResponse['pg_error_description']);
                    $objResponse->addChild('pg_sig', $arrResponse['pg_sig']);
                    break;

                case 'result':
                    if (intval($objOrder->order_total) != intval($arrRequest['pg_amount'])) {
                        $strResponseDescription = 'Wrong amount';
                        if ($arrRequest['pg_can_reject'] == 1) {
                            $strResponseStatus = 'rejected';
                        } else {
                            $strResponseStatus = 'error';
                        }
                    } elseif ((empty($objOrder) || !in_array($objOrder->status, $aGoodResultStatuses)) &&
                        !($arrRequest['pg_result'] == 0 && $objOrder->status == 'failed')
                    ) {
                        $strResponseDescription = 'Order status ' . $objOrder->status . ' or deleted order';
                        if ($arrRequest['pg_can_reject'] == 1) {
                            $strResponseStatus = 'rejected';
                        } else {
                            $strResponseStatus = 'error';
                        }
                    } else {
                        $strResponseStatus = 'ok';
                        $strResponseDescription = "Request cleared";

                        if ($arrRequest['pg_result'] == 1) {
                            $objOrder->update_status('completed', __(LanguageTBank::get(LanguageTBank::PAYMENT_SUCCESS), 'woocommerce'));
                            WC()->cart->empty_cart();
                        } else {
                            $objOrder->update_status('failed', __(LanguageTBank::get(LanguageTBank::PAYMENT_NOT_SUCCESS), 'woocommerce'));
                            WC()->cart->empty_cart();
                        }
                    }

                    $objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
                    $objResponse->addChild('pg_salt', $arrRequest['pg_salt']);
                    $objResponse->addChild('pg_status', $strResponseStatus);
                    $objResponse->addChild('pg_description', $strResponseDescription);

                    break;
                case 'success':
                    wp_redirect($this->get_return_url($objOrder));
                    break;
                case 'failed':
                    wp_redirect($objOrder->get_cancel_order_url());
                    break;
                default :
                    die('wrong type');
            }

            header("Content-type: text/xml");
            echo $objResponse->asXML();
            die();
        }

        function showMessage($content)
        {
            return '
        <h1>' . $this->msg['title'] . '</h1>
        <div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>
        ';
        }

        function showTitle($title)
        {
            return false;
        }
    }

    /**
     * Add the gateway to WooCommerce
     **/
    function add_tbank_gateway($methods)
    {
        $methods[] = 'WC_TBank';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_tbank_gateway');
}

/////////////// success page

add_filter('query_vars', 'tbank_success_query_vars');
function tbank_success_query_vars($query_vars)
{
    $query_vars[] = 'tbank_success';
    return $query_vars;
}


add_action('parse_request', 'tbank_success_parse_request');
function tbank_success_parse_request(&$wp)
{
    if (array_key_exists('tbank_success', $wp->query_vars)) {
        $a = new WC_TBank();
        add_action('the_title', array($a, 'showTitle'));
        add_action('the_content', array($a, 'showMessage'));

        if ($wp->query_vars['tbank_success'] == 1) {
            if (isset($_COOKIE['tbankReturnUrl'])) {
                $tbankReturnUrl = $_COOKIE['tbankReturnUrl'];
                unset($_COOKIE['tbankReturnUrl']);
                echo "<script language=\"javascript\" type=\"text/javascript\">document.location.replace('" . $tbankReturnUrl . "');</script>";
            } else {
                $a->msg['title'] = LanguageTBank::get(LanguageTBank::PAYMENT_SUCCESS);
                $a->msg['message'] = LanguageTBank::get(LanguageTBank::PAYMENT_THANK);
                $a->msg['class'] = 'woocommerce_message woocommerce_message_info';
                WC()->cart->empty_cart();
            }
        } else {
            $a->msg['title'] = LanguageTBank::get(LanguageTBank::PAYMENT_NOT_SUCCESS);
            $a->msg['message'] = LanguageTBank::get(LanguageTBank::PAYMENT_ERROR);
            $a->msg['class'] = 'woocommerce_message woocommerce_message_info';
        }
    }
    return;
}

/////////////// success page end
?>
