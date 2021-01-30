<?php
class Woo_Feed_Shipping {
	/**
	 * @var WC_Product $product Contain product object.
	 */
	private $product;

    /**
     * @var array $settings Contain plugin setting.
     */
    private $settings;

    /**
     * @var string $currency Currency sign like `USD`, `EUR`.
     */
    private $currency;

	/**
	 * @var string $class_cost_id Shipping class cost id.
	 */
	private $class_cost_id;
	/**
	 * @var array $shipping_zones Contain Shipping Zone info.
	 */
	private $shipping_zones;
	/**
	 * @var string $country
	 */
	private $feed_country;
	/**
	 * @var array $config Contain feed configuration.
	 */
	private $config;

	public function __construct( $product, $feed_config ) {
		$this->config         = $feed_config;
		$this->product        = $product;
		$this->class_cost_id  = $this->set_shipping_class_id();
		$this->shipping_zones = $this->set_shipping_zone();
		$this->feed_country   = $this->set_country();
        $this->settings       = woo_feed_get_options( 'all' );
        $this->currency       = $this->get_currency();
	}


	private function set_country() {
		return $this->config['feed_country'];
	}

    /**
     * Get current feed currency.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return string $currency feed currency
     */
	private function get_currency() {
	    $currency           = '';
        $mattributes        = $this->config['mattributes'];
        $price_index        = array_search('price', $mattributes);
        $sale_price_index    = array_search('sale_price', $mattributes);

        //when feedCurrency is set to feed config setting, bring it or look at the prices suffix values.
	    if ( isset($this->config['feedCurrency']) ) {
	        $currency = $this->config['feedCurrency'];
        }else {
            if ( isset($price_index) && ! empty($price_index) ) {
                $currency = $this->config['suffix'][ $price_index ];

                //when price attribute's suffix value is empty, check and bring sale_price attribute's suffix value if exists.
                if ( empty($currency) && ! empty($sale_price_index) ) {
                    $currency = $this->config['suffix'][ $sale_price_index ];
                }
            }
        }

	    return $currency;
    }

	private function set_shipping_class_id() {
		$class_cost_id = "class_cost_" . $this->product->get_shipping_class_id();
		if ( $class_cost_id == "class_cost_0" ) {
			return 'no_class_cost';
		}

		return $class_cost_id;
	}

	public function set_shipping_zone() {
        if ( class_exists( 'WC_Shipping_Zones' ) ) {
            return WC_Shipping_Zones::get_zones();
        }
        return false;
	}

    /**
     * Get shipping information.
     *
     * @since 5.2.0
     * @return array $shipping_info shipping information
     */
	public function get_shipping() {
		$shipping_info = [];

		foreach ( $this->shipping_zones as $zone ) {
			$shipping = [];
			foreach ( $zone['zone_locations'] as $zone_type ) {
				if ( $zone_type->type == "country" ) {
					// This is a country shipping zone
					$shipping['country'] = $zone_type->code;
				} elseif ( $zone_type->type == "code" ) {
					// This is a country shipping zone
					$shipping['country'] = $zone_type->code;
				} elseif ( $zone_type->type == "state" ) {
					// This is a state shipping zone, split of country
					$zone_explode        = explode( ":", $zone_type->code );
					$shipping['country'] = $zone_explode[0];

					//TODO Adding a region is only allowed for these countries
					$region_countries = array( 'US', 'JP', 'AU' );
					$shipping['region'] = $zone_explode[1];

				} elseif ( $zone_type->type == "postcode" ) {
					// Create an array of postal codes so we can loop over it later
					$zone_type->code         = str_replace( "...", "-", $zone_type->code );
					$shipping['postal_code'] = $zone_type->code;
				}

                //when allow_all_shipping is disabled and feed country doesn't match shipping country then skip the shipping method
                if ( isset($this->settings) && is_array($this->settings) ) {
                    if ( array_key_exists('allow_all_shipping', $this->settings) ) {
                        $all_country_shipping = $this->settings['allow_all_shipping'];
                        if ( ! empty($all_country_shipping) && 'no' === $all_country_shipping ) {
                            if ( $shipping['country'] !== $this->feed_country ) {
                                unset($shipping);
                                continue 2;
                            }
                        }
                    }
                }


				$shipping_methods = $zone['shipping_methods'];

                // Continue loop if no shipping method defined.
                if ( empty($shipping_methods) ) {
                    unset($shipping);
                    continue 2;
                }


				foreach ( $shipping_methods as $method ) {
					if ( 'yes' === $method->enabled ) {

                        //when only_free_shipping is enabled and shipping is not free shipping then skip the shipping method
//                        if( isset($this->settings) && is_array($this->settings) ) {
//                            if( array_key_exists('only_free_shipping', $this->settings) ) {
//                                $only_free_shipping = $this->settings['only_free_shipping'];
//                                if( empty($only_free_shipping) || 'yes' === $only_free_shipping ) {
//                                    if ( "free_shipping" !== $method->id ) {
//                                        unset( $shipping );
//                                        continue;
//                                    }
//                                }
//                            }
//                        }

//                        if ($method->id == "free_shipping") {
//                            $minimum_fee = $method->min_amount;
//
//                            // Set type to double otherwise the >= doesn't work
//                            settype($minimum_fee, "double");
//
//                            // Only Free Shipping when product price is over or equal to minimum order fee
//                            if ($this->product->get_price() >= $minimum_fee) {
//                                $shipping['free'] = "yes";
//                            } else {
//                                // There are no free shipping requirements
//                                if ($method->requires === "") {
//                                    $shipping['free'] = "yes";
//                                } else {
//                                    // No Free Shipping Allowed for this product
//                                    //unset($zone_details);
//                                    unset($shipping['service']);
//                                    unset($shipping['price']);
//                                    unset($shipping);
//                                }
//                            }
//                        }


						if ( empty( $shipping['country'] ) ) {
							$shipping['service'] = $zone['zone_name'] . " " . $method->title;
						} else {
							$shipping['service'] = $zone['zone_name'] . " " . $method->title . " " . $shipping['country'];
						}

                        //when only_local_pickup_shipping is disabled then skip the local pickup method
                        if ( $method->id === "local_pickup" ) {
                            if ( isset($this->settings) && is_array($this->settings) ) {
                                if ( array_key_exists('only_local_pickup_shipping', $this->settings) ) {
                                    $allow_local_pickup = $this->settings['only_local_pickup_shipping'];
                                    if ( ! empty($allow_local_pickup) && 'no' === $allow_local_pickup ) {
                                        unset( $shipping );
                                        continue 3;
                                    }
                                }
                            }
                        }
					}else {
                        unset( $shipping );
                        continue 3;
                    }

					$shipping_cost = $this->get_shipping_cost($shipping);
					$shipping['price'] = (string) $shipping_cost;

				}
				$shipping_info[] = $shipping;
			}
		}

        return $shipping_info;
	}


