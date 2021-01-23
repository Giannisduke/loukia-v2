<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


/**
 * Class WC_Vivawallet_Payment_Gateway
 */
class WC_Vivawallet_Payment_Gateway extends WC_Payment_Gateway {

	/**
	 * Client id
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Client secret
	 *
	 * @var string
	 */
	private $client_secret;


	/**
	 * Source code
	 *
	 * @var string
	 */
	private $source_code;

	/**
	 * Test mode
	 *
	 * @var string
	 */
	private $test_mode;

	/**
	 * Test client id
	 *
	 * @var string
	 */
	private $test_client_id;

	/**
	 * Test client secret
	 *
	 * @var string
	 */
	private $test_client_secret;

	/**
	 * Test source code
	 *
	 * @var string
	 */
	private $test_source_code;

	/**
	 * Credentials
	 *
	 * @var array
	 */
	private $credentials;


	/**
	 * Demo source list
	 *
	 * @var object
	 */
	private $demo_source_list;

	/**
	 * Live source list
	 *
	 * @var object
	 */
	private $live_source_list;

	/**
	 * Capture immediately
	 *
	 * @var string
	 */
	private $capture_immediately;

	/**
	 * Order status
	 *
	 * @var string
	 */
	private $order_status;


	/**
	 * Instalments
	 *
	 * @var string
	 */
	private $instalments;



