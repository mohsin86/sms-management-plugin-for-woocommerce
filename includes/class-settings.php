<?php
class smsSettings
{
    public function __construct()
    {
        register_activation_hook(__FILE__, array(__CLASS__, 'activation'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivation'));


        add_action( 'admin_enqueue_scripts', array(__CLASS__,'load_custom_wp_admin_style' ));
        add_action('admin_menu', array(__CLASS__, 'admin_menu_submenu'));
    }

    // Add plugin menu pages to admin menu
    public function admin_menu_submenu() {
        // Set up the plugin admin menu
        add_submenu_page( 'woocommerce', 'SMS Settings', 'SMS Settings', 'manage_options', 'my_sms_settings_slug', array(__CLASS__,'my_sms_settings_page'));
        add_submenu_page( 'woocommerce', 'SMS Marketing', 'SMS Marketing', 'manage_options', 'my_sms_marketing_slug', array(__CLASS__,'my_sms_marketing_page'));
    }

    public function load_custom_wp_admin_style($hook) {
       //  Load only on ?page=mypluginname
        $ho = $hook;
        echo '<pre>';
        print_r($ho);
        echo '</pre>';
        if(($hook == 'woocommerce_page_my_sms_settings_slug') ||($hook == 'woocommerce_page_my_sms_marketing_slug') )  {



//        if(($hook != 'woocommerce_page_my_sms_settings_slug') ) {
//
//        }elseif($hook != 'woocommerce_page_my_sms_marketing_slug'){
//
//        }else{
//            return;
//        }

        wp_enqueue_style( 'my_plugin_bootstrap','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), '3.3.7', 'all' );
        // js
        wp_deregister_script( 'jquery' );
        wp_register_script('jquery',"//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js", false, null);
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'bootstrap-min-script', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',array ('jquery'  ),'3.3.7',true);

        }else return;

    }

    public function my_sms_settings_page(){
      if(isset($_POST['update'])){
          self::_settings_update();
      }

        if(isset($_POST['reset'])){
            self::_settings_reset();
        }
        self::_settings_list();
    }

    public function my_sms_marketing_page(){


        if(isset($_POST['send_sms'])){
           $status  =  self::_send_marketing_sms();
        }

        self::_sms_marketing_form($status='');
    }

    private function _settings_list(){

        $order_status_processing ='Hello %user%, Your order no:%order-id% has been confirmed. we will inform you when your order is available and ready! '  ;
        $order_payment_complete = 'Hello %user%, You Payment is recieved for Invoice %order_id%';
        $order_delivery_processing = 'Hello %user%, %item-number% from order no:%order_id% is ready and will be delivered in 1 to 3 business days.';
        $new_order = 'Hello %user%, Thanks for purchasing. Your order no:%order_id% ';
        $order_status_failed = 'Hello %user%, Your order no:%order_id% is failed. Please, Placed your order again';
        $order_status_cancelled = 'Hello %user%, Your order no:%order_id% is cencelled';
        $order_status_refunded = 'Hello %user%, Your payment %payment_amount% is refunded for order no:%order_id%';
        $order_status_completed = 'Hello %user%, Your order no:%order_id% is completed. Reply us with a review on http://koikinbo.com/';
        $order_status_pending = 'Hello %user%, Your payment is pending for order no:%order_id%.';
        $order_status_hold = '';
        $order_status_processing_active = 0;
        $order_payment_complete_active = 0;
        $order_delivery_processing_active = 0;
        $order_status_failed_active = 0;
        $order_status_cancelled_active = 0;
        $order_status_refunded_active = 0;
        $order_status_completed_active = 0;
        $new_order_active = 0;
        $order_status_pending_active = 0;
        $order_status_hold = 0;

        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."cron_sms_settings" );
        if(!empty($results)) {
               foreach ($results as $row) {
                    if($row->_type=='order_status_processing'){
                        $order_status_processing =  $row->_body!=''?$row->_body:$order_status_processing;
                        $order_status_processing_active = $row->_status;
                    }
                   if($row->_type=='order_payment_complete'){
                       $order_payment_complete =  $row->_body!=''?$row->_body:$order_payment_complete;
                       $order_payment_complete_active = $row->_status;
                   }
                   if($row->_type=='order_delivery_processing'){
                       $order_delivery_processing =  $row->_body!=''?$row->_body:$order_delivery_processing;
                       $order_delivery_processing_active = $row->_status;
                   }
                   if($row->_type=='order_status_failed'){
                       $order_status_failed =  $row->_body!=''?$row->_body:$order_status_failed;
                       $order_status_failed_active = $row->_status;
                   }
                   if($row->_type=='order_status_cancelled'){
                       $order_status_cancelled =  $row->_body!=''?$row->_body:$order_status_cancelled;
                       $order_status_cancelled_active = $row->_status;
                   }
                   if($row->_type=='order_status_refunded'){
                       $order_status_refunded =  $row->_body!=''?$row->_body:$order_status_refunded;
                       $order_status_refunded_active = $row->_status;
                   }
                   if($row->_type=='order_status_completed'){
                       $order_status_completed =  $row->_body!=''?$row->_body:$order_status_completed;
                       $order_status_completed_active = $row->_status;
                   }
                   if($row->_type=='order_status_hold'){
                       $order_status_hold =  $row->_body!=''?$row->_body:$order_status_hold;
                       $order_status_hold_active = $row->_status;
                   }


                   if($row->_type=='new_order'){
                       $new_order =  $row->_body!=''?$row->_body:$new_order;
                       $new_order_active = $row->_status;
                   }
                   if($row->_type=='order_status_pending'){
                       $order_status_pending =  $row->_body!=''?$row->_body:$new_order;
                       $order_status_pending_active = $row->_status;
                   }

               }
           }


         ?>
        <form class="alldetails" action="<?php echo admin_url('admin.php?page=my_sms_settings_slug'); ?>" method="post">

            <div class="postbox" style='width: 97.5%;float: left;padding: 10px;margin: 14px;'>
                <div class="row">
                  <div class="col-md-12">
                    <h3 class="hndle ui-sortable-handle" style="position: relative;"><span>SMS Settings:

                            <input value="Reset" name="reset" class="button-primary" style="float: right" type="submit">
                            <input value="Update" name="update" class="button-primary" style="float: right; margin-right: 5px;" type="submit">
                         <a class="btn" style="position: absolute; right: 0px; top: -33px;"><span class="glyphicon glyphicon-info-sign" onclick="javascript:return false;"></span></a>
                        </span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Payment Complete -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Payment Complete :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input <?php echo $order_payment_complete_active==1? 'checked="checked"':'';  ?> type="checkbox" value="1"  class="form-control" name="order_payment_complete_active"/>
                                                <textarea  class="form-control" name="order_payment_complete"><?php echo trim($order_payment_complete); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Complete -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Payment Pending :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input <?php echo $order_status_pending_active==1? 'checked="checked"':'';  ?> type="checkbox" value="1"  class="form-control" name="order_status_pending"/>
                                                <textarea  class="form-control" name="order_status_pending"><?php echo trim($order_status_pending); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                              <!-- Order Processing -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order Processing :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_status_processing_active==1? 'checked="checked"':'';  ?>  value="1"  class="form-control" name="order_status_processing_active"/>

                                                <textarea  class="form-control" name="order_status_processing"><?php echo trim($order_status_processing); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Processing -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Delivery Processing :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_delivery_processing_active==1? 'checked="checked"':'';  ?> value="1"   class="form-control" name="order_delivery_processing_active"/>
                                                <textarea  class="form-control" name="order_delivery_processing"><?php echo trim($order_delivery_processing); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Processing -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order on-hold :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_status_hold_active==1? 'checked="checked"':'';  ?> value="1"   class="form-control" name="order_status_hold_active"/>
                                                <textarea  class="form-control" name="order_status_hold"><?php echo trim($order_status_hold); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Order completed -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order Completed :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox"  <?php echo $order_status_completed_active==1? 'checked="checked"':'';  ?> value="1"  class="form-control" name="order_status_completed_active"/>
                                                <textarea  class="form-control" name="order_status_completed"><?php echo trim($order_status_completed); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order refunded -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order Refunded :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_status_refunded_active==1? 'checked="checked"':'';  ?> value="1"  class="form-control" name="order_status_refunded_active"/>
                                                <textarea  class="form-control" name="order_status_refunded"><?php echo trim($order_status_refunded); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Cancelled -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order Cancelled :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_status_cancelled_active==1? 'checked="checked"':'';  ?>  value="1"  class="form-control" name="order_status_cancelled_active"/>
                                                <textarea  class="form-control" name="order_status_cancelled"><?php echo trim($order_status_cancelled); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Cancelled -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">Order Failed :</div>
                                    <div class="panel-body">
                                        <div class="col-md-12 sor">
                                            <div class="form-group">
                                                <label>Is Activate ? :</label>
                                                <input type="checkbox" <?php echo $order_status_failed_active==1? 'checked="checked"':'';  ?> value="1"   class="form-control" name="order_status_failed_active"/>
                                                <textarea  class="form-control" name="order_status_failed"><?php echo trim($order_status_failed); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div> <!--row -->
                    </div> <!--inside -->
                  </div> <!-- col-md-12 -->
              </div> <!--row -->
            </div> <!-- post Box -->

        </form>

        <div id="toottip_html" style="display: none">
            <ul>
                <li><strong>Failed</strong>&nbsp;– Payment failed or was declined (unpaid). Note that this status may not show immediately and instead show as Pending until verified (i.e., PayPal)</li>
                <li><strong>Processing</strong>&nbsp;– Payment received and stock has been reduced – the order is awaiting fulfillment. All product orders require processing.</li>
                <li><strong>Order Completed</strong>&nbsp;– Order fulfilled, payed and complete , delivered – requires no further action.</li>
                <li><strong>On-Hold</strong>&nbsp;– Awaiting payment – stock is reduced, but you need to confirm payment, or the order couldn’t be sent out for delivery </li>
                <li><strong>Cancelled</strong>&nbsp;– Cancelled by an admin or the customer – no further action required</li>
                <li><strong>Refunded</strong>&nbsp;– Refunded by an admin&nbsp;– no further action required</li>

                <li>%user% = customer Name</li>
                <li> %order-id% / %invoice_id% = order or invoid Number</li>
                <li> %item-number% = number of items that one's Bought</li>
                <li> %payment_amount% </li>
                <li> %items_name% = items Name </li>

            </ul>
        </div>
<style>
    .tooltip-inner {
        max-width: 60%;
        /* If max-width does not work, try using width instead */
        width: 60%;
        text-align: left;
        z-index: 9999;
    }
</style>
        <script>

            $(document).ready(function(){
                var toottip_html = $('#toottip_html').html();
                console.log(toottip_html);
                $('.btn').tooltip({
                        title: toottip_html,
                        html: true,
                        placement: "bottom"
                });
            });
        </script>

        <?php
    } // _settings_list

    function _settings_update(){

        $order_status_processing = isset($_POST['order_status_processing'])? $_POST['order_status_processing']:'';
        $order_payment_complete = isset($_POST['order_payment_complete'])? $_POST['order_payment_complete']:'';
        $order_delivery_processing = isset($_POST['order_delivery_processing'])? $_POST['order_delivery_processing']:'';
        $new_order = isset($_POST['new_order'])? $_POST['new_order']:'';
        $order_status_failed = isset($_POST['order_status_failed'])? $_POST['order_status_failed']:'';
        $order_status_cancelled = isset($_POST['order_status_cancelled'])? $_POST['order_status_cancelled']:'';
        $order_status_refunded = isset($_POST['order_status_refunded'])? $_POST['order_status_refunded']:'';
        $order_status_completed = isset($_POST['order_status_completed'])? $_POST['order_status_completed']:'';
        $order_status_pending = isset($_POST['order_status_pending'])? $_POST['order_status_pending']:'';
        $order_status_hold = isset($_POST['order_status_hold'])? $_POST['order_status_hold']:'';


        $order_status_processing_active = isset($_POST['order_status_processing_active'])? $_POST['order_status_processing_active']:0;
        $order_payment_complete_active = isset($_POST['order_payment_complete_active'])? $_POST['order_payment_complete_active']:0;
        $order_delivery_processing_active = isset($_POST['order_delivery_processing_active'])? $_POST['order_delivery_processing_active']:0;
        $order_status_failed_active = isset($_POST['order_status_failed_active'])? $_POST['order_status_failed_active']:0;
        $order_status_cancelled_active =isset($_POST['order_status_cancelled_active'])? $_POST['order_status_cancelled_active']:0;
        $order_status_refunded_active = isset($_POST['order_status_refunded_active'])? $_POST['order_status_refunded_active']:0;
        $order_status_completed_active = isset($_POST['order_status_completed_active'])? $_POST['order_status_completed_active']:0;
        $new_order_active = isset($_POST['new_order'])? $_POST['new_order']:0;
        $order_status_pending_active = isset($_POST['order_status_pending_active'])? $_POST['order_status_pending_active']:0;
        $order_status_hold_active = isset($_POST['order_status_hold_active'])? $_POST['order_status_hold_active']:0;


        $rows = [
            [$order_status_processing, $order_status_processing_active, 'order_status_processing'],
            [$order_payment_complete, $order_payment_complete_active, 'order_payment_complete'],
            [$order_delivery_processing, $order_delivery_processing_active, 'order_delivery_processing'],
            [$new_order, $new_order_active, 'new_order'],
            [$order_status_failed, $order_status_failed_active, 'order_status_failed'],
            [$order_status_cancelled, $order_status_cancelled_active, 'order_status_cancelled'],
            [$order_status_refunded, $order_status_refunded_active, 'order_status_refunded'],
            [$order_status_completed, $order_status_completed_active, 'order_status_completed'],
            [ $order_status_pending,  $order_status_pending_active, 'order_status_pending'],
            [ $order_status_hold,  $order_status_hold_active, 'order_status_hold'],


        ];

        global $wpdb;
//        echo '<pre>';
//        echo $wpdb->prefix."cron_sms_settings";

        foreach ($rows as $row){
            $wpdb->update(
                $wpdb->prefix."cron_sms_settings",  // table
                array(  // column
                    '_body' => $row[0], '_status' => (int)$row[1]

                )
                , array( '_type' => $row[2] ) //where
                , array( '%s', '%d' ), array( '%s' )
            );



        }

        return true;
    } // _settings_update
    function _settings_reset(){
        $order_status_processing ='Hello %user%, Your order no:%order-id% has been confirmed. we will inform you when your order is available and ready! '  ;
        $order_payment_complete = 'Hello %user%, You Payment is recieved for Invoice %order_id%';
        $order_delivery_processing = 'Hello %user%, %item-number% from order no:%order_id% is ready and will be delivered in 1 to 3 business days.';
        $new_order = 'Hello %user%, Thanks for purchasing. Your order no:%order_id% ';
        $order_status_failed = 'Hello %user%, Your order no:%order_id% is failed. Please, Placed your order again';
        $order_status_cancelled = 'Hello %user%, Your order no:%order_id% is cencelled';
        $order_status_refunded = 'Hello %user%, Your payment %payment_amount% is refunded for order no:%order_id%';
        $order_status_completed = 'Hello %user%, Your order no:%order_id% is completed. Reply us with a review on http://koikinbo.com/';
        $order_status_pending = 'Hello %user%, Your payment is pending fororder no:%order_id%.';
        $order_status_hold = '';

        $order_status_processing_active = 0;
        $order_payment_complete_active = 0;
        $order_delivery_processing_active = 0;
        $order_status_failed_active = 0;
        $order_status_cancelled_active = 0;
        $order_status_refunded_active = 0;
        $order_status_completed_active = 0;
        $new_order_active = 0;
        $order_status_pending_active = 1;
        $order_status_hold_active = 0;

        $rows = [
            [$order_status_processing, $order_status_processing_active, 'order_status_processing'],
            [$order_payment_complete, $order_payment_complete_active, 'order_payment_complete'],
            [$order_delivery_processing, $order_delivery_processing_active, 'order_delivery_processing'],
            [$new_order, $new_order_active, 'new_order'],
            [$order_status_failed, $order_status_failed_active, 'order_status_failed'],
            [$order_status_cancelled, $order_status_cancelled_active, 'order_status_cancelled'],
            [$order_status_refunded, $order_status_refunded_active, 'order_status_refunded'],
            [$order_status_completed, $order_status_completed_active, 'order_status_completed'],
            [$order_status_hold, $order_status_hold_active, 'order_status_hold'],
        ];

        global $wpdb;

        foreach ($rows as $row){
            $wpdb->update(
                $wpdb->prefix."cron_sms_settings",  // table
                array(  // column
                    '_body' => $row[0], '_status' => (int)$row[1]

                )
                , array( '_type' => $row[2] ) //where
                , array( '%s', '%d' ), array( '%s' )
            );



        }

        return true;
    }
    private function wp_roles_array() {
        $editable_roles = get_editable_roles();
        foreach ($editable_roles as $role => $details) {
            $sub['role'] = esc_attr($role);
            $sub['name'] = translate_user_role($details['name']);
            $roles[] = $sub;
        }
        return $roles;
    }
    private function role_exists( $role ) {

          if( ! empty( $role ) ) {
                return $GLOBALS['wp_roles']->is_role( $role );
              }

              return false;
    }
    private function _sms_marketing_form($status){
        ?>
        <form class="alldetails" action="<?php echo admin_url('admin.php?page=my_sms_marketing_slug'); ?>" method="post">
             <div class="postbox" style='width: 97.5%;float: left;padding: 10px;margin: 14px;'>
                <div class="row">
                  <div class="col-md-12">
                    <h3 class="hndle ui-sortable-handle" style="position: relative;"><span>SMS MARKETING:

                        </span></h3>
                    <div class="inside">
                        <div class="row">
                                        <div class="col-md-12">
                                                    <?php

                                                    if($status){?>
                                                            <div class="alert alert-success fade in">
                                                                <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                                <strong>Success!</strong> <?php echo $status; ?>
                                                            </div>

                                                    <?php

                                                    }
                                                    ?>
                                                <div class="form-group">
                                                <?php
                                                     $roles = self::wp_roles_array();
                                                ?>
                                                   <label>To : </label>
                                                   <select name="to" id="input-to" class="form-control" style="width: 237px;" required>
                                                        <option value="">---------</option>
                                                        <?php
                                                        if(!empty($roles)){
                                                          foreach($roles as $role){
                                                            echo '<option value="'.$role['role'].'">'.$role['name'].' </option>';
                                                          }
                                                        }
                                                        ?>
                                                  </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 ">
                                                  <div class="form-group">
                                                        <label style="vertical-align: top;">Message :</label>
                                                        <textarea  class="form-control" rows="5" cols="30" name="message" required></textarea>
                                                   </div>
                                            </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                        <input value="send" name="send_sms" class="button-primary" style="" type="submit">
                                                    </div>
                                            </div>
                        </div> <!--row -->
                    </div> <!--inside -->
                  </div> <!-- col-md-12 -->
              </div> <!--row -->
            </div> <!-- post Box -->

        </form>
<?php
    } // sms Form
    private function _send_marketing_sms(){
         global $wpdb;
         $to = isset($_POST['to'])? $_POST['to']:'';
         $message = isset($_POST['message'])? $_POST['message']:'';
         $role = self::role_exists($to);
         if($role){
            $users = get_users( 'role='.$to );
            // Array of WP_User objects.
            foreach ( $users as $user ) {
                $user_id=  $user->ID;
                $name = $user->data->display_name;
                $phone = get_the_author_meta( 'phone', $user_id );
                if($phone) {
                    $wpdb->insert(
                        $wpdb->prefix . "cron_sms_archive",  // table
                        array(  // column
                            '_sms_type' => 'Marketing'
                        , '_sms_body' => $message
                        , '_number' => $phone
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
         }

        return 'SMS Send;';

    }
    public function activation(){
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'liveshoutbox';
        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          name tinytext NOT NULL,
          text text NOT NULL,
          url varchar(55) DEFAULT '' NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
    }

    public function deactivation(){

    }

}












