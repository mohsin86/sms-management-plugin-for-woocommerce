<?php

defined( 'ABSPATH' ) or die( 'Hush! Stay away please!' );

class sendSMS
{

    public function __construct()
    {

        add_action( 'woocommerce_order_status_pending', array($this,'order_pending')) ;

        add_action( 'woocommerce_order_status_failed',  array($this,'order_failed'));

        add_action( 'woocommerce_order_status_on-hold',  array($this,'order_hold'));

        add_action( 'woocommerce_order_status_delivery-man',  array($this,'delivery_man'));

        add_action( 'woocommerce_order_status_processing',  array($this,'order_processing'));

        add_action( 'woocommerce_order_status_completed',  array($this,'order_completed'));

        add_action( 'woocommerce_order_status_refunded',  array($this,'order_refunded'));

        add_action( 'woocommerce_order_status_cancelled',  array($this,'order_cancelled'));

        add_action( 'woocommerce_new_order', array($this,'new_order'),  1, 1  );

        add_action('woocommerce_payment_complete',array($this,'order_payment_complete' ));

    }

    /**
     * Add rider user-role on plugin activation
     **/

    public function order_pending($order_id) {
            self::_send_sms('order_status_pending',$order_id);
    }

    public function order_failed($order_id) {
            self::_send_sms('order_status_failed',$order_id);
    }

    public function order_hold($order_id) {
        self::_send_sms('order_status_hold',$order_id);
    }

    public  function order_processing($order_id) {
        self::_send_sms('order_status_processing',$order_id);
    }

    public  function order_completed($order_id) {
        self::_send_sms('order_status_completed',$order_id);
    }

    public function order_refunded($order_id) {
        self::_send_sms('order_status_refunded',$order_id);
    }

    public function order_cancelled($order_id) {
        self::_send_sms('order_status_cancelled',$order_id);
    }

    public function order_payment_complete( $order_id ) {
        self::_send_sms('order_status_pending',$order_id);

    }

    public function new_order( $order_id ) {
        self::_send_sms('new_order',$order_id);

    }

    public function order_payment_pending( $order_id ) {
        self::_send_sms('order_payment_pending',$order_id);

    }

    public function delivery_man($order_id){
        self::_send_sms('order_delivery_processing',$order_id);
    }

    private function _send_sms($type,$order_id){
        global $wpdb;
        $results = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cron_sms_settings where _type = %s ",$type) );
        $text = $results->_body;
        $_status =  (int)$results->_status;
        if($_status==1){
            $order = new WC_Order( $order_id );
            $user_id = $order->user_id;
            $user = new WP_User( $user_id );
            $name = $user->user_firstname.' '.$user->user_lastname;
             $items = $order->get_items();
            $item_number = count($items);
            if(!empty($items)){
                $product_name = '';
                foreach ( $items as $item ) {
                    $product_name .= ', '.$item['name'];
                }
            }
            $payment_amount = $order->get_formatted_order_total(); // $order->get_total();
            $billing_phone = $order->billing_phone;
            $search_words = array("%user%", "%order-id%","%order_id%", "%item-number%","%payment_amount%","%invoice_id%", "%items_name%");
            $replace_with   = array($name, $order_id, $order_id, $item_number,$payment_amount,$order_id,$product_name );
            $_sms_type =  $order->get_status();    ;
            $message = str_replace($search_words, $replace_with, $text);
            if($billing_phone) {
                $wpdb->insert(
                    $wpdb->prefix . "cron_sms_archive",  // table
                    array(  // column
                        '_sms_type' => $_sms_type
                    , '_sms_body' => $message
                    , '_number' => $billing_phone
                    , '_status' => 0
                    , '_priority' => 'H'
                    , '_sms_user_name' => 'koikinbo'
                    , '_sms_user_pass' => 'kinbo_3211'
                    , '_sms_user_url' => 'http://sms.ecarebd.com'
                    , '_brand' => 'koikinbo'

                    )
                    , array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
                );
            }

        }

        return true;

    }

}