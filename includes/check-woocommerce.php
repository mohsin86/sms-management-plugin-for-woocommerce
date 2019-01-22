<?php
/**
 * Created by PhpStorm.
 * User: mohammed.mohasin
 * Date: 16-Feb-17
 * Time: 6:24 PM
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    /*
     *  Initialize Objec
     */
    $smsSetting = new smsSettings(); // For Menu and Settings

    $sendSms = new sendSMS(); // send sms

    /*
     * WooCommerce plugin activation prompt tracker
     */
    update_option('sms_woocommerce_prompt', 'false');
}else {
    /*
     * WooCommerce plugin activation prompt tracker
     */
    update_option('sms_woocommerce_prompt', 'true');

    if( get_option('sms_woocommerce_prompt') == 'true' )
    {
        /*
         * Show prompt to user
         */
        add_action('admin_notices', 'sms_woocommerce_activate_prompt');
        function sms_woocommerce_activate_prompt()
        {
            echo "<div class='updated'><p>Please activate WooCommerce to use Woocommerce SMS Management Plugin.</p></div>";
        }
    }
}
