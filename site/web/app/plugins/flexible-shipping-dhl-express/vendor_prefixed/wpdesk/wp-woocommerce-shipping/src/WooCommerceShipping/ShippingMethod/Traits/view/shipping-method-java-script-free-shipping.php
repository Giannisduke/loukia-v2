<?php

namespace DhlVendor;

/**
 * @var string $settings_prefix
 */
?>
<script type="text/javascript">

	jQuery( document ).ready( function () {

		let $free_shipping_field_status = jQuery( '#<?php 
echo $settings_prefix;
?>_free_shipping_status' );

		$free_shipping_field_status.change( function () {
			let $free_shipping_field_amount = jQuery( '#<?php 
echo $settings_prefix;
?>_free_shipping_amount' );
			let $free_shipping_field_label = jQuery( '#<?php 
echo $settings_prefix;
?>_free_shipping_label' );

			if ( jQuery( this ).is( ':checked' ) && jQuery( this ).is( ':visible' ) ) {
				$free_shipping_field_amount.closest( 'tr' ).show();
				$free_shipping_field_amount.attr( 'required', true );

				$free_shipping_field_label.closest( 'tr' ).show();
			} else {
				$free_shipping_field_amount.closest( 'tr' ).hide();
				$free_shipping_field_amount.attr( 'required', false );

				$free_shipping_field_label.closest( 'tr' ).hide();
			}
		} );

		if ( $free_shipping_field_status.length ) {
			$free_shipping_field_status.change();
		}

	} );

</script>
<?php 
