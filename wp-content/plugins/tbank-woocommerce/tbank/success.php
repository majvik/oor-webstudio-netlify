<?php
set_error_handler('exceptions_error_handler', E_ALL);
function exceptions_error_handler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() == 0 || $errno != E_ERROR) {
        return;
    }

    die('SYSTEM ERROR'); // ошибка в работе CMS
}

try {
    require(dirname(__FILE__) . '../../../../../wp-blog-header.php');
    $request = (array)json_decode(file_get_contents('php://input'));
    header("HTTP/1.1 200 ok");
    //woocommerce_tbank_settings
    $settings = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name='woocommerce_tbank_settings'");
    $settings = unserialize($settings[0]->option_value);

    // Data fix
    if(isset($request['Data'])) {
        unset($request['Data']);
    }

    $request['Password'] = htmlspecialchars_decode($settings['secret_key']);
    ksort($request);
    $request_str = json_encode($request);
    $original_token = $request['Token'];
    unset($request['Token']);
    
    $request['Success'] = $request['Success'] === true ? 'true' : 'false';

    $values = '';
    foreach ($request as $key => $val) {
        $values .= $val;
    }

    $token = hash('sha256', $values);
    
    if ($token == $original_token) {
        $orders = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id=" . (int)$request['OrderId']);
        $order_status = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=" . $orders[0]->order_id);
        $status = $order_status[0]->post_status;
        $order_id = $request['OrderId'];
        $order = wc_get_order((int)$order_id);
        if (!$order){
            die("ORDER NOT FOUND");
        }
        if ((int)(round($order->get_total()*100)) !== $request['Amount']) {
            die("AMOUNTS DO NOT MATCH");
        }
        /* Если заказ выполнен или обрабатывается - ничего не менять */
        if($status == 'wc-completed' || $status == 'wc-processing') die('OK');

        switch ($request['Status']) {
            case 'AUTHORIZED':
                $order_status = 'wc-on-hold';
                break; /*Деньги на карте захолдированы. Корзина очищается.*/
            case 'CONFIRMED':
                $order->payment_complete($order->get_transaction_id());
                if (function_exists('wcs_get_subscriptions_for_order')) {
                    write_Rebillid_TBank($request);
                }
                die('OK');
                break; /*Платеж подтвержден.*/
            case 'REJECTED':
                $order_status = 'wc-failed';
                break; /*Платеж отклонен.*/
            case 'CANCELED':
            case 'REVERSED':
                $order_status = 'wc-cancelled';
                break; /*Платеж отменен*/
            case 'REFUNDED':
                $order_status = 'wc-refunded';
                $order->update_status($order_status);
                do_action('woocommerce_order_edit_status', (int)$request['OrderId'], $order_status);        
                break; /*Произведен возврат денег клиенту*/
        }
        $order->update_status($order_status);
        do_action('woocommerce_order_edit_status', (int)$request['OrderId'], $order_status);
    
        if (function_exists('wcs_get_subscriptions_for_order')) {
            write_Rebillid_TBank($request);
        }

        die('OK');
    } else {
        die('TOKEN ISNOT CORRECT'); // указан некорректный пароль
    }
} catch (Exception $e) {
    die($e); // ошибка в работе модуля
}

function write_Rebillid_TBank($request)
{
    global $wpdb;

    $tableName = $wpdb->prefix . "recurrent_tbank";
    $subscriptionsForOrder = wcs_get_subscriptions_for_order($request['OrderId'], array('order_type' => 'any'));

    if (!empty($subscriptionsForOrder)) {
        $subscription = array_pop($subscriptionsForOrder);
        $parentOrder = $subscription->get_parent_id();
        //если в нотификации пришел родительский платеж, записываем в таблицу RebillId
        if ($parentOrder == $request['OrderId']) {
            $wpdb->insert(
                $tableName,
                array('rebillId' => $request['RebillId'], 'paymentId' => $request['OrderId']),
                array('%s', '%s')
            );
        }
    }
}
