<?php

namespace WCML\Rest\Wrapper\Products;

use WCML\Rest\Wrapper\Handler;
use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Exceptions\InvalidProduct;
use WCML\Rest\Exceptions\Generic;


class Languages extends Handler {

	/** @var Sitepress */
	private $sitepress;
	/** @var WPML_Post_Translation */
	private $wpmlPostTranslations;
	/** @var WPML_Query_Filter */
	private $wpmlQueryFilter;
	/** @var WCML_Synchronize_Variations_Data */
	private $wcmlSyncVariationsData;
	/** @var WCML_Attributes */
	private $wcmlAttributes;

	public function __construct(
		\Sitepress $sitepress,
		\WPML_Post_Translation $wpmlPostTranslations,
		\WPML_Query_Filter $wpmlQueryFilter,
		\WCML_Synchronize_Variations_Data $wcmlSyncVariationsData,
		\WCML_Attributes $wcmlAttributes
	) {
		$this->sitepress              = $sitepress;
		$this->wpmlPostTranslations   = $wpmlPostTranslations;
		$this->wpmlQueryFilter        = $wpmlQueryFilter;
		$this->wcmlSyncVariationsData = $wcmlSyncVariationsData;
		$this->wcmlAttributes         = $wcmlAttributes;
	}

	/**
	 * @param array $args
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		$data = $request->get_params();
		if ( isset( $data['lang'] ) && $data['lang'] === 'all' ) {
			remove_filter( 'posts_join', [ $this->wpmlQueryFilter, 'posts_join_filter' ] );
			remove_filter( 'posts_where', [ $this->wpmlQueryFilter, 'posts_where_filter' ] );
		}

		return $args;
	}


	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param WP_REST_Response $response
	 * @param object $object
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {

		$response->data['translations'] = array();

		$trid = $this->wpmlPostTranslations->get_element_trid( $response->data['id'] );

		if ( $trid ) {
			$translations = $this->wpmlPostTranslations->get_element_translations( $response->data['id'], $trid );
			foreach ( $translations as $translation ) {
				$response->data['translations'][ $this->wpmlPostTranslations->get_element_lang_code( $translation ) ] = $translation;
			}
			$response->data['lang'] = $this->wpmlPostTranslations->get_element_lang_code( $response->data['id'] );
		}

		return $response;
	}


	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object $object
	 * @param WP_REST_Request $request
	 * @param bool $creating
	 *
	 * @throws InvalidLanguage
	 * @throws InvalidProduct
	 * @throws Generic
	 *
	 */
	public function insert( $object, $request, $creating ) {
		$data = $request->get_params();

		if ( isset( $data['lang'] ) && in_array( $request->get_method(), array( 'POST', 'PUT' ), true ) ) {

			if ( ! apply_filters( 'wpml_language_is_active', false, $data['lang'] ) ) {
				throw new InvalidLanguage( $data['lang'] );
			}
			if ( isset( $data['translation_of'] ) ) {
				$trid = $this->wpmlPostTranslations->get_element_trid( $data['translation_of'] );
				if ( empty( $trid ) ) {
					throw new InvalidProduct( $data['translation_of'] );
				}

				$this->sitepress->copy_custom_fields( $data['translation_of'], $object->get_id() );
			} else {
				$trid = null;
			}

			$this->sitepress->set_element_language_details( $object->get_id(), 'post_'.get_post_type( $object->get_id() ), $trid, $data['lang'] );
			wpml_tm_save_post( $object->get_id(), get_post( $object->get_id() ), ICL_TM_COMPLETE );

			if ( isset( $data['translation_of'] ) ) {
				// needs run after set_element_language_details
				$this->syncVariableProduct( $object, $data['translation_of'], $data['lang'] );
			}
		} else {
			if ( isset( $data['translation_of'] ) ) {
				throw new Generic( __( 'Using "translation_of" requires providing a "lang" parameter too', 'woocommerce-multilingual' ) );
			}
		}
	}

	/**
	 * @param object $object
	 * @param string $translationOf
	 * @param string $lang
	 */
	private function syncVariableProduct( $object, $translationOf, $lang ) {

		if ( 'variable' === $object->get_type() ) {
			$this->wcmlAttributes->sync_default_product_attr( $translationOf, $object->get_id(), $lang );
			$this->wcmlSyncVariationsData->sync_product_variations( $translationOf, $object->get_id(), $lang );
		}
	}


}