	/**
	 * WC_Vivawallet_Payment_Gateway constructor.
	 */
	public function __construct() {

		$this->id                 = 'vivawallet_native';
		$this->method_title       = __( 'Viva Wallet for WooCommerce', 'woocommerce_vivawallet' );
		$this->method_description = __( 'Sign up for a demo account to test the API. Cards accepted: Visa, MasterCard, Maestro, Amex and more. ', 'woocommerce_vivawallet' );

		$this->icon       = apply_filters( 'woocommerce_vivawallet_icon', WC_Vivawallet_Helper::VW_CHECKOUT_PAYMENT_LOGOS_URL );
		$this->has_fields = true;

		$this->supports = array(
			'products',
			'refunds',
			// 'default_credit_card_form',
			'tokenization',
			// 'credit_card_form_cvc_on_saved_method'
		);

		$this->init_settings();

		$this->client_id          = $this->get_option( 'client_id' );
		$this->client_secret      = $this->get_option( 'client_secret' );
		$this->test_client_id     = $this->get_option( 'test_client_id' );
		$this->test_client_secret = $this->get_option( 'test_client_secret' );
		$this->source_code        = $this->get_option( 'source_code' );
		$this->test_source_code   = $this->get_option( 'test_source_code' );

		$this->capture_immediately = $this->get_option( 'capture_immediately' );
		$this->order_status        = $this->get_option( 'order_status' );
		$this->instalments         = $this->get_option( 'instalments' );

		$this->init_form_fields();

		$this->credentials = $this->set_credentials();

		$this->test_mode   = $this->get_option( 'test_mode' );
		$this->title       = $this->get_option( 'title' );
		$this->enabled     = $this->get_option( 'enabled' );
		$this->description = $this->get_option( 'description' );

		set_transient( 'admin-vw-notice-transient', true, 0 );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action( 'woocommerce_order_edit_status', array( $this, 'process_refund' ), 99, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

		add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'viva_payments_credit_card_fields' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_and_styles' ) );

		// Setting a custom timeout value for cURL. Using a high value for priority to ensure the function runs after any other added to the same action hook.
		add_action( 'http_api_curl', array( $this, 'sar_custom_curl_timeout' ), 9999, 1 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 99, 0 );

		add_action( 'woocommerce_settings_start', array( $this, 'admin_settings_start' ) );

	}

	/**
	 * Sar_custom_curl_timeout
	 *
	 * @param string $handle handle.
	 */
	public function sar_custom_curl_timeout( $handle ) {
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
		curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
	}



	/**
	 * Init form fields
	 */
	public function init_form_fields() {
		if ( ! WC_Vivawallet_Helper::is_valid_currency() ) {
			$this->form_fields = include dirname( __FILE__ ) . '/admin/vivawallet-error-page.php';
		} else {
			$this->form_fields = include dirname( __FILE__ ) . '/admin/vivawallet-settings.php';
		}

	}


	/**
	 * Admin_settings_start.
	 */
	public function admin_settings_start() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( is_cart() || is_checkout() ) {
			return;
		}

		if ( 'no' === $this->enabled ) {
			return;
		}

		if ( 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
			return;
		}

		if ( isset( $_GET['section'], $_GET['section_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_GET['section_nonce'] ), 'section_action' ) ) {
			return;
		}

		if ( 'vivawallet_native' !== $_GET['section'] ) {
			return;
		}

		$this->admin_check_and_display_sources_in_admin( $this->credentials['demo_token'], $this->credentials['live_token'] );
	}

	/**
	 * Set_credentials.
	 */
	public function set_credentials() {
		return array(
			'demo_token' => WC_Vivawallet_Helper::get_token( $this->test_client_id, $this->test_client_secret, 'yes', 'back' ),
			'live_token' => WC_Vivawallet_Helper::get_token( $this->client_id, $this->client_secret, 'no', 'back' ),
		);
	}

	/**
	 * Display any admin notices to the user.
	 */
	public function admin_notices() {

		// fire only once.

		if ( ! get_transient( 'admin-vw-notice-transient' ) ) {
			return;
		}
		set_transient( 'admin-vw-notice-transient', false, 0 );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( is_cart() || is_checkout() ) {
			return;
		}

		if ( 'no' === $this->enabled ) {
			return;
		}

		if ( 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
			return;
		}

		if ( isset( $_GET['section'], $_GET['section_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_GET['section_nonce'] ), 'section_action' ) ) {
			return;
		}

		if ( 'vivawallet_native' !== $_GET['section'] ) {
			return;
		}

		$site_url = get_site_url();
		$domain   = wp_parse_url( $site_url, PHP_URL_HOST );

		if ( ! WC_Vivawallet_Helper::is_valid_domain_name( $domain ) ) { // if not in a valid domain.

			$error  = __( 'Viva Wallet Warning: A valid domain name is needed for Viva Wallet services to work correctly. Your domain,', 'woocommerce_vivawallet' );
			$error .= ' "';
			$error .= $domain;
			$error .= '", ';
			$error .= __( 'does not seem valid.', 'woocommerce_vivawallet' );
			$error .= ' ';
			$error .= __( 'To test locally, edit your hosts file and add a domain, for example, "vivawalletdemo.test".', 'woocommerce_vivawallet' );

			echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';

		}

		if ( 'yes' === $this->test_mode ) {
			if ( empty( $this->credentials['demo_token'] ) ) {
				$error = __( 'Viva Wallet: Your DEMO credentials are NOT valid. Please check your credentials!', 'woocommerce_vivawallet' );
				echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';
				return;
			} else {
				$mes = __( 'Viva Wallet: Your DEMO credentials are valid.', 'woocommerce_vivawallet' );
				echo '<div class="updated"><p><b>' . esc_html( $mes ) . '</b></p></div>';
			}
		} else {
			if ( empty( $this->credentials['live_token'] ) ) {

				$error = __( 'Viva Wallet: Your LIVE credentials are NOT valid. Please check your credentials!', 'woocommerce_vivawallet' );
				echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';
				return;
			} else {
				$mes = __( 'Viva Wallet: Your LIVE credentials are valid.', 'woocommerce_vivawallet' );
				echo '<div class="updated"><p><b>' . esc_html( $mes ) . '</b></p></div>';
			}
		}

		$creds = $this->get_option( 'source_error' );
		if ( ! empty( $creds ) ) {
			if ( 'code_created' === $creds ) {

				if ( 'yes' === $this->test_mode ) {
					$mes  = __( 'Viva Wallet: A new DEMO source code has been created in the Viva Wallet banking app with code: ', 'woocommerce_vivawallet' );
					$mes .= $this->get_option( 'test_source_code' );

				} else {
					$mes  = __( 'Viva Wallet: A new LIVE source code has been created in the Viva Wallet banking app with code: ', 'woocommerce_vivawallet' );
					$mes .= $this->get_option( 'source_code' );
				}

				$mes .= __( ', and name: ', 'woocommerce_vivawallet' ) . 'Viva Wallet For WC - ' . $domain . '.';
				$mes .= __( ', and set as default source code.', 'woocommerce_vivawallet' );

				echo '<div class="updated"><p><b>' . esc_html( $mes ) . '</b></p></div>';

				$this->update_option( 'source_error', '' );
			} elseif ( 'code_exists' === $creds ) {
				if ( 'yes' === $this->test_mode ) {
					$mes  = __( 'Viva Wallet: You changed or updated your DEMO credentials, a DEMO source code for your domain was found with name: ', 'woocommerce_vivawallet' );
					$mes .= $this->get_option( 'test_source_code' );
				} else {
					$mes  = __( 'Viva Wallet: You changed or updated your LIVE credentials, a LIVE source code for your domain was found with name: ', 'woocommerce_vivawallet' );
					$mes .= $this->get_option( 'source_code' );
				}
				$mes .= __( ', and set as default source code.', 'woocommerce_vivawallet' );
				echo '<div class="updated"><p><b>' . esc_html( $mes ) . '</b></p></div>';
				$this->update_option( 'source_error', '' );

			} else {
				if ( 'yes' === $this->test_mode ) {
					$error = __( 'Viva Wallet: Your DEMO credentials are valid. ', 'woocommerce_vivawallet' );
				} else {
					$error = __( 'Viva Wallet: Your LIVE credentials are valid. ', 'woocommerce_vivawallet' );
				}
				$error .= __( 'But there was an error trying to create a new source. Error: ', 'woocommerce_vivawallet' ) . $creds;
				$error .= ' ';
				$error .= __( 'Please try to save your settings again. Also check the sources selection box in advanced settings to see your available source codes and set one from there if available.', 'woocommerce_vivawallet' );
				echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';
				$this->update_option( 'source_error', '' );
				return;
			}
		}

		if ( 'yes' === $this->test_mode ) {
			$source = $this->get_option( 'test_source_code' );
		} else {
			$source = $this->get_option( 'source_code' );
		}

		if ( ! empty( $source ) ) {

			if ( 'yes' === $this->test_mode ) {
				$token = $this->credentials['demo_token'];
			} else {
				$token = $this->credentials['live_token'];
			}
			$res = WC_Vivawallet_Helper::check_source( $token, $source, $this->test_mode );

			if ( 'Active' === $res ) {
				if ( 'yes' === $this->test_mode ) {
					$mes = __( 'Viva Wallet: Your DEMO source code:', 'woocommerce_vivawallet' );
				} else {
					$mes = __( 'Viva Wallet: Your LIVE source code:', 'woocommerce_vivawallet' );
				}

				$mes .= ' ';
				$mes .= $source;
				$mes .= ' ';
				$mes .= __( 'is verified and you are ready to accept payments.', 'woocommerce_vivawallet' );
				echo '<div class="updated"><p><b>' . esc_html( $mes ) . '</b></p></div>';
			} elseif ( 'Pending' === $res ) {
				if ( 'yes' === $this->test_mode ) {
					$error  = __( 'Viva Wallet: Your DEMO credentials are valid and connection with Viva Wallet was successful. ', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= __( 'We\'re in the process of reviewing your DEMO website "', 'woocommerce_vivawallet' );
				} else {
					$error  = __( 'Viva Wallet: Your LIVE credentials are valid and connection with Viva Wallet was successful. ', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= __( 'We\'re in the process of reviewing your LIVE website "', 'woocommerce_vivawallet' );
				}
				$error .= $source;
				$error .= '". ';
				$error .= __( 'For a perfect one-shot-approval (1-2 business days), make sure that you have included the elements described in the following link. ', 'woocommerce_vivawallet' );
				$error .= 'https://help.vivawallet.com/hc/en-us/articles/360002562577-What-happens-during-payment-source-activation';
				echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';
			} elseif ( 'InActive' === $res ) {
				if ( 'yes' === $this->test_mode ) {
					$error  = __( 'Viva Wallet: Your DEMO credentials are valid and connection with Viva Wallet was successful. ', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= __( 'But your DEMO source code: ', 'woocommerce_vivawallet' );
				} else {
					$error  = __( 'Viva Wallet: Your LIVE credentials are valid and connection with Viva Wallet was successful. ', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= __( 'But your LIVE source code: ', 'woocommerce_vivawallet' );
				}
				$error .= ' ';
				$error .= $source;
				$error .= ' ';
				$error .= __( 'has been BLOCKED. Please check your latest email from Viva Wallet Support for more info.', 'woocommerce_vivawallet' );
				echo '<div class="error"><p><b>' . esc_html( $mes ) . '</b></p></div>';
			}
		}

		$res = $this->checkIfInstalments();
		if ( ! $res ) { // if we dont allow instalments.
			if ( $this->checkIfInstalmentsSet() ) { // check if instalments is set.
				$this->update_option( 'instalments', '' ); // empty and notify admin.
				$error = 'Viva Wallet: WARNING Instalments option is only available for greek Viva Wallet accounts. Your instalments setting was reset to default.';
				echo '<div class="error"><p><b>' . esc_html( $error ) . '</b></p></div>';
			}
		}

	}



	/**
	 * Loads and displays the sources in admin settings page
	 *
	 * @param string $demo_token demo_token.
	 * @param string $live_token live_token.
	 */
	private function admin_check_and_display_sources_in_admin( $demo_token, $live_token ) {

		if ( isset( $demo_token ) && false !== $demo_token ) {
			$this->demo_source_list = WC_Vivawallet_Helper::get_sources( $demo_token, 'yes' );

		} else {
			$this->demo_source_list = array();
		}

		if ( isset( $live_token ) && false !== $live_token ) {
			$this->live_source_list = WC_Vivawallet_Helper::get_sources( $live_token, 'no' );
		} else {
			$this->live_source_list = array();
		}

		$site_url = get_site_url();

		$domain = wp_parse_url( $site_url, PHP_URL_HOST );

		if ( ! empty( $this->demo_source_list ) && 'error' !== $this->demo_source_list ) {
			foreach ( $this->demo_source_list as $key => $value ) { // in demo mode we show all sources.
				$this->form_fields['test_source_code']['options'][ $value->sourceCode ] = $value->sourceCode . ' - ' . $value->name . ' - ' . $value->domain;
			}
		}

		if ( ! empty( $this->live_source_list ) && 'error' !== $this->live_source_list ) {
			foreach ( $this->live_source_list as $key => $value ) {
				if ( 'Default' !== $value->sourceCode && $value->domain === $domain ) { // in live sources we hide default and all sources not related to domain.
					$this->form_fields['source_code']['options'][ $value->sourceCode ] = $value->sourceCode . ' - ' . $value->name . ' - ' . $value->domain;
				}
			}
		}

	}


	/**
	 * Admin scripts and styles
	 */
	public function admin_scripts_and_styles() {

		if ( 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
			return;
		}

		wp_register_script( 'vivawallet_admin', plugins_url( '/assets/js/admin-vivawallet.js', __FILE__ ), array( 'jquery' ), WC_VIVAWALLET_VERSION, true );
		wp_localize_script(
			'vivawallet_admin',
			'vivawallet_admin_params',
			array(
				'allowInstalments' => $this->checkIfInstalments(),
			)
		);

		wp_enqueue_script( 'vivawallet_admin' );
	}





	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		ob_start();

		$res = '<p>';
		if ( isset( $this->description ) ) {
			$res .= $this->description;
			$res .= '</p>';
			echo wp_kses_post( $res );
			$res = '<p>';
		}
		$res .= '<img src="' . WC_Vivawallet_Helper::VW_CC_LOGOS_URL . '" style="max-width:100%;"  />';
		$res .= '</p>';
		echo wp_kses_post( $res );

		if ( 'yes' === $this->test_mode ) {
			/* translators: warning */
			$test_mode_warning  = '<div><p>' . esc_html__( 'TEST MODE ENABLED. ', 'woocommerce_vivawallet' );
			$test_mode_warning .= '<br />' . esc_html__( 'For testing you can use 4111 1111 1111 1111 for card number, 111 for card code and any future date for expiry date in order to test the payment process.', 'woocommerce_vivawallet' );
			/* translators: For more info check the %1$s documentation %2$s */
			$test_mode_warning .= '<br />' . sprintf( esc_html__( 'For more info check the %1$s documentation %2$s', 'woocommerce_vivawallet' ), '<a target="_blank" href="https://developer.vivawallet.com/api-reference-guide/payment-api/">', '</a>' ) . '</p></div>';

			$test_mode_warning = trim( $test_mode_warning );
			echo wp_kses_post( $test_mode_warning );
		}

		$cc_form           = new WC_Payment_Gateway_CC();
		$cc_form->id       = $this->id;
		$cc_form->supports = $this->supports;
		$cc_form->form();

		ob_end_flush();
	}

	/**
	 * Payment scripts
	 */
	public function payment_scripts() {
		global $wp;
		// we need JavaScript to process a token only on checkout pages.
		if ( ! is_checkout() && empty( $_GET['pay_for_order'] ) ) {
			return;
		}

		if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
			// check for thank you page (return if true.. so we dont inject js in there.
			return;
		}

		// only add scripts and css when in checkout.. not in order-received page.
		if ( isset( $_GET['order-received'], $_GET['order-received_nonce'] ) && 'true' === $_GET['order-received'] && ! wp_verify_nonce( sanitize_key( $_GET['order-received_nonce'] ), 'order-received_action' ) ) {
			return;
		}

		// if our payment gateway is disabled, we do not have to enqueue scripts.
		if ( 'no' === $this->enabled ) {
			return;
		}

		wp_register_style( 'vivawallet_styles_core', plugins_url( 'assets/css/vivawallet-styles-core.css', __FILE__ ), array(), WC_VIVAWALLET_VERSION );
		wp_enqueue_style( 'vivawallet_styles_core' );

		$site_url = get_site_url();
		$domain   = wp_parse_url( $site_url, PHP_URL_HOST );

		// if not valid domain.
		if ( ! WC_Vivawallet_Helper::is_valid_domain_name( $domain ) ) { // if not in a valid domain.

			$error  = __( 'Viva Wallet: A valid domain name is needed for Viva Wallet services to work correctly. Your domain,', 'woocommerce_vivawallet' );
			$error .= ' "';
			$error .= $domain;
			$error .= '", ';
			$error .= __( 'does not seem valid.', 'woocommerce_vivawallet' );
			$error .= ' ';
			$error .= __( 'To test locally, edit your hosts file and add a domain, for example, "vivawalletdemo.test".', 'woocommerce_vivawallet' );

			wc_add_notice( $error, 'error' );
			return;
		}

		$has_valid_creds = false;
		if ( 'yes' === $this->test_mode ) {
			if ( isset( $this->credentials['demo_token'] ) && false !== $this->credentials['demo_token'] ) {
				$has_valid_creds = true;
			}
		} else {
			if ( isset( $this->credentials['live_token'] ) && false !== $this->credentials['live_token'] ) {
				$has_valid_creds = true;
			}
		}

		// no reason to enqueue scripts if API keys are not set.
		if ( 'no' === $this->test_mode ) {
			if ( empty( $this->client_secret ) || empty( $this->client_id ) ) {
				$this->update_option( 'source_code', '' );
				$this->source_code = '';
				$error             = __( 'Viva Wallet: Client Secret or Client ID not set. Please check documentation and fill in your Viva Wallet gateway settings.', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}
			if ( empty( $this->source_code ) ) {
				$error = __( 'Viva Wallet: Source Code is not set. Please check documentation and fill in your Viva Wallet gateway settings.', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}

			if ( false === $has_valid_creds ) {
				$error = __( 'Viva Wallet: Your credentials are NOT valid. Please check your credentials!', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}

			$res = WC_Vivawallet_Helper::check_source( $this->credentials['demo_token'], $this->test_source_code, 'yes' );

			if ( 'Pending' === $res ) {
				$error = __( 'Viva Wallet: Your LIVE credentials are valid and connection with Viva Wallet was successful.', 'woocommerce_vivawallet' );
				/* translators: error */
				$error .= sprintf( __( 'We\'re in the process of reviewing your LIVE website. For a perfect one-shot-approval (1-2 business days), make sure that you have included the elements described in <a href="%s" target="_blank" style="text-decoration: underline; font-weight: bold;">this link</a>.', 'woocommerce_vivawallet' ), 'https://help.vivawallet.com/hc/en-us/articles/360002562577-What-happens-during-payment-source-activation' );
				wc_add_notice( $error, 'error' );
				return;
			} elseif ( 'InActive' === $res ) {
				if ( current_user_can( 'manage_woocommerce' ) ) {
					$error  = __( 'Viva Wallet: Your LIVE credentials are valid and connection with Viva Wallet was successful. But your LIVE source code, has been', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= '<span style="font-weight: bold;">';
					$error .= __( 'BLOCKED', 'woocommerce_vivawallet' );
					$error .= '</span>';
					$error .= '. ';
					$error .= __( 'Please check your latest email from Viva Wallet Support for more info.', 'woocommerce_vivawallet' );
					wc_add_notice( $error, 'error' );
				} else {
					$error = __( 'Viva Wallet: Something went wrong! Please try again or come back later. If you are the admin of the website, please check Viva Wallet for Woo Commerce plugin.', 'woocommerce_vivawallet' );
					wc_add_notice( $error, 'error' );
				}
				return;
			}
		} else {
			if ( empty( $this->test_client_id ) || empty( $this->test_client_secret ) ) {
				$this->update_option( 'test_source_code', '' );
				$this->test_source_code = '';
				$error                  = __( 'Viva Wallet: YOU ARE OPERATING IN TEST MODE. Test Client Secret or Client ID not set. Please check documentation and fill in your Viva Wallet gateway settings.', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}
			if ( empty( $this->test_source_code ) ) {
				$error = __( 'Viva Wallet: YOU ARE OPERATING IN TEST MODE. Test Source Code is not set. Please check documentation and fill in your Viva Wallet gateway settings.', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}

			if ( false === $has_valid_creds ) {
				$error = __( 'Viva Wallet: Your credentials are NOT valid. Please check your credentials!', 'woocommerce_vivawallet' );
				wc_add_notice( $error, 'error' );
				return;
			}

			$res = WC_Vivawallet_Helper::check_source( $this->credentials['live_token'], $this->source_code, 'no' );

			if ( 'Pending' === $res ) {
				$error = __( 'Viva Wallet: Your DEMO credentials are valid and connection with Viva Wallet was successful.', 'woocommerce_vivawallet' );
				/* translators: error */
				$error .= sprintf( __( 'We\'re in the process of reviewing your LIVE website. For a perfect one-shot-approval (1-2 business days), make sure that you have included the elements described in <a href="%s" target="_blank" style="text-decoration: underline; font-weight: bold;">this link</a>.', 'woocommerce_vivawallet' ), 'https://help.vivawallet.com/hc/en-us/articles/360002562577-What-happens-during-payment-source-activation' );
				wc_add_notice( $error, 'error' );
				return;
			} elseif ( 'InActive' === $res ) {
				if ( current_user_can( 'manage_woocommerce' ) ) {
					$error  = __( 'Viva Wallet: Your DEMO credentials are valid and connection with Viva Wallet was successful. But your DEMO Source Code, has been', 'woocommerce_vivawallet' );
					$error .= ' ';
					$error .= '<span style="font-weight: bold;">';
					$error .= __( 'BLOCKED', 'woocommerce_vivawallet' );
					$error .= '</span>';
					$error .= '. ';
					$error .= __( 'Please check your latest email from Viva Wallet Support for more info.', 'woocommerce_vivawallet' );
					wc_add_notice( $error, 'error' );
				} else {
					$error = __( 'Viva Wallet: Something went wrong! Please try again or come back later. If you are the admin of the website, please check Viva Wallet for Woo Commerce plugin.', 'woocommerce_vivawallet' );
					wc_add_notice( $error, 'error' );
				}

				return;
			}
		}
		// do not work with card details without SSL unless your website is in a test mode.
		if ( ! $this->test_mode && ! is_ssl() ) {
			$error = __( 'Viva Wallet: This site is not SSL protected. Please protect your domain to use Viva Wallet payments.', 'woocommerce_vivawallet' );
			wc_add_notice( $error, 'error' );
			return;
		}

		$total = WC()->cart->total;

		$test_mode     = $this->test_mode;
		$client_id     = ( 'yes' === $this->test_mode ) ? $this->test_client_id : $this->client_id;
		$client_secret = ( 'yes' === $this->test_mode ) ? $this->test_client_secret : $this->client_secret;
		$source_code   = ( 'yes' === $this->test_mode ) ? $this->test_source_code : $this->source_code;

		$token = WC_Vivawallet_Credentials::get_credentials( 'front', $test_mode, $client_id, $client_secret, $source_code );

		$inject_cc_logo = false;

		if ( 'no' === $this->get_option( 'advanced_settings_enabled' ) ) {  // check if advanced settings is enabled.
			// if not enabled show inject logos (the default value is yes).
			$inject_cc_logo = true;
		} else {
			// check the prefered value for cc logo.
			if ( 'yes' === $this->get_option( 'cc_logo_enabled' ) ) {
				$inject_cc_logo = true;
			}
		}

		if ( $inject_cc_logo ) {
			wp_register_style( 'vivawallet_styles_cc_logos', plugins_url( 'assets/css/vivawallet-styles-cc-logos.css', __FILE__ ), array(), WC_VIVAWALLET_VERSION );
			wp_enqueue_style( 'vivawallet_styles_cc_logos' );
		}

		// check installments.

		$max_period = '1';

		$instal_logic = $this->instalments;

		if ( isset( $instal_logic ) && '' !== $instal_logic ) {

			$split_instal_vivawallet = explode( ',', $instal_logic );

			$c = count( $split_instal_vivawallet );

			$instal_vivawallet_max = array();

			for ( $i = 0; $i < $c; $i++ ) {

				list( $instal_amount, $instal_term ) = explode( ':', $split_instal_vivawallet[ $i ] );
				if ( $total >= $instal_amount ) {
					$instal_vivawallet_max[] = trim( $instal_term );
				}
			}
			if ( count( $instal_vivawallet_max ) > 0 ) {
				$max_period = max( $instal_vivawallet_max );
			}
		}

		$show_vw_logo = false;
		if ( 'no' === $this->get_option( 'advanced_settings_enabled' ) ) {
			$show_vw_logo = true;
		} else {
			if ( 'yes' === $this->get_option( 'logo_enabled' ) ) {
				$show_vw_logo = true;
			}
		}

		wp_enqueue_script( 'vivawallet-web-checkout-v02', WC_Vivawallet_Helper::get_api_url_endpoint( $this->test_mode, WC_Vivawallet_Helper::ENDPOINT_NATIVE_JS ), array( 'jquery' ), WC_Vivawallet_Helper::NATIVE_JS_VERSION, true );

		wp_register_script( 'woocommerce_vivawallet', plugins_url( '/assets/js/payment-vivawallet.js', __FILE__ ), array( 'jquery' ), WC_VIVAWALLET_VERSION, true );
		wp_localize_script(
			'woocommerce_vivawallet',
			'vivawallet_params',
			array(
				'token'             => $token['token'],
				'scriptUrl'         => WC_Vivawallet_Helper::get_api_url( $this->test_mode ),
				'installmentsUrl'   => WC_Vivawallet_Helper::get_api_url_endpoint( $this->test_mode, WC_Vivawallet_Helper::ENDPOINT_INSTALLMENTS ),
				'amount'            => $total,
				'allowInstallments' => $this->checkIfInstalments(),
				'maxInstallments'   => $max_period,
				'showVWLogo'        => $show_vw_logo,
				'logoPath'          => WC_Vivawallet_Helper::VW_LOGO_URL,
				'labelLogoTxt'      => esc_html__( 'Powered by', 'woocommerce_vivawallet' ),
				'labelForLoader'    => __( 'PLEASE WAIT', 'woocommerce_vivawallet' ),
				'labelForCCerror'   => '<strong>' . __( 'Please check your card details!', 'woocommerce_vivawallet' ) . '</strong>',
				'labelForAPIerror'  => '<strong>' . __( 'Connection to Viva Wallet API failed. Please check your connection or try again later.', 'woocommerce_vivawallet' ) . '</strong>',
				'labelForAJAXerror' => '<strong>' . __( 'Connection to WooCommerce checkout failed. Please check your connection or try again later.', 'woocommerce_vivawallet' ) . '</strong>',
			)
		);
		wp_enqueue_script( 'woocommerce_vivawallet', '', array(), WC_VIVAWALLET_VERSION, true );
	}

	/**
	 * Process payment
	 *
	 * @param int $order_id order id.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( isset( $_POST['viva_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['viva_nonce'] ), 'viva_action' ) ) {
			wc_add_notice( __( 'Something went wrong. Please refresh your page and try again.', 'woocommerce_vivawallet' ), 'error' );
			return array(
				'result'   => 'error',
				'redirect' => false,
			);
		}

		if ( isset( $_POST['testWooForm'] ) && 'true' === $_POST['testWooForm'] ) {
			return array(
				'result'    => 'success',
				'resultApi' => 'success',
				'redirect'  => false,
			);
		}

		if ( isset( $_POST['testCCForm'] ) && 'true' === $_POST['testCCForm'] ) {
			if ( ! isset( $_POST['accessToken'] ) || ! isset( $_POST['chargeToken'] ) || ! isset( $_POST['installments'] ) ) {
				/* translators: error credit card not valid */
				wc_add_notice( sprintf( __( 'Please check your %s or try again later!', 'woocommerce_vivawallet' ), '<strong>' . __( 'card details', 'woocommerce_vivawallet' ) . '</strong>' ), 'error' );

				return array(
					'result'   => 'error',
					'redirect' => esc_url_raw( add_query_arg( 'order-pay', $order->get_id(), add_query_arg( 'key', $order->get_order_key(), wc_get_page_permalink( 'checkout' ) ) ) ),
				);
			}

			$access_token = sanitize_text_field( wp_unslash( $_POST['accessToken'] ) );
			$charge_token = sanitize_text_field( wp_unslash( $_POST['chargeToken'] ) );

			$test_mode   = $this->test_mode;
			$source_code = ( 'yes' === $this->test_mode ) ? $this->test_source_code : $this->source_code;

			$installments_post_val = sanitize_text_field( wp_unslash( $_POST['installments'] ) );

			if ( ! $this->checkIfInstalments() ) { // reset installments (if not allowed).
				$installments_post_val = '1';
			}

			if ( 1 < $installments_post_val ) {
				$note  = __( 'WARNING: This order was paid with installments!', 'woocommerce_vivawallet' );
				$note .= ' ';
				$note .= __( 'Number of installments: ', 'woocommerce_vivawallet' ) . $installments_post_val;
				$order->add_order_note( $note, false );
			}

			$result = WC_Vivawallet_Helper::transaction_api_call( $order, $access_token, $test_mode, $source_code, $charge_token, $installments_post_val );

			if ( isset( $result['response']['code'] ) && 200 === $result['response']['code'] ) {

				$transaction_id = json_decode( $result['body'] );
				$transaction_id = $transaction_id->transactionId;

				add_post_meta( $order_id, WC_Vivawallet_Helper::POST_META_VW_TXN, $transaction_id );

				$status  = __( 'Order has been paid with Viva Wallet, TxID: ', 'woocommerce_vivawallet' );
				$status .= $transaction_id;

				$order->add_order_note( $status, false );
				$order->save();

				return array(
					'result'    => 'success',
					'resultApi' => 'success',
					'message'   => $transaction_id,
					'redirect'  => false,
				);
			}

			// transaction call to API failed.

			if ( isset( $result['headers']['X-Viva-CorrelationId'] ) ) {
				$note = 'Transaction failed with Viva-CorrelationId: ' . $result['headers']['X-Viva-CorrelationId'];
				$order->add_order_note( $note, false );
				$order->save();
			}

			if ( isset( $result['headers']['x-viva-eventid'] ) ) {
				$int = (int) $result['headers']['x-viva-eventid'];
				if ( 10000 < $int ) {
					wc_add_notice( __( 'The card issuer rejected this transaction. Please use a different card.', 'woocommerce_vivawallet' ), 'error' );
				} else {
					wc_add_notice( __( 'There was a problem with your card. Please check the card details and try again.', 'woocommerce_vivawallet' ), 'error' );
				}
			} else {
				wc_add_notice( __( 'There was a connection problem. Please try again later.', 'woocommerce_vivawallet' ), 'error' );
			}

			return array(
				'result'   => 'error',
				'redirect' => esc_url_raw( add_query_arg( 'order-pay', $order->get_id(), add_query_arg( 'key', $order->get_order_key(), wc_get_page_permalink( 'checkout' ) ) ) ),
			);

		} else {
			// this is the final call to save the order after the trans is completed...

			global $woocommerce;
			$woocommerce->cart->empty_cart();

			$order->payment_complete();
			$update_status = false;

			if ( 'no' === $this->get_advanced_settings() ) {  // check if advanced settings is enabled.
				// if not enabled complete the transaction (the default value is completed).
				$update_status = true;
			} else {
				// check the prefered order status value.
				if ( 'completed' === $this->get_order_update_status() ) {
					$update_status = true;
				}
			}

			if ( $update_status ) {
				$order->update_status( WC_Vivawallet_Helper::ORDER_STATUS_COMPLETE );
			}

			$order->save();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);

		}

	}

	/**
	 * Process refund
	 *
	 * @param int    $order_id order_id.
	 * @param null   $amount amount.
	 * @param string $reason reason.
	 *
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( ! is_numeric( $amount ) && 'refunded' === $amount ) {
			$order  = wc_get_order( $order_id );
			$amount = $order->get_total();
		}

		$test_mode     = $this->test_mode;
		$client_id     = ( 'yes' === $this->test_mode ) ? $this->test_client_id : $this->client_id;
		$client_secret = ( 'yes' === $this->test_mode ) ? $this->test_client_secret : $this->client_secret;
		$source_code   = ( 'yes' === $this->test_mode ) ? $this->test_source_code : $this->source_code;

		return WC_Vivawallet_Refund::process_refund( WC_Vivawallet_Credentials::get_credentials( 'back', $test_mode, $client_id, $client_secret, $source_code ), $order_id, $amount, $reason );

	}

	/**
	 * Method to create sources in Viva Wallet
	 */
	public function process_admin_options() {

		$old_client_id_test = $this->get_option( 'test_client_id' );
		$old_client_id_live = $this->get_option( 'client_id' );

		$old_client_secret_test = $this->get_option( 'test_client_secret' );
		$old_client_secret_live = $this->get_option( 'client_secret' );

		parent::process_admin_options();

		$this->enabled = $this->get_option( 'enabled' );
		$this->test_mode = $this->get_option( 'test_mode' );

		$this->client_id     = $this->get_option( 'client_id' );
		$this->client_secret = $this->get_option( 'client_secret' );

		$this->test_client_id     = $this->get_option( 'test_client_id' );
		$this->test_client_secret = $this->get_option( 'test_client_secret' );

		if ( 'yes' === $this->test_mode ) {
			if ( $this->test_client_id !== $old_client_id_test || $this->test_client_secret !== $old_client_secret_test ) {
				$this->update_option( 'test_source_code', '' );
			}
		} else {
			if ( $this->client_id !== $old_client_id_live || $this->client_secret !== $old_client_secret_live ) {
				$this->update_option( 'source_code', '' );
			}
		}

		$this->test_source_code = $this->get_option( 'test_source_code' );
		$this->source_code      = $this->get_option( 'source_code' );

		$this->credentials = $this->set_credentials();

		$token = ( 'yes' === $this->test_mode ) ? $this->credentials['demo_token'] : $this->credentials['live_token'];

		if ( ! empty( $token ) ) {

			$source = ( 'yes' === $this->test_mode ) ? $this->test_source_code : $this->source_code;

			if ( empty( $source ) ) { // no source found.. or credentials changed.. create one.

				// first check if source for this domain exists.

				$existing_sources = WC_Vivawallet_Helper::get_sources( $token, $this->test_mode );

				if ( 'error' !== $existing_sources ) {
					$existing_sources_length = count( $existing_sources );
				} else {
					$existing_sources_length = 0;
				}

				$source_id   = $existing_sources_length + 1;
				$source_name = '';

				// scan the object for sources and if we have a match to the domain.
				if ( $existing_sources_length >= 1 ) {
					foreach ( $existing_sources as $id => $item ) {
						$site_url = get_site_url();
						$domain   = wp_parse_url( $site_url, PHP_URL_HOST );

						if ( $item->domain === $domain ) { // source exists for this domain.

							$source_name = $item->sourceCode;
							$this->update_option( 'source_error', 'code_exists' );
							break;
						}
					}
				}

				// if no sources found for the domain .. create one..
				if ( '' === $source_name ) {
					// fix name to send.
					$temp_name = WC_Vivawallet_Helper::SOURCE_IDENTIFIER . str_pad( $source_id, 4, '0', STR_PAD_LEFT );

					$res = WC_Vivawallet_Source::create_source( $token, $temp_name, $this->test_mode );

					$source_name = $temp_name;
					$this->update_option( 'source_error', 'code_created' );
				}

				// set the correct source to the saved source code value.
				if ( 'yes' === $this->test_mode ) {
					$this->update_option( 'test_source_code', $source_name );
				} else {
					$this->update_option( 'source_code', $source_name );
				}
			}
		}

	}


	/**
	 * Viva payments credit card fields
	 *
	 * @param array $cc_fields cc_fields.
	 *
	 * @return array
	 */
	public function viva_payments_credit_card_fields( $cc_fields ) {

		foreach ( $cc_fields as $key => $value ) {
			// change the name and add data-vp to cc inputs.
			if ( 'card-number-field' === $key ) {
				$value             = str_replace( 'id="vivawallet_native-card-number"', 'id="vivawallet_native-card-number" data-vp="cardnumber"', $value );
				$cc_fields[ $key ] = $value;
			}
			if ( 'card-expiry-field' === $key ) {
				$value             = str_replace( 'id="vivawallet_native-card-expiry"', 'id="vivawallet_native-card-expiry" data-vp="expdate"', $value );
				$cc_fields[ $key ] = $value;
			}
			if ( 'card-cvc-field' === $key ) {
				$value             = str_replace( 'id="vivawallet_native-card-cvc"', 'id="vivawallet_native-card-cvc" data-vp="cvv"', $value );
				$cc_fields[ $key ] = $value;
			}
		}

		return $cc_fields;
	}








	/**
	 * CheckIfInstalments
	 * checks if instalments are allowed (only for greek stores)
	 *
	 * @return boolean
	 */
	public function checkIfInstalments() {
		$wc_country = WC_Admin_Settings::get_option( 'woocommerce_default_country' );
		if ( isset( $wc_country ) && ! empty( $wc_country ) ) {
			$wc_country = explode( ':', $wc_country );
			$wc_country = $wc_country[0];
			if ( 'GR' === $wc_country ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * CheckIfInstalmentsSet
	 *
	 * @return boolean
	 */
	public function checkIfInstalmentsSet() {
		$vw_instalments = $this->get_option( 'instalments' );
		if ( isset( $vw_instalments ) && '' !== $vw_instalments ) {
			return true;
		}
		return false;
	}





	/**
	 * Get advanced settings
	 *
	 * @return string
	 */
	public function get_advanced_settings() {
		return $this->get_option( 'advanced_settings_enabled' );
	}

	/**
	 * Get_order_update_status
	 *
	 * @return string
	 */
	public function get_order_update_status() {
			return $this->get_option( 'order_status' );
	}

}
