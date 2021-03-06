<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


class PR_DHL_API_Paket extends PR_DHL_API {

	const DHL_PAKET_DISPLAY_DAYS = 5;
	const DHL_PAKET_REMOVE_DAYS = 2;

	private $de_national_holidays = array('2020-11-22','2020-11-29','2020-12-06','2020-12-13','2020-12-20','2020-12-25','2020-12-26','2020-12-27','2021-01-01','2021-04-02','2021-04-04','2021-04-05','2021-05-01','2021-05-13','2021-05-23','2021-05-24','2021-10-03','2021-12-25','2021-12-26','2022-01-01','2022-04-15','2022-04-17','2022-04-18','2022-05-01','2022-05-26','2022-06-05','2022-06-06','2022-10-03','2022-12-25','2022-12-26','2023-01-01','2023-04-07','2023-04-09','2023-04-10','2023-05-01','2023-05-18','2023-05-28','2023-05-29','2023-10-03','2023-12-25','2023-12-26','2024-01-01','2024-03-29','2024-03-31','2024-04-01','2024-05-01','2024-05-09','2024-05-19','2024-05-20','2024-10-03','2024-12-25','2024-12-26');

	public function __construct( $country_code ) {
		$this->country_code = $country_code;
		try {
			$this->dhl_label = new PR_DHL_API_SOAP_Label( );
			$this->dhl_finder = new PR_DHL_API_SOAP_Finder( );
		} catch (Exception $e) {
			throw $e;	
		}
	}

	public function is_dhl_paket( ) {
		return true;
	}
	
	public function get_dhl_products_international() {
		$country_code = $this->country_code;
		
		$germany_int =  array( 
								'V55PAK' => __('DHL Paket Connect', 'pr-shipping-dhl'),
								'V54EPAK' => __('DHL Europaket (B2B)', 'pr-shipping-dhl'),
								'V53WPAK' => __('DHL Paket International', 'pr-shipping-dhl'),
								);

		$dhl_prod_int = array();

		switch ($country_code) {
			case 'DE':
				$dhl_prod_int = $germany_int;
				break;
			default:
				break;
		}

        return apply_filters( 'pr_shipping_dhl_paket_products_international', $dhl_prod_int );
	}

	public function get_dhl_products_domestic() {
		$country_code = $this->country_code;

		$germany_dom = array(  
								'V01PAK' => __('DHL Paket', 'pr-shipping-dhl'),
								'V01PRIO' => __('DHL Paket PRIO', 'pr-shipping-dhl'),
								'V62WP' => __('DHL Warenpost National', 'pr-shipping-dhl'),
								);

		$dhl_prod_dom = array();

		switch ($country_code) {
			case 'DE':
				$dhl_prod_dom = $germany_dom;
				break;
			default:
				break;
		}

        return apply_filters( 'pr_shipping_dhl_paket_products_domestic', $dhl_prod_dom );
	}

