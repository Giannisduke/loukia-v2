<?php /** @noinspection PhpUnusedPrivateMethodInspection, PhpUndefinedMethodInspection, PhpUnused, PhpUnusedPrivateFieldInspection, PhpUnusedLocalVariableInspection, DuplicatedCode, PhpUnusedParameterInspection, PhpForeachNestedOuterKeyValueVariablesConflictInspection, RegExpRedundantEscape */

/**
 * Class Google Product Review
 *
 * Responsible for processing and generating feed for Google.com
 *
 * @since 1.0.0
 * @package Google
 *
 */
class Woo_Feed_Review {

    /**
     * This variable is responsible for holding all product attributes and their values
     *
     * @since   1.0.0
     * @var     array $products Contains all the product attributes to generate feed
     * @access  public
     */
    public $products;

    /**
     * This variable is responsible for holding feed configuration form values
     *
     * @since   1.0.0
     * @var     array $rules Contains feed configuration form values
     * @access  public
     */
    public $rules;

    /**
     * This variable is responsible for mapping store attributes to merchant attribute
     *
     * @since   1.0.0
     * @var     array $mapping Map store attributes to merchant attribute
     * @access  public
     */
    public $mapping;

    /**
     * This variable is responsible for generate error logs
     *
     * @since   1.0.0
     * @var     array $errorLog Generate error logs
     * @access  public
     */
    public $errorLog;

    /**
     * This variable is responsible for making error number
     *
     * @since   1.0.0
     * @var     int $errorCounter Generate error number
     * @access  public
     */
    public $errorCounter;

    /**
     * Feed Wrapper text for enclosing each product information
     *
     * @since   1.0.0
     * @var     string $feedWrapper Feed Wrapper text
     * @access  public
     */
    public $feedWrapper = 'review';

    /**
     * Store product information
     *
     * @since   1.0.0
     * @var     array $storeProducts
     * @access  public
     */
    private $storeProducts;

    /**
     * Define the core functionality to generate feed.
     *
     * Set the feed rules. Map products according to the rules and Check required attributes
     * and their values according to merchant specification.
     * @var Woo_Generate_Feed $feedRule Contain Feed Configuration
     * @since    1.0.0
     */
    public function __construct( $feedRule ) {
        $feedRule['itemWrapper'] = $this->feedWrapper;
        $this->products          = new Woo_Feed_Products_v3( $feedRule );
        // When update via cron job then set productIds.
        if ( ! isset( $feedRule['productIds'] ) ) {
            $feedRule['productIds'] = $this->products->query_products();
        }
        $this->products->get_products( $feedRule['productIds'] );
        $this->rules = $feedRule;
    }


    /**
     * Return Feed
     *
     * @return array|bool|string
     */
    public function returnFinalProduct() {
        if ( ! empty( $this->products ) ) {

            if ( 'xml' == $this->rules['feedType'] ) {
                // return $this->get_feed($this->products);
                $feed = array(
                    'header' => $this->get_xml_feed_header(),
                    'body'   => $this->get_xml_feed_body(),
                    'footer' => $this->get_xml_feed_footer(),
                );

                return $feed;
            }
        }

        $feed = array(
            'body'   => '',
            'header' => '',
            'footer' => '',
        );

        return $feed;
    }



    /**
     * Make xml node
     *
     * @param string $attribute Attribute Name
     * @param string $value Attribute Value
     * @param bool   $cdata
     * @param string $space
     *
     * @return string
     */
    function formatXMLLine( $attribute, $value, $cdata, $space = '' ) {
        // Make single XML  node
        if ( ! empty( $value ) ) {
            $value = trim( $value );
        }
        if ( gettype( $value ) == 'array' ) {
            $value = wp_json_encode( $value );
        }
        if ( false === strpos( $value, '<![CDATA[' ) && 'http' === substr( trim( $value ), 0, 4 ) ) {
            $value = "<![CDATA[$value]]>";
        } elseif ( false === strpos( $value, '<![CDATA[' ) && true === $cdata && ! empty( $value ) ) {
            $value = "<![CDATA[$value]]>";
        } elseif ( $cdata ) {
            if ( ! empty( $value ) ) {
                $value = "<![CDATA[$value]]>";
            }
        }

        if ( 'g:additional_image_link' == substr( $attribute, 0, 23 ) ) {
            $attribute = 'g:additional_image_link';
        }

        return "$space<$attribute>$value</$attribute>";
    }


    /**
     * Make XML Feed Header
     * @return string
     */
    public function get_xml_feed_header() {
        $output = '<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.google.com/shopping/reviews/schema/product/2.3/product_reviews.xsd">
<publisher><name>' . html_entity_decode( get_option( 'blogname' ) ) . '</name></publisher>
  <reviews>';

        return $output;
    }

    /**
     * Make XML Feed
     * @param array $items items.
     * @return string
     */
    public function get_xml_feed_body() {
        $review_data = woo_feed_get_approved_reviews_data();

        $feed = $this->create_xml_lines($review_data);

        //$feed = '<reviewer_data></reviewer_data>';
        //if ( $review_data ) {
            //$this->create_xml_lines($review_data);

            return $feed;
        //}

    }

    /**
     * Make XML Feed Footer
     * @return string
     */
    public function get_xml_feed_footer() {
        $footer = '  </reviews>
</feed>';

        return $footer;
    }

