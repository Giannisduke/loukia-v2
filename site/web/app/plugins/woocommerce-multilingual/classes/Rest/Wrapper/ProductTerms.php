<?php

namespace WCML\Rest\Wrapper;

use WPML\FP\Obj;
use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Exceptions\InvalidTerm;
use WCML\Rest\Exceptions\MissingLanguage;

class ProductTerms extends Handler {

	/** @var Sitepress */
	private $sitepress;
	/** @var WPML_Term_Translation */
	private $wpmlTermTranslations;
	/** @var WCML_Terms */
	private $wcmlTerms;

	public function __construct(
		\SitePress $sitepress,
		\WPML_Term_Translation $wpmlTermTranslations,
		\WCML_Terms $wcmlTerms
	) {
		$this->sitepress            = $sitepress;
		$this->wpmlTermTranslations = $wpmlTermTranslations;
		$this->wcmlTerms            = $wcmlTerms;
	}

	/**
	 * @param array $args
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array
	 *
	 * @throws InvalidLanguage
	 */
	public function query( $args, $request ) {
		$language = Obj::prop( 'lang', $request->get_params() );

		if ( $language ) {
			if ( 'all' === $language ) {
				remove_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ], 10 );
				remove_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );
			} else {
				$this->checkLanguage( $language );
			}
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

		$response->data['translations'] = [];

		$id   = Obj::prop( 'id', $response->data );
		$trid = $this->wpmlTermTranslations->get_element_trid( $id );

		if ( $trid ) {
			$response->data['translations'] = $this->wpmlTermTranslations->get_element_translations( $id, $trid );
			$response->data['lang']         = $this->wpmlTermTranslations->get_element_lang_code( $id );
		}

		return $response;
	}


	/**
	 * Sets the product information according to the provided language
	 *
	 * @param WP_Term $term
	 * @param WP_REST_Request $request
	 * @param bool $creating
	 *
	 * @throws InvalidLanguage
	 * @throws InvalidTerm
	 *
	 */
	public function insert( $term, $request, $creating ) {
		$language      = Obj::prop( 'lang', $request->get_params() );
		$translationOf = Obj::prop( 'translation_of', $request->get_params() );

		if ( $language && in_array( $request->get_method(), array( 'POST', 'PUT' ), true ) ) {

			$this->checkLanguage( $language );

			if ( $translationOf ) {
				$trid = $this->wpmlTermTranslations->get_element_trid( $translationOf );
				if ( empty( $trid ) ) {
					throw new InvalidTerm( $translationOf );
				}
			} else {
				$trid = null;
			}

			$this->sitepress->set_element_language_details( $term->term_id, 'tax_' . $term->taxonomy, $trid, $language );

			$this->wcmlTerms->update_terms_translated_status( $term->taxonomy );
		} elseif ( $translationOf ) {
			throw new MissingLanguage();
		}
	}

	private function checkLanguage( $language ) {
		if ( ! $this->sitepress->is_active_language( $language ) ) {
			throw new InvalidLanguage( $language );
		}
	}

}