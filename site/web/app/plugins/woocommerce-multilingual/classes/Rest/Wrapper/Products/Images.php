<?php

namespace WCML\Rest\Wrapper\Products;

use WCML\Rest\Wrapper\Handler;

class Images extends Handler {

	/** @var WCML_Products */
	private $wcmlProduct;

	/** @var Translatable|NonTranslatable */
	private $wcmlMedia;

	public function __construct(
		\WCML_Products $wcmlProduct,
		$wcmlMedia
	) {
		$this->wcmlProduct = $wcmlProduct;
		$this->wcmlMedia   = $wcmlMedia;
	}

	/**
	 * @param object $object Inserted object.
	 * @param WP_REST_Request $request Request object.
	 * @param boolean $creating True when creating object, false when updating.
	 */
	public function insert( $object, $request, $creating ) {
		$data = $request->get_params();

		if ( isset( $data['translation_of'] ) ) {
			$this->wcmlMedia->sync_thumbnail_id( $data['translation_of'], $object->get_id(), $data['lang'] );
			$this->wcmlMedia->sync_product_gallery( $data['translation_of'] );

			if ( isset( $data['variations'] ) ) {
				foreach ( $data['variations'] as $variation_id ) {
					$this->wcmlMedia->sync_variation_thumbnail_id( $this->wcmlProduct->get_original_product_id( $variation_id ), $variation_id, $data['lang'] );
				}
			}
		}
	}
}