    /**
     * Make XML Feed Body
     * @param array $data review product array
     *
     * @return string
     */
    public function create_xml_lines( $data ) {
        $output = '';

        if ( ! empty($data) && is_array($data) ) {
            foreach ( $data as $data_item_key => $data_item ) {
                $chunk_data = array_chunk($data_item, 1, true);
                $output .= '<review>';
                foreach ( $chunk_data as $key => $value ) {
                    foreach ( $value as $item_key => $item_value ) {
                        if ( is_array($item_value) ) {
                            if ( is_int($item_key) ) {
                                $output .= '</'. key($value) .'>';
                                $output .= '<'. key($value) .'>';
                            }else {
                                $output .= '<'. $item_key .'>';
                            }
                        }else {
                            $output .= '<'. $item_key .'>';
                        }
                        if ( is_array($item_value) ) {
                            foreach ( $item_value as $item_value2_key => $item_value2 ) {
                                if ( is_array($item_value2) ) {
                                    if ( is_int($item_value2_key) ) {
                                        $output .= '</'. key($item_value) .'>';
                                        $output .= '<'. key($item_value) .'>';
                                    }else {
                                        $output .= '<'. $item_value2_key .'>';
                                    }
                                }else {
                                    $output .= '<'. $item_value2_key .'>';
                                }
                                if ( is_array($item_value2) ) {

                                    foreach ( $item_value2 as $item_value3_key => $item_value3_value ) {
                                        if ( is_array($item_value3_value) ) {
                                            if ( is_int($item_value3_key) ) {
                                                $output .= '</'. key($item_value2) .'>';
                                                $output .= '<'. key($item_value2) .'>';
                                            }else {
                                                $output .= '<'. $item_value3_key .'>';
                                            }
                                        }else {
                                            $output .= '<'. $item_value3_key .'>';
                                        }
                                        if ( is_array($item_value3_value) ) {
                                            foreach ( $item_value3_value as $item_value4_key => $item_value4_value ) {
                                                if ( is_array($item_value4_value) ) {
                                                    if ( is_int($item_value4_key) ) {
                                                        $output .= '</'. key($item_value2) .'>';
                                                        $output .= '<'. key($item_value2) .'>';
                                                    }else {
                                                        $output .= '<'. $item_value4_key .'>';
                                                    }
                                                }else {
                                                    $output .= '<'. $item_value4_key .'>';
                                                }

                                                if ( is_array($item_value4_value) ) {
                                                    foreach ( $item_value4_value as $item_value5_key => $item_value5_value ) {
                                                        if ( is_array($item_value5_value) ) {
                                                            if ( is_int($item_value5_key) ) {
                                                                $output .= '</'. key($item_value3_value) .'>';
                                                                $output .= '<'. key($item_value3_value) .'>';
                                                            }else {
                                                                $output .= '<'. $item_value5_key .'>';
                                                            }
                                                        }else {
                                                            $output .= '<'. $item_value5_key .'>';
                                                        }

                                                        //test end
                                                        if ( is_array($item_value5_value) ) {
                                                            if ( is_int($item_value5_key) ) {
                                                                $output .= '</'. key($item_value3_value) .'>';
                                                            }else {
                                                                $output .= '</'. $item_value5_key .'>';
                                                            }
                                                        }else {
                                                            $output .= '</'. $item_value5_key .'>';
                                                        }
                                                    }
                                                }else {
                                                    $output .= $item_value4_value;
                                                }

                                                if ( is_array($item_value4_value) ) {
                                                    if ( is_int($item_value4_key) ) {
                                                        $output .= '</'. key($item_value2) .'>';
                                                    }else {
                                                        $output .= '</'. $item_value4_key .'>';
                                                    }
                                                }else {
                                                    $output .= '</'. $item_value4_key .'>';
                                                }
                                            }
                                        }else {
                                            $output .= $item_value3_value;
                                        }
                                        if ( is_array($item_value3_value) ) {
                                            if ( is_int($item_value3_key) ) {
                                                $output .= '</'. key($item_value2) .'>';
                                            }else {
                                                $output .= '</'. $item_value3_key .'>';
                                            }
                                        }else {
                                            $output .= '</'. $item_value3_key .'>';
                                        }
                                    }
                                }else {
                                    $output .= $item_value2;
                                }
                                if ( is_array($item_value2) ) {
                                    if ( is_int($item_value2_key) ) {
                                        $output .= '</'. key($item_value) .'>';
                                    }else {
                                        $output .= '</'. $item_value2_key .'>';
                                    }
                                }else {
                                    $output .= '</'. $item_value2_key .'>';
                                }
                            }
                        }else {
                            $output .= $item_value;
                        }
                        if ( is_array($item_value) ) {
                            if ( is_int($item_key) ) {
                                $output .= '</'. key($value) .'>';
                            }else {
                                $output .= '</'. $item_key .'>';
                            }
                        }else {
                            $output .= '</'. $item_key .'>';
                        }
                    }
                }
                $output .= '</review>';
            }
        }

        return $output;
    }

    /**
     * Short Products
     * @return array
     */
    public function short_products() {
        if ( $this->products ) {
            update_option( 'wpf_progress', esc_html__('Shorting Products', 'woo-feed' ), false );
            sleep( 1 );
            $array = array();
            $ij    = 0;
            foreach ( $this->products as $key => $item ) {
                $array[ $ij ] = $item;
                unset( $this->products[ $key ] );
                $ij ++;
            }

            return $this->products = $array;
        }

        return $this->products;
    }
}
