<?php

class FacetWP_Integration_EDD
{

    public $query_found = false;


    function __construct() {
        add_filter( 'facetwp_facet_sources', [ $this, 'exclude_data_sources' ] );
        add_filter( 'edd_downloads_query', [ $this, 'edd_downloads_query' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'front_scripts' ] );
    }


    /**
     * Trigger some EDD code on facetwp-loaded
     * @since 2.0.4
     */
    function front_scripts() {
        FWP()->display->assets['edd.js'] = FACETWP_URL . '/includes/integrations/edd/edd.js';
    }


    /**
     * Intercept EDD's [downloads] shortcode
     * @since 2.0.4
     */
    function edd_downloads_query( $query ) {
        if ( ! empty( FWP()->facet->query_args ) && 'wp' == FWP()->facet->template['name'] && ! $this->query_found ) {
            $query = array_merge( $query, FWP()->facet->query_args );
            $this->query_found = true;
        }

        return $query;
    }


    /**
     * Exclude specific EDD custom fields
     * @since 2.4
     */
    function exclude_data_sources( $sources ) {
        $prefixes = [ '_edd_discount', '_edd_log', '_edd_payment' ];
        foreach ( $sources['custom_fields']['choices'] as $key => $val ) {
            foreach ( $prefixes as $prefix ) {
                if ( 0 === strpos( $val, $prefix ) ) {
                    unset( $sources['custom_fields']['choices'][ $key ] );
                }
            }
        }

        return $sources;
    }
}


if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
    new FacetWP_Integration_EDD();
}
