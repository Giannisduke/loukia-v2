<?php

namespace WCML\Rest\Wrapper\Products;

use WCML\Rest\Wrapper\Handler;

class Prices extends Handler {

	/** @var WCML_Multi_Currency */
	private $wcmlMultiCurrency;
	/** @var array */
	private $currenciesOrderSettings;
	/** @var WPML_Post_Translation */
	private $wpmlPostTranslations;

	public function __construct(
		\WCML_Multi_Currency $wcmlMultiCurrency,
		array $currenciesOrderSettings,
		\WPML_Post_Translation $wpmlPostTranslations
	) {
		$this->wcmlMultiCurrency       = $wcmlMultiCurrency;
		$this->currenciesOrderSettings = $currenciesOrderSettings;
		$this->wpmlPostTranslations    = $wpmlPostTranslations;
	}

	/**
	 * @param WP_REST_Response $response The response object.
	 * @param object $object Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {

		if ( ! empty( $this->currenciesOrderSettings ) ) {

			$response->data['multi-currency-prices'] = [];
			$isCustomPricesOn                        = get_post_meta( $response->data['id'], '_wcml_custom_prices_status', true );

			foreach ( $this->currenciesOrderSettings as $currency ) {
				if ( $currency !== wcml_get_woocommerce_currency_option() ) {
					if ( $isCustomPricesOn ) {
						$customPrices = (array) $this->wcmlMultiCurrency->custom_prices->get_product_custom_prices( $response->data['id'], $currency );
						foreach ( $customPrices as $key => $price ) {
							$response->data['multi-currency-prices'][ $currency ][ preg_replace( '#^_#', '', $key ) ] = $price;
						}
					} else {
						$response->data['multi-currency-prices'][ $currency ]['regular_price'] =
							$this->wcmlMultiCurrency->prices->raw_price_filter( $response->data['regular_price'], $currency );
						if ( ! empty( $response->data['sale_price'] ) ) {
							$response->data['multi-currency-prices'][ $currency ]['sale_price'] =
								$this->wcmlMultiCurrency->prices->raw_price_filter( $response->data['sale_price'], $currency );
						}
					}
				}
			}
		}

		return $response;
	}

	/**
	 * @param object $object Inserted object.
	 * @param WP_REST_Request $request Request object.
	 * @param boolean $creating True when creating object, false when updating.
	 */
	public function insert( $object, $request, $creating ) {
		$data = $request->get_params();

		if ( ! empty( $data['custom_prices'] ) ) {

			$trid              = $this->wpmlPostTranslations->get_element_trid( $object->get_id() );
			$originalProductId = $this->wpmlPostTranslations->get_original_post_ID( $trid );

			update_post_meta( $originalProductId, '_wcml_custom_prices_status', 1 );

			foreach ( $data['custom_prices'] as $currency => $prices ) {

				$pricesUscore = array();
				foreach ( $prices as $k => $p ) {
					$pricesUscore[ '_' . $k ] = $p;
				}
				$this->wcmlMultiCurrency->custom_prices->update_custom_prices( $originalProductId, $pricesUscore, $currency );

			}

		}
	}

}