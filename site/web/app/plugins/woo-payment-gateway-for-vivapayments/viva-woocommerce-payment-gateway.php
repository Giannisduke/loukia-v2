<?php

/*
Plugin Name: Viva Payments - Viva Wallet WooCommerce Payment Gateway
Plugin URI: https://www.papaki.com
Description: Viva Payments - Viva Wallet Payment Gateway allows you to accept payment through various channels such as Maestro, Mastercard, AMex cards, Diners  and Visa cards On your Woocommerce Powered Site.
Version: 1.3.9
Author: Papaki
Author URI: https://www.papaki.com
License:           GPL-3.0+
License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
WC tested up to: 4.6.1
Text Domain: woo-payment-gateway-for-vivapayments
Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'encryption.php';
add_action('plugins_loaded', 'woocommerce_vivapay_init', 0);

function woocommerce_vivapay_init()
{

    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    load_plugin_textdomain('woo-payment-gateway-for-vivapayments', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    /**
     * Gateway class
     */
    class WC_Papaki_Vivapayments_Gateway extends WC_Payment_Gateway
    {

        public function __construct()
        {
            global $woocommerce;

            $this->id = 'papaki_vivapayments_gateway';
            //$this->icon = apply_filters('woocommerce_vivaway_icon', plugins_url('img/pay-via-vivapay.png', __FILE__));
            //$this->icon = apply_filters('woocommerce_vivaway_icon', plugins_url('img/viva_wallet.svg', __FILE__));

            $this->has_fields = false;
            $this->liveurl = 'https://www.vivapayments.com/api/';
            $this->testurl = 'https://demo.vivapayments.com/api/';
            $this->notify_url = WC()->api_request_url('WC_Papaki_Vivapayments_Gateway');
            $this->method_title = 'VivaPayments Gateway';
            $this->method_description = __('VivaWallet Payment Gateway allows you to accept payment through various channels such as Maestro, Mastercard, AMex cards, Diners  and Visa cards On your Woocommerce Powered Site.', 'woo-payment-gateway-for-vivapayments');

            $this->redirect_page_id = $this->get_option('redirect_page_id');
            // Load the form fields.
            $this->init_form_fields();

            //dhmioyrgia vashs

            global $wpdb;

            if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "viva_payment_transactions'") === $wpdb->prefix . 'viva_payment_transactions') {
                // The database table exist
            } else {
                // Table does not exist
                $query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'viva_payment_transactions (id int(11) unsigned NOT NULL AUTO_INCREMENT, ref varchar(100) not null, trans_code varchar(255) not null,  orderid varchar(100) not null , timestamp datetime default null, PRIMARY KEY (id))';
                $wpdb->query($query);
            }

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->title = sanitize_text_field($this->get_option('title'));
            $this->description = sanitize_text_field($this->get_option('description'));
            $this->vivaPayMerchantId = sanitize_text_field($this->get_option('vivaPayMerchantId'));
            $this->vivaPayAPIKey = sanitize_text_field($this->get_option('vivaPayAPIKey'));
            $this->vivaPayCodeId = sanitize_text_field($this->get_option('vivaPayCodeId'));
            $this->customerMessage = sanitize_text_field($this->get_option('customerMessage'));
            $this->mode = sanitize_text_field($this->get_option('mode'));
            $this->allowedInstallments = absint($this->get_option('installments'));
            $this->installments_variation = sanitize_text_field($this->get_option('installments_variation'));
            $this->viva_render_logo = sanitize_text_field($this->get_option('viva_render_logo'));
            // $this->tags = $this->get_option('tags');
            //Actions
            add_action('woocommerce_receipt_papaki_vivapayments_gateway', array($this, 'receipt_page'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            // Payment listener/API hook
            add_action('woocommerce_api_wc_papaki_vivapayments_gateway', array($this, 'check_vivapayments_response'));

            if($this->viva_render_logo == "yes") {
                $this->icon = apply_filters('woocommerce_vivaway_icon', plugins_url('img/viva_wallet.svg', __FILE__));
            }
        }

        /**
         * Admin Panel Options
         * */
        public function admin_options()
        {
            echo '<h3>' . __('VivaWallet Payment Gateway', 'woo-payment-gateway-for-vivapayments') . '</h3>';
            echo '<p>' . __('VivaWallet Payment Gateway allows you to accept payment through various channels such as Maestro, Mastercard, AMex cards, Diners and Visa cards.', 'woo-payment-gateway-for-vivapayments') . '</p>';

            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        }

        /**
         * Initialise Gateway Settings Form Fields
         * */
        function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'checkbox',
                    'label' => __('Enable VivaWallet Payment Gateway', 'woo-payment-gateway-for-vivapayments'),
                    'description' => __('Enable or disable the gateway.', 'woo-payment-gateway-for-vivapayments'),
                    'desc_tip' => true,
                    'default' => 'yes',
                ),
                'title' => array(
                    'title' => __('Title', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woo-payment-gateway-for-vivapayments'),
                    'desc_tip' => false,
                    'default' => __('Credit card via VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                ),
                'description' => array(
                    'title' => __('Description', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'woo-payment-gateway-for-vivapayments'),
                    'default' => __('Pay Via VivaWallet: Accepts  Mastercard, Visa cards and etc.', 'woo-payment-gateway-for-vivapayments'),
                ),
                'viva_render_logo' => array(
                    'title' => __('Display the logo of VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'checkbox',
                    'description' => __('Enable to display the logo of VivaWallet next to the title which the user sees during checkout.', 'woo-payment-gateway-for-vivapayments'),
                    'default' => 'yes'
                ),
                'vivaPayMerchantId' => array(
                    'title' => __('VivaWallet Merchant ID', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'text',
                    'description' => __('Enter Your VivaWallet Merchant ID, this can be gotten on your account page when you login on VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'vivaPayAPIKey' => array(
                    'title' => __('VivaWallet API key', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'password',
                    'description' => __('Enter Your VivaWallet API key, this can be gotten on your account page when you login on VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'vivaPayCodeId' => array(
                    'title' => __('VivaPayments CodeId', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'text',
                    'description' => __('Enter Your VivaWallet CodeId, or use "default" , this can be gotten on your account page when you login on VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                    'default' => '',
                    'desc_tip' => true,
                ), 'customerMessage' => array(
                    'title' => __('Message to Customer', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'text',
                    'description' => __('Enter a message that will be shown to the customer at the payment receipt from VivaWallet', 'woo-payment-gateway-for-vivapayments'),
                    'default' => '',
                    'desc_tip' => true,
                ), 'mode' => array(
                    'title' => __('Mode', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'checkbox',
                    'label' => __('Enable Test Mode', 'woo-payment-gateway-for-vivapayments'),
                    'default' => 'yes',
                    'description' => __('This controls  the payment mode as TEST or LIVE.', 'woo-payment-gateway-for-vivapayments'),
                ),
                'redirect_page_id' => array(
                    'title' => __('Return page URL <br />(Successful or Failed Transactions)', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'select',
                    'options' => $this->papaki_get_pages('Select Page'),
                    'description' => __('We recommend you to select the default “Thank You Page”, in order to automatically serve both successful and failed transactions, with the latter also offering the option to try the payment again.<br /> If you select a different page, you will have to handle failed payments yourself by adding custom code.', 'woo-payment-gateway-for-vivapayments'),
                    'default' => "-1"
                ),
                'installments' => array(
                    'title' => __('Installments', 'woo-payment-gateway-for-vivapayments'),
                    'type' => 'select',
                    'options' => $this->papaki_get_installments('Select Installments'),
                    'description' => __('1 to 36 Installments,1 for one time payment</br> Your customers will be able to choose as many installments as they want, regardless of the total amount of their order.', 'woo-payment-gateway-for-vivapayments'),
                ),
                'installments_variation' => array(
                    'title' => __('Number of installments depending on the total order amount', 'woo-payment-gateway-for-vivapayments'),
                    'type'  => 'text',
                    'description' => __('Example: 150:3, 600:6</br> Order total 150 -> allow 3 installments, order total 600 -> allow 6 installments</br> Leave the field blank if you do not want to limit the number of installments depending on the amount of the order.', 'woo-payment-gateway-for-vivapayments')
                )
                /*,
                'tags' => array(
                    'title' => __('Tags', 'woo-payment-gateway-for-vivapayments'),
                    'type'  => 'text',
                    'description' => __('Tags')
                )*/
            );
        }

        public function get_option($key, $empty_value = null)
        {
            $option_value = parent::get_option($key, $empty_value);
            if ($key == 'vivaPayAPIKey') {
                $decrypted = WC_Payment_Gateway_KeyEncryption_Viva::decrypt(base64_decode($option_value), substr(NONCE_KEY, 0, 32));
                $option_value = stripslashes($decrypted);
            }
            return $option_value;
        }

        public function validate_vivaPayAPIKey_field($key, $value)
        {
            $encrypted = WC_Payment_Gateway_KeyEncryption_Viva::encrypt($value, substr(NONCE_KEY, 0, 32));
            return base64_encode($encrypted);
        }

        function papaki_get_pages($title = false, $indent = true)
        {
            $wp_pages = get_pages('sort_column=menu_order');
            $page_list = array();
            if ($title) {
                $page_list[] = $title;
            }

            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while ($has_parent) {
                        $prefix .= ' - ';
                        $next_page = get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            $page_list[-1] = __('Thank you page', 'woo-payment-gateway-for-vivapayments');
            return $page_list;
        }

        function papaki_get_installments($title = false, $indent = true)
        {

            for ($i = 1; $i <= 36; $i++) {
                $installment_list[$i] = $i;
            }
            return $installment_list;
        }

        /**
         * Generate the VivaPay Payment button link
         * */
        function generate_vivapayments_form($order_id)
        {
            global $woocommerce;

            $order = new WC_Order($order_id);

            //select demo or live
            if ($this->mode == "yes") {
                $requesturl = 'https://demo.vivapayments.com'; // demo environment URL
            } else {
                $requesturl = 'https://www.vivapayments.com';
            }

            $request = $requesturl . '/api/orders';

            $MerchantId = $this->vivaPayMerchantId;
            $APIKey = $this->vivaPayAPIKey;
            $srccode = $this->vivaPayCodeId;

            //Set the Payment Amount
            $Amount = $order->get_total() * 100; // Amount in cents

            $MaxInstallments = $this->allowedInstallments;
            $installments_variation = $this->installments_variation;


            $IsPreAuth = false;

            // variation for installments
            if(!empty($installments_variation)){
                $MaxInstallments = 1; // initialize the max installments
                if(isset($installments_variation) && !empty($installments_variation)) {
                    $instalments_split = explode(',', $installments_variation);
                    foreach($instalments_split as $key => $value) {
                        $installment = explode(':', $value);
                        if((is_array($installment) && count($installment) != 2)){
                            continue;
                        }
                        if( !is_numeric($installment[0]) || !is_numeric($installment[1])) {
                            continue;
                        }
    
                        if($Amount >= ($installment[0]*100)) {
                            $MaxInstallments = $installment[1];
                        }
                    }
                }
    
                // check for max installments -- currently are 36
                if($MaxInstallments > 36) {
                    $MaxInstallments = 36;
                }
            }

            //Set some optional parameters (Full list available here: https://github.com/VivaPayments/API/wiki/Optional-Parameters)
            $AllowRecurring = 'false'; // This flag will prompt the customer to accept recurring payments in tbe future.

            //Detect wp locale and adjust redirect page's language
            $locale = get_locale();
            if($locale == 'el') {
                $RequestLang = 'el-GR';
            } else {
                $RequestLang = 'en-US';
            }

            $currency_symbol ='';
            $currency_code = get_woocommerce_currency();
            switch ($currency_code) {
            case 'EUR':
               $currency_symbol = 978;
               break;
            case 'GBP':
               $currency_symbol = 826;
               break;
            case 'BGN':
               $currency_symbol = 975;
               break;
            case 'RON':
               $currency_symbol = 946;
               break;
            default:
            $currency_symbol = 978;
            }

            $MerchantTrns = 'Vivapayments ' . $order_id;
            $CustomerTrns = $this->customerMessage;

            // $tags = serialize(explode(',', $this->tags));

            $postargs = 'Amount=' . urlencode($Amount) . '&AllowRecurring=' . $AllowRecurring . '&RequestLang=' . $RequestLang . '&SourceCode=' . $srccode . '&DisableIVR=true&MaxInstallments=' . $MaxInstallments . '&MerchantTrns=' . $MerchantTrns . '&CustomerTrns=' . $CustomerTrns . '&IsPreAuth=' . $IsPreAuth;
                // . '&Tags=' . $tags; //     . '&CurrencyCode=' . $currency_symbol;
            
                // Get the curl session object
            $session = curl_init($request);
            
            // Set the POST options.
            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_USERPWD, htmlspecialchars_decode($MerchantId) . ':' . htmlspecialchars_decode($APIKey));
            
            // Do the POST and then close the session
            $response = curl_exec($session);
            $error = curl_error($session);
            curl_close($session);

            // Parse the JSON response
            try {
                if (is_object(json_decode($response))) {
                    $resultObj = json_decode($response);
                } else {
                    $order->add_order_note(__("Wrong Merchant crendentials or API unavailable.", 'woo-payment-gateway-for-vivapayments') . print_r(array($error, $response), 1));

                    return __("Wrong Merchant crendentials or API unavailable", 'woo-payment-gateway-for-vivapayments');
                }
            } catch (Exception $e) {
                //  echo $e->getMessage();
            }

            global $wpdb;
            if ($resultObj->ErrorCode == 0) { //success when ErrorCode = 0
                $transId = $resultObj->OrderCode;

                if (!is_null($transId)) {

                    $wpdb->delete($wpdb->prefix . 'viva_payment_transactions', array('orderid' => $order_id));
                    $wpdb->insert($wpdb->prefix . 'viva_payment_transactions', array('trans_code' => $transId, 'orderid' => $order_id, 'timestamp' => current_time('mysql', 1)));

                    wc_enqueue_js('
				$.blockUI({
						message: "' . esc_js(__('Thank you for your order. We are now redirecting you to VivaWallet to make payment.', 'woo-payment-gateway-for-vivapayments')) . '",
						baseZ: 99999,
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
							padding:        "20px",
							zindex:         "9999999",
							textAlign:      "center",
							color:          "#555",
							border:         "3px solid #aaa",
							backgroundColor:"#fff",
							cursor:         "wait",
							lineHeight:		"24px",
						}
					});
				jQuery("#submit_vivapayments_payment_form").click();
			');
                    return '<form action="' . $requesturl . '/web/checkout?ref=' . $transId . '" method="post" id="vivapayments_payment_form" target="_top">

					<!-- Button Fallback -->
					<div class="payment_buttons">
						<input type="submit" class="button alt" id="submit_vivapayments_payment_form" value="' . __('Pay via VivaWallet', 'woo-payment-gateway-for-vivapayments') . '" /> <a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', 'woo-payment-gateway-for-vivapayments') . '</a>
					</div>
					<script type="text/javascript">
						jQuery(".payment_buttons").hide();
					</script>
				</form>';
                } else {
                    return __('Wrong Merchant Credentials or SourceID', 'woo-payment-gateway-for-vivapayments');
                }
            } else {

                return __('The following error occured: ', 'woo-payment-gateway-for-vivapayments') . $resultObj->ErrorText;
            }
        }

        /**
         * Process the payment and return the result
         * */
        /**/
        function process_payment($order_id)
        {

            $order = new WC_Order($order_id);
            $current_version = get_option( 'woocommerce_version', null );
            if (version_compare( $current_version, '2.2.0', '<' )) { //older version
                return array('result' => 'success', 'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay')))));
            } else if (version_compare( $current_version, '2.4.0', '<' )) { //older version
                return array
                    (
                        'result' => 'success',
                        'redirect'	=> add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
                    );
            } else if (version_compare( $current_version, '3.0.0', '<' )) { //older version
                return array
                    (
                        'result' => 'success',
                        'redirect'	=> add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, wc_get_page_permalink( 'checkout' )))
                    );
            } else {
                return array('result' => 'success',
                'redirect' => add_query_arg('order-pay', $order->get_id(), add_query_arg('key', $order->get_order_key(), wc_get_page_permalink('checkout'))));
            }

            return array('result' => 'success',
            'redirect' => add_query_arg('order-pay', $order->get_id(), add_query_arg('key', $order->get_order_key(), wc_get_page_permalink('checkout'))));

            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url(true),
            );
        }

        /**
         * Output for the order received page.
         * */
        function receipt_page($order)
        {
            echo '<p>' . __('Thank you - your order is now pending payment. You should be automatically redirected to Vivapayments to make payment.', 'woo-payment-gateway-for-vivapayments') . '</p>';
            echo $this->generate_vivapayments_form($order);
        }

        /**
         * Verify a successful Payment!
         * */
        function check_vivapayments_response()
        {

            global $woocommerce;
            global $wpdb;

            if (isset($_POST['df'])) {

                /*
             * Just an empty isset Don't know why its needed
             */
            } else {

                $trans_id = sanitize_text_field($_GET['t']);
                $vivaid = sanitize_text_field($_GET['s']);

                if ($this->mode == "yes") {
                    $requesturl = 'https://demo.vivapayments.com/api';
                } else {
                    $requesturl = 'https://www.vivapayments.com/api';
                }

                $postargs = 'ordercode=' . $vivaid;
                $request = $requesturl . '/transactions?' . $postargs;

                $MerchantId = $this->vivaPayMerchantId;
                $APIKey = $this->vivaPayAPIKey;
                // Get the curl session object
                $session = curl_init($request);
                curl_setopt($session, CURLOPT_HEADER, false);
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($session, CURLOPT_USERPWD, htmlspecialchars_decode($MerchantId) . ':' . htmlspecialchars_decode($APIKey));
                // Do the POST and then close the session
                $response = curl_exec($session);
                curl_close($session);

                try {
                    if (is_object(json_decode($response))) {
                        $resultObj = json_decode($response);
                    } else {
                        return __("Wrong Merchant credentials or API unavailable", 'woo-payment-gateway-for-vivapayments');
                    }
                } catch (Exception $e) {
                    // echo $e->getMessage();
                }

                if ($resultObj->ErrorCode == 0) {
                    $orderquery = "SELECT *
			FROM " . $wpdb->prefix . "viva_payment_transactions
			WHERE `trans_code` = " . $vivaid . "	;";

                    $order = $wpdb->get_results($orderquery);

                    $orderid = sanitize_text_field($order[0]->orderid);
                    $order = new WC_Order($orderid);

                    $status = sanitize_text_field($resultObj->Transactions[0]->StatusId);

                    if (isset($status)) {

                        if ($status == "F") {

                            if ($order->get_status() == 'processing') {

                                $order->add_order_note(__('Payment Via VivaWallet<br />Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id);

                                //Add customer order note
                                $order->add_order_note(__('Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br />VivaWallet Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id, 1);

                                // Reduce stock levels
                                $order->reduce_order_stock();

                                // Empty cart
                                WC()->cart->empty_cart();

                                $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'woo-payment-gateway-for-vivapayments');
                                $message_type = 'success';
                                $vivapayments_message = array(
                                    'message' => $message,
                                    'message_type' => $message_type,
                                );

                                $this->generic_add_meta($orderid, '_papaki_vivapayments_message', $vivapayments_message);
                            } else {
                                if ($order->has_downloadable_item()) {

                                    // check if the order has only downloadable items
                                    $hasOnlyDownloadable = true; 
                                    foreach ($order->get_items() as $key => $order_prod) {
                                        $p = $order_prod->get_product();
                                        if($p->is_downloadable() == false && $p->is_virtual() == false) {
                                            $hasOnlyDownloadable = false; 
                                        }
                                      }

                                    if($hasOnlyDownloadable) {
                                        //Update order status
                                        $order->update_status('completed', __('Payment received, your order is now complete.', 'woo-payment-gateway-for-vivapayments'));

                                        //Add admin order note
                                        $order->add_order_note(__('Payment Via VivaWallet Payment Gateway<br />Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id);

                                        //Add customer order note
                                        $order->add_order_note(__('Payment Received.<br />Your order is now complete.<br />VivaWallet Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id, 1);

                                        $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is now complete.', 'woo-payment-gateway-for-vivapayments');
                                        $message_type = 'success';
                                    } else {
                                        //Update order status
                                        $order->update_status('processing', __('Payment received, your order is currently being processed.', 'woo-payment-gateway-for-vivapayments'));

                                        //Add admin order noote
                                        $order->add_order_note(__('Payment Via VivaWallet Payment Gateway<br />Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id);

                                        //Add customer order note
                                        $order->add_order_note(__('Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br />VivaWallet Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id, 1);

                                        $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'woo-payment-gateway-for-vivapayments');
                                        $message_type = 'success';
                                    }
                                } else {

                                    //Update order status
                                    $order->update_status('processing', __('Payment received, your order is currently being processed.', 'woo-payment-gateway-for-vivapayments'));

                                    //Add admin order noote
                                    $order->add_order_note(__('Payment Via VivaWallet Payment Gateway<br />Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id);

                                    //Add customer order note
                                    $order->add_order_note(__('Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br />VivaWallet Transaction ID: ', 'woo-payment-gateway-for-vivapayments') . $trans_id, 1);

                                    $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'woo-payment-gateway-for-vivapayments');
                                    $message_type = 'success';
                                }

                                $vivapayments_message = array(
                                    'message' => $message,
                                    'message_type' => $message_type,
                                );

                                $this->generic_add_meta($orderid, '_papaki_vivapayments_message', $vivapayments_message);
                                // Reduce stock levels
                                $order->reduce_order_stock();

                                // Empty cart
                                WC()->cart->empty_cart();
                            }
                        } else {

                            $message = __('Thank you for shopping with us. <br />However, the transaction wasn\'t successful, payment wasn\'t received.', 'woo-payment-gateway-for-vivapayments');
                            $message_type = 'error';

                            //$transaction_id = $transaction['transaction_id'];

                            //Add Customer Order Note
                            $order->add_order_note($message . '<br />VivaWallet Transaction ID: ' . $trans_id, 1);

                            //Add Admin Order Note
                            $order->add_order_note($message . '<br />VivaWallet Transaction ID: ' . $trans_id);

                            //Update the order status
                            $order->update_status('failed', '');

                            $vivapayments_message = array(
                                'message' => $message,
                                'message_type' => $message_type,
                            );

                            $this->generic_add_meta($orderid, '_papaki_vivapayments_message', $vivapayments_message);
                            // wc_add_notice($message, $message_type);
                           // return;

                        }
                    } else {

                        $message = __('Thank you for shopping with us. <br />However, the transaction wasn\'t successful, payment wasn\'t received.', 'woo-payment-gateway-for-vivapayments');
                        $message_type = 'error';

                        $tm_ref = sanitize_text_field($_GET['s']);

                        $check_query = $wpdb->get_results("SELECT orderid FROM {$wpdb->prefix}viva_payment_transactions WHERE trans_code = '".addslashes($tm_ref)."'", ARRAY_A);
                        $check_query_count = count($check_query);

                        if($check_query_count >= 1){
                            $orderid = $check_query[0]['orderid'];
                            $order = new WC_Order($orderid);
                            $order->update_status('failed', '');

                            $order->add_order_note($message . '<br />Vivapay Transaction ID: ' . $tm_ref, 1);

                            //Add Admin Order Note
                            $order->add_order_note($message . '<br />Vivapay Transaction ID: ' . $tm_ref);

                            $vivapayments_message = array(
                                'message' => $message,
                                'message_type' => $message_type,
                            );

                            $this->generic_add_meta($orderid, '_papaki_vivapayments_message', $vivapayments_message);
                        }
                    }
                }

                if ($this->redirect_page_id == "-1") {
                    $redirect_url = $this->get_return_url($order);
                } else {
                    $redirect_url = ($this->redirect_page_id == "" || $this->redirect_page_id == 0) ? get_site_url() . "/" : get_permalink($this->redirect_page_id);
                    //For wooCoomerce 2.0
                    $redirect_url = add_query_arg(array('msg' => urlencode($this->msg['message']), 'type' => $this->msg['class']), $redirect_url);
                }
                wp_redirect($redirect_url);

                exit;
            }
        }

        function generic_add_meta($orderid, $key, $value)
        {
            $order = new WC_Order($orderid);
            if (method_exists($order, 'add_meta_data') && method_exists($order, 'save_meta_data')) {
                $order->add_meta_data($key, $value, true);
                $order->save_meta_data();
            } else {
                update_post_meta($orderid, $key, $value);
            }
        }

    }

    function papaki_vivapayments_message()
    {
        $order_id = absint(get_query_var('order-received'));
        $order = new WC_Order($order_id);
        if (method_exists($order, 'get_payment_method')) {
            $payment_method = $order->get_payment_method();
        } else {
            $payment_method = $order->payment_method;
        }

        if (is_order_received_page() && ('papaki_vivapayments_gateway' == $payment_method)) {

            $vivapayments_message = ''; //get_post_meta($order_id, '_papaki_vivapayments_message', true);
            if (method_exists($order, 'get_meta')) {
                $vivapayments_message = $order->get_meta('_papaki_vivapayments_message', true);
            } else {
                $vivapayments_message = get_post_meta($order_id, '_papaki_vivapayments_message', true);
            }
            if (!empty($vivapayments_message)) {
                $message = $vivapayments_message['message'];
                $message_type = $vivapayments_message['message_type'];

                //delete_post_meta($order_id, '_papaki_vivapayments_message');
                if (method_exists($order, 'delete_meta_data')) {
                    $order->delete_meta_data('_papaki_vivapayments_message');
                    $order->save_meta_data();
                } else {
                    delete_post_meta($order_id, '_papaki_vivapayments_message');
                }

                wc_add_notice($message, $message_type);
            }
        }
    }

    add_action('wp', 'papaki_vivapayments_message');

    /**
     * Add Vivapay Gateway to WC
     * */
    function woocommerce_add_vivapayments_gateway($methods)
    {
        $methods[] = 'WC_Papaki_Vivapayments_Gateway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_vivapayments_gateway');

    /**
     * Add Settings link to the plugin entry in the plugins menu for WC below 2.1
     * */
    if (version_compare(WOOCOMMERCE_VERSION, "2.1") <= 0) {

        add_filter('plugin_action_links', 'papaki_vivapayments_plugin_action_links', 10, 2);

        function papaki_vivapayments_plugin_action_links($links, $file)
        {
            static $this_plugin;

            if (!$this_plugin) {
                $this_plugin = plugin_basename(__FILE__);
            }

            if ($file == $this_plugin) {
                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Papaki_Vivapayments_Gateway">Settings</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

    }
    /**
     * Add Settings link to the plugin entry in the plugins menu for WC 2.1 and above
     * */else {
        add_filter('plugin_action_links', 'papaki_vivapayments_plugin_action_links', 10, 2);

        function papaki_vivapayments_plugin_action_links($links, $file)
        {
            static $this_plugin;

            if (!$this_plugin) {
                $this_plugin = plugin_basename(__FILE__);
            }

            if ($file == $this_plugin) {
                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=wc_papaki_vivapayments_gateway">Settings</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

    }
}