	public function get_dhl_preferred_day_time( $postcode, $account_num, $cutoff_time = '12:00', $exclude_working_days = array() ) {
		// Always exclude Sunday
		$exclude_sun = array( 'Sun' => __('sun', 'pr-shipping-dhl') );
		$exclude_working_days += $exclude_sun;
		$day_counter = 0;

		// Get existing timezone to reset afterwards
		$current_timzone = date_default_timezone_get();
		// Always set and get DE timezone and check against it. 
		date_default_timezone_set('Europe/Berlin');

		// Get existing time locale
		// $current_locale = setlocale(LC_TIME, 0);
		// Set time locale based on WP locale setting (Settings->General)
		// $wp_locale = get_locale();
		// setlocale(LC_TIME, $wp_locale);
		// setlocale(LC_TIME, 'de_DE', 'deu_deu', 'de_DE.utf8', 'German', 'deu/ger', 'de_DE@euro', 'de', 'ge');
		
		$tz_obj = new DateTimeZone( 'Europe/Berlin' );
		$today = new DateTime("now", $tz_obj);	// Should the order date be passed as a variable?
		$today_de_timestamp = $today->getTimestamp();

		$week_day = $today->format('D');
		$week_date = $today->format('Y-m-d');
		$week_time = $today->format('H:i');

		// Compare week day with key since key includes capital letter in beginning and will work for English AND German!
		// Check if today is a working day...
		if ( ( ! array_key_exists($week_day, $exclude_working_days) ) && ( ! in_array($week_date, $this->de_national_holidays) ) ) {
			// ... and check if after cutoff time if today is a transfer day
			if( $today_de_timestamp >= strtotime( $cutoff_time ) ) {
				// If the cut off time has been passed, then add a day
				$today->add( new DateInterval('P1D') ); // Add 1 day
				$week_day = $today->format('D');
				$week_date = $today->format('Y-m-d');

				$day_counter++;

			}
		}

		// Make sure the next transfer days are working days
		while ( array_key_exists($week_day, $exclude_working_days) || in_array($week_date, $this->de_national_holidays) ) {
			$today->add( new DateInterval('P1D') ); // Add 1 day
			$week_day = $today->format('D');
			$week_date = $today->format('Y-m-d');

			$day_counter++;
		}

		$args['postcode'] = $postcode;
		$args['account_num'] = $account_num;
		$args['start_date'] = $week_date;
		$dhl_parcel_services = new PR_DHL_API_REST_Parcel();
		$preferred_services = $dhl_parcel_services->get_dhl_parcel_services($args);
		
		$preferred_day_time = array();
		$preferred_day_time['preferred_day'] = $this->get_dhl_preferred_day( $preferred_services );

		// Reset time locael
		// setlocale(LC_TIME, $current_locale);
		// Reset timezone to not affect any other plugins
		date_default_timezone_set($current_timzone);

		return $preferred_day_time;
	}

	protected function get_dhl_preferred_day( $preferred_services ) {
		$day_of_week_arr = array(
		            '1' => __('Mon', 'pr-shipping-dhl'), 
		            '2' => __('Tue', 'pr-shipping-dhl'), 
		            '3' => __('Wed', 'pr-shipping-dhl'),
		            '4' => __('Thu', 'pr-shipping-dhl'),
		            '5' => __('Fri', 'pr-shipping-dhl'),
		            '6' => __('Sat', 'pr-shipping-dhl'),
		            '7' => __('Sun', 'pr-shipping-dhl')
		        );
		
		$preferred_days = array();
		if( isset( $preferred_services->preferredDay->available ) && $preferred_services->preferredDay->available && isset( $preferred_services->preferredDay->validDays ) ) {

			foreach ($preferred_services->preferredDay->validDays as $days_key => $days_value) {
				$temp_day_time = strtotime( $days_value->start );

				$day_of_week = date('N', $temp_day_time );
				$week_date = date('Y-m-d', $temp_day_time );

				$preferred_days[ $week_date ] = $day_of_week_arr[ $day_of_week ];
			}
			
			// Add none option
			array_unshift( $preferred_days, __('none', 'pr-shipping-dhl') );
		}


		return $preferred_days;
	}

	public function get_dhl_duties() {
		$duties = parent::get_dhl_duties();

		$duties_paket = array(
					'DXV' => __('Delivery Duty Paid (excl. VAT )', 'pr-shipping-dhl'),
					'DDX' => __('Delivery Duty Paid (excl. Duties, taxes and VAT)', 'pr-shipping-dhl')
					);
		$duties += $duties_paket;

		return $duties;
	}

	public function get_dhl_visual_age() {
		$visual_age = array(
					'0' => _x('none', 'age context', 'pr-shipping-dhl'),
					'A16' => __('Minimum age of 16', 'pr-shipping-dhl'),
					'A18' => __('Minimum age of 18', 'pr-shipping-dhl')
					);
		return $visual_age;
	}
}