    /**
     * Get shipping cost.
     *
     * @param $shipping array shipping information
     *
     * @since 5.2.0
     * @return mixed $shipping_cost shipping cost
     */
	private function get_shipping_cost( $shipping ) {
		// Set shipping cost
		$shipping_cost = 0;
		$tax = 0;
		defined( 'WC_ABSPATH' ) || exit;

		// Load cart functions which are loaded only on the front-end.
		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';

		wc_load_cart();
		global $woocommerce;

		// Make sure to empty the cart again
		$woocommerce->cart->empty_cart();

		// Set Shipping Country.
		if ( isset($shipping['country']) and ! empty($shipping['country']) ) {
			$woocommerce->customer->set_shipping_country( $shipping['country'] );
		}
		// Set Shipping Region.
		if ( isset($shipping['region']) and ! empty($shipping['region']) ) {
			$woocommerce->customer->set_shipping_state( $shipping['region'] );
		}else {
			$woocommerce->customer->set_shipping_state("");
		}

		$woocommerce->cart->add_to_cart( $this->product->get_id(), 1 );

		// Read cart and get shipping costs
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
			$shipping_cost = $woocommerce->cart->get_shipping_total();
			$tax = $woocommerce->cart->get_shipping_tax();
		}

		$shipping_cost = $shipping_cost + $tax;

		// Make sure to empty the cart again
		$woocommerce->cart->empty_cart();

