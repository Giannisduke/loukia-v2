<?php

namespace WCML\Rest\Wrapper;

use WCML\Rest\Wrapper\Products\Images as ProductsImages;
use WCML\Rest\Wrapper\Products\Languages as ProductsLanguages;
use WCML\Rest\Wrapper\Products\Prices as ProductsPrices;

use WCML\Rest\Wrapper\Orders\Languages as OrdersLanguages;
use WCML\Rest\Wrapper\Orders\Prices as OrdersPrices;
use WCML\Rest\Wrapper\Reports\ProductsCount;
use WCML\Rest\Wrapper\Reports\ProductsSales;
use WCML\Rest\Wrapper\Reports\TopSeller;

class Factory {

	/**
	 * @return Handler
	 */
	public static function create( $objectType ) {
		global $woocommerce_wpml, $wpml_post_translations, $wpml_term_translations, $sitepress, $wpml_query_filter, $wpdb;

		$isMultiCurrencyOn = wcml_is_multi_currency_on();

		switch ( $objectType ) {
			case 'shop_order':
				$objects[] = new OrdersLanguages();
				if ( $isMultiCurrencyOn ) {
					$objects[] = new OrdersPrices( $woocommerce_wpml->multi_currency->orders );
				}

				return new Composite( $objects );
			case 'product_variation':
			case 'product':
				$objects[] = new ProductsLanguages( $sitepress, $wpml_post_translations, $wpml_query_filter, $woocommerce_wpml->sync_variations_data, $woocommerce_wpml->attributes );
				$objects[] = new ProductsImages( $woocommerce_wpml->products, $woocommerce_wpml->media );
				if ( $isMultiCurrencyOn ) {
					$objects[] = new ProductsPrices( $woocommerce_wpml->multi_currency, $woocommerce_wpml->settings['currencies_order'], $wpml_post_translations );
				}

				return new Composite( $objects );
			case 'term':
				return new ProductTerms( $sitepress, $wpml_term_translations, $woocommerce_wpml->terms );
			case 'reports_top_seller':
				return new TopSeller( $sitepress );
			case 'reports_products_count':
				return new ProductsCount( $sitepress, $wpdb );
			case 'reports_products_sales':
				return new ProductsSales();
		}

		return new Handler();
	}

}