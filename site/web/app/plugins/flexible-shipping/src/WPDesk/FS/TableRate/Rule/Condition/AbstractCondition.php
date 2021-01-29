<?php
/**
 * Class AbstractCondition
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use Exception;
use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\Field\InputTextField;
use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\Renderer\JsonNormalizedRenderer;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Exception\ConditionInvalidNumberValue;
use WPDesk\FS\TableRate\Exception\ConditionRequiredField;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Abstract condition.
 */
abstract class AbstractCondition implements Condition, FieldProvider, JsonSerializable {

	/**
	 * @var string
	 */
	protected $condition_id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @return string
	 */
	public function get_condition_id() {
		return $this->condition_id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param ShippingContents $shipping_contents  .
	 * @param array            $condition_settings .
	 *
	 * @return ShippingContents
	 */
	public function process_shipping_contents( ShippingContents $shipping_contents, array $condition_settings ) {
		return $shipping_contents;
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents           .
	 * @param LoggerInterface  $logger             .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger ) {
		return false;
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return array();
	}

	/**
	 * @param array  $condition_settings .
	 * @param bool   $condition_matched  .
	 * @param string $input_data         .
	 *
	 * @return string
	 */
	protected function format_for_log( array $condition_settings, $condition_matched, $input_data ) {
		// Translators: condition name.
		$formatted_for_log = '   ' . sprintf( __( 'Condition: %1$s;', 'flexible-shipping' ), $this->get_name() );
		foreach ( $this->get_fields() as $field ) {
			$value = '';
			if ( isset( $condition_settings[ $field->get_name() ] ) ) {
				$value = $condition_settings[ $field->get_name() ];
			}
			$formatted_for_log .= sprintf( ' %1$s: %2$s;', $field->get_name(), is_array( $value ) ? implode( ', ', $value ) : $value );
		}
		// Translators: input data.
		$formatted_for_log .= sprintf( __( ' input data: %1$s;', 'flexible-shipping' ), $input_data );
		// Translators: matched condition.
		$formatted_for_log .= sprintf( __( ' matched: %1$s', 'flexible-shipping' ), $condition_matched ? __( 'yes', 'flexible-shipping' ) : __( 'no', 'flexible-shipping' ) );

		return $formatted_for_log;
	}

	/**
	 * @param array $condition_settings .
	 *
	 * @return array
	 */
	public function prepare_settings( $condition_settings ) {
		return $condition_settings;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$renderer = new JsonNormalizedRenderer();

		return array(
			'condition_id' => $this->get_condition_id(),
			'label'        => $this->get_name(),
			'parameters'   => $renderer->render_fields( $this, array() ),
		);
	}

}
