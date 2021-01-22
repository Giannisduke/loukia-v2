<?php

class FWP_i18n_Polylang
{

    function __construct() {
        add_action( 'wp_footer', array( $this, 'wp_footer' ), 30 );
        add_action( 'admin_init', array( $this, 'register_strings' ) );

        add_filter( 'facetwp_query_args', array( $this, 'facetwp_query_args' ), 10, 2 );
        add_filter( 'facetwp_indexer_query_args', array( $this, 'facetwp_indexer_query_args' ) );
        add_filter( 'facetwp_render_params', array( $this, 'support_preloader' ) );
        add_filter( 'facetwp_i18n', array( $this, 'facetwp_i18n' ) );
        add_filter( 'get_terms_args', array( $this, 'get_terms_args' ) );
    }


    /**
     * Put the language into FWP_HTTP
     */
    function wp_footer() {
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
            echo "<script>if ('undefined' != typeof FWP_HTTP) FWP_HTTP.lang = '$lang';</script>";
        }
    }


    /**
     * Support FacetWP preloading (3.0.4+)
     */
    function support_preloader( $params ) {
        if ( isset( $params['is_preload'] ) && function_exists( 'pll_current_language' ) ) {
            $params['http_params']['lang'] = pll_current_language();
        }

        return $params;
    }


    /**
     * Query posts for the current language
     */
    function facetwp_query_args( $args, $class ) {
        if ( isset( $class->http_params['lang'] ) ) {
            $args['lang'] = $class->http_params['lang'];
        }

        return $args;
    }


    /**
     * Index all languages
     */
    function facetwp_indexer_query_args( $args ) {
        $args['lang'] = ''; // query posts in all languages
        return $args;
    }


    /**
     * Register dynamic strings
     */
    function register_strings() {
        $facets = FWP()->helper->get_facets();
        $whitelist = array( 'label', 'label_any', 'placeholder' );

        if ( ! empty( $facets ) ) {
            foreach ( $facets as $facet ) {
                foreach ( $whitelist as $k ) {
                    if ( ! empty( $facet[ $k ] ) ) {
                        pll_register_string( 'FacetWP', $facet[ $k ] );
                    }
                }
            }
        }
    }


    /**
     * Handle string translations
     */
    function facetwp_i18n( $string ) {
        $lang = pll_current_language();
        $default = pll_default_language();

        if ( isset( FWP()->facet->http_params['lang'] ) ) {
            $lang = FWP()->facet->http_params['lang'];
        }

        if ( $lang != $default ) {
            return pll_translate_string( $string, $lang );
        }

        return $string;
    }


    /**
     * Grab all taxonomy terms when indexing
     */
    function get_terms_args( $args ) {
        if ( '' !== get_option( 'facetwp_indexing', '' ) ) {
            $args['lang'] = '';
        }

        return $args;
    }
}

new FWP_i18n_Polylang();