		return $shipping_cost;
	}

    /**
     * Get lowest shipping price.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return mixed
     */
	public function get_lowest_shipping_price() {
		return min( wp_list_pluck( $this->get_shipping(), 'price' ) );
	}

    /**
     * Get highest shipping price.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return mixed
     */
	public function get_highest_shipping_price() {
		return max( wp_list_pluck( $this->get_shipping(), 'price' ) );
	}

    /**
     * Get first shipping price.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return mixed
     */
	public function get_first_shipping_price() {
		$shipping_prices = wp_list_pluck( $this->get_shipping(), 'price' );

		return reset( $shipping_prices );
	}

    /**
     * Get google shipping feed structure for XML/CSV feed type.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return string $feedBody shipping feed structure
     */
    public function get_google_shipping() {
        $shipping_arr = $this->get_shipping();
        $feedBody = "";

        if ( "xml" === $this->config['feedType'] ) {
            $shipping_label = 'g:shipping';

            if ( isset($shipping_arr) && is_array($shipping_arr) ) {
                $feedBody .= "<$shipping_label>"; //start shipping label
                $i = 0;
                $len = count($shipping_arr);

                foreach ( $shipping_arr as $k => $shipping_item ) {
                    $shipping_child = '';
                    foreach ( $shipping_item as $shipping_item_attr => $shipping_value ) {
                        if ( "price" === $shipping_item_attr ) {
                            $shipping_value = $shipping_value . ' ' . $this->currency;
                        }

                        $shipping_child  .= '<' . $shipping_item_attr . '>' . $shipping_value . '</' . $shipping_item_attr . '>';
                        $shipping_child = stripslashes( $shipping_child );
                    }

                    // when loop is in last ride skip inserting labels, as it insert label with empty value
                    $feedBody .= "$shipping_child";
                    if ( isset($shipping_child) && ! empty($shipping_child) && $i !== $len - 1 ) {
                        $feedBody .= "</$shipping_label>";
                        $feedBody .= "\n";
                        $feedBody .= "<$shipping_label>";
                    }

                    unset($shipping_child); //unset for not to join all item together
                    $i++;
                }

                $feedBody .= "</$shipping_label>"; //end shipping label
                $feedBody .= "\n";
            }        
}elseif ( in_array($this->config['feedType'], [ 'csv', 'tsv', 'xls' ]) ) {

            if ( isset($shipping_arr) && is_array($shipping_arr) ) {
                foreach ( $shipping_arr as $k => $shipping_item ) {
                    $shipping_child = '';
                    foreach ( $shipping_item as $shipping_item_attr => $shipping_value ) {
                        if ( "price" === $shipping_item_attr ) {
                            $shipping_value = $shipping_value . ' ' . $this->currency;
                        }
                        
                        if ( "postal_code" !== $shipping_item_attr ) {
                            $shipping_child  .= $shipping_value . ":";
                        }
                    }
                    $shipping_child = trim( $shipping_child, ":" );

                    //add separator for multiple shipping method
                    $feedBody .= $shipping_child . '||';
                }

                //trim last extra sign
                $feedBody = trim($feedBody, '||');
            }
        }
        
        return $feedBody;
    }


    /**
     * Get google tax feed structure for XML/CSV feed type.
     *
     * @since 5.2.0
     * @author Nazrul Islam Nayan
     * @return string $feedBody tax feed structure
     */
    public function get_google_tax() {
        $feedBody = "";
        $tax_rates = $this->get_tax_rates();

        if ( "xml" === $this->config['feedType'] ) {
            if ( isset($tax_rates) && is_array($tax_rates) ) {
                $tax_label = "g:tax";
                $feedBody .= "<$tax_label>"; //start tax label
                $i = 0;
                $len = count($tax_rates);

                foreach ( $tax_rates as $k => $tax_item ) {
                    $tax_child = '';
                    foreach ( $tax_item as $tax_item_attr => $tax_value ) {
                        if ( "rate" === $tax_item_attr ) {
                            $tax_value = $tax_value . ' ' . $this->currency;
                        }

                        $tax_child  .= '<' . $tax_item_attr . '>' . "$tax_value" . '</' . $tax_item_attr . '>';
                        $tax_child = stripslashes( $tax_child );
                    }

                    // Strip slash from output
                    // when loop is in last ride skip inserting labels, as it insert label with empty value
                    $feedBody .= "$tax_child";
                    if ( isset($tax_child) && ! empty($tax_child) && $i !== $len - 1 ) {
                        $feedBody .= "</$tax_label>";
                        $feedBody .= "\n";
                        $feedBody .= "<$tax_label>";
                    }

                    unset($tax_child); //unset for not to join all item together
                    $i++;
                }

                $feedBody .= "</$tax_label>"; //end tax label
                $feedBody .= "\n";

            }
        }elseif ( in_array($this->config['feedType'], [ 'csv', 'tsv', 'xls' ]) ) {
            if ( isset($tax_rates) && is_array($tax_rates) ) {
                foreach ( $tax_rates as $k => $tax_item ) {
                    $tax_child = '';
                    foreach ( $tax_item as $tax_item_attr => $tax_value ) {
                        if ( "rate" === $tax_item_attr ) {
                            $tax_value = $tax_value . ' ' . $this->currency;
                        }

                        if ( "postal_code" !== $tax_item_attr ) {
                            $tax_value = ! empty($tax_value) ? $tax_value : '';
                            $tax_child  .= $tax_value . ":";
                        }
                    }
                    $tax_child = trim( $tax_child, ":" );

                    //add separator for multiple tax
                    $feedBody .= $tax_child . '||';
                }

                //trim last extra sign
                $feedBody = trim($feedBody, '||');
            }
        }

        return $feedBody;
    }

    /**
     * Get tax rates.
     *
     * @since 5.2.0
     * @return mixed
     */
    public function get_tax_rates() {
        // Skip if product is not taxable.
        if ( ! $this->product->is_taxable() ) {
            return "";
        }

        $all_tax_rates = [];
        $formatted_rates = [];

        // Retrieve all tax classes.
        $tax_classes = WC_Tax::get_tax_classes();

        // Make sure "Standard rate" (empty class name) is present.
        if ( ! in_array('', $tax_classes) ) {
            array_unshift($tax_classes, '');
        }

        // For each tax class, get all rates.
        foreach ( $tax_classes as $tax_class ) {
            $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
            $all_tax_rates = array_merge($all_tax_rates, $taxes);
        }

        if ( ! empty($all_tax_rates) ) {
            foreach ( $all_tax_rates as $rate_key => $rate ) {
                $formatted_rates[ $rate_key ]['country'] = $rate->tax_rate_country;
                $formatted_rates[ $rate_key ]['region'] = $rate->tax_rate_state;
                $formatted_rates[ $rate_key ]['rate'] = $rate->tax_rate;
                $formatted_rates[ $rate_key ]['tax_ship'] = ($rate->tax_rate_shipping) ? 'yes' : 'no';
            }
        }

        return $formatted_rates;
    }
}