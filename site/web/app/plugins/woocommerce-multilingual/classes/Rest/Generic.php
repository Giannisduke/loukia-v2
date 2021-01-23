<?php

namespace WCML\Rest;

class Generic {

	/**
	 * Enforces the language of request as the current language to be able to filter items by language
	 *
	 * @param WP_REST_Server $wp_rest_server
	 */
	public static function setLanguageForRequest( \WP_REST_Server $wp_rest_server ) {
		if ( isset( $_GET['lang'] ) ) {
			if ( apply_filters( 'wpml_language_is_active', false, $_GET['lang'] ) ) {
				wpml_switch_language_action( $_GET['lang'] );
			}
		}
	}

	/**
	 * Prevent WPML redirection when using the default language as a parameter in the url
	 */
	public static function preventDefaultLangUrlRedirect() {
		$exp = explode( '?', $_SERVER['REQUEST_URI'] );
		if ( ! empty( $exp[1] ) ) {
			parse_str( $exp[1], $vars );
			if ( isset( $vars['lang'] ) && $vars['lang'] === wpml_get_default_language() ) {
				unset( $vars['lang'] );
				$_SERVER['REQUEST_URI'] = $exp[0] . '?' . http_build_query( $vars );
			}
		}
	}

	/**
	 * @param WP_Query $wp_query
	 */
	public static function autoAdjustIncludedIds( \WP_Query $wp_query ) {
		$lang    = $wp_query->get( 'lang' );
		$include = $wp_query->get( 'post__in' );
		if ( empty( $lang ) && ! empty( $include ) ) {
			$filtered_include = array();
			foreach ( $include as $id ) {
				$filtered_include[] = wpml_object_id_filter( $id, get_post_type( $id ), true );
			}
			$wp_query->set( 'post__in', $filtered_include );
		}
	}

}