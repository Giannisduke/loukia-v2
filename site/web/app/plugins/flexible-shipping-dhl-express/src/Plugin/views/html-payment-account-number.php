<?php
/**
 * Payment account number script.
 *
 * @package WPDesk\FlexibleShippingDhl
 */

?><script type="text/javascript">
	jQuery(document).ready(function(){
		let $use_payment_account_number = jQuery('#woocommerce_flexible_shipping_dhl_express_use_payment_account_number');

		function update_payment_account_number_visibility() {
			let use_payment_account_number_checked = $use_payment_account_number.is(':checked');
			let $payment_account_number = jQuery('#woocommerce_flexible_shipping_dhl_express_payment_account_number');
			$payment_account_number.closest('tr').toggle(use_payment_account_number_checked);
			$payment_account_number.prop('required',use_payment_account_number_checked);
		}

		$use_payment_account_number.change(function() {
			update_payment_account_number_visibility();
		});

		update_payment_account_number_visibility();
	});
</script>
