<?php /** @noinspection PhpUnusedPrivateMethodInspection, PhpUndefinedMethodInspection, PhpUnused, PhpUnusedPrivateFieldInspection, PhpUnusedLocalVariableInspection, DuplicatedCode, PhpUnusedParameterInspection, PhpForeachNestedOuterKeyValueVariablesConflictInspection, RegExpRedundantEscape */

/**
 * Class Google
 *
 * Responsible for processing and generating feed for Google.com
 *
 * @since 1.0.0
 * @package Google
 *
 */
class Woo_Feed_Google {
	
	/**
	 * This variable is responsible for holding all product attributes and their values
	 *
	 * @since   1.0.0
	 * @var     Woo_Feed_Products_v3 $products Contains all the product attributes to generate feed
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
	public $feedWrapper = 'item';
	
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
			// @TODO use limit for free version here for cron calls... {google,facebook,pinterest,custom}
			$feedRule['productIds'] = $this->products->query_products();
		}
		$this->products->get_products( $feedRule['productIds'] );
		$this->rules = $feedRule;
	}
	
	
	/**
	 * Return Feed
	 * @return array
	 */
	public function returnFinalProduct() {
		if ( ! empty( $this->products ) ) {
			if ( 'xml' == $this->rules['feedType'] ) {
				$feed = array(
					'body'   => $this->products->feedBody,
					'header' => $this->products->feedHeader,
					'footer' => $this->products->feedFooter,
				);
				
				return $feed;
			} elseif ( 'txt' == $this->rules['feedType'] ) {
				$feed = array(
					'body'   => $this->products->feedBody,
					'header' => $this->products->feedHeader,
					'footer' => '',
				);
				return $feed;
			} elseif ( 'csv' == $this->rules['feedType'] || 'tsv' == $this->rules['feedType'] || 'xls' == $this->rules['feedType'] ) {
				$feed = array(
					'body'   => $this->products->feedBody,
					'header' => $this->products->feedHeader,
					'footer' => '',
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
	


}
