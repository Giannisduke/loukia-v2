<?php

namespace DhlVendor;

/**
 * Shipping method settings template.
 *
 * @package WPDesk\WooCommerceShipping
 */
/**
 * Params.
 *
 * @var string $settings_html .
 * @var string $service_id .
 */
$params = isset($params) ? $params : array();
?>
<div class="wrap">
	<div class="flexible-shipping-settings">
		<div class="flexible-shipping-main-content" style="height: auto;">
			<table class="form-table">
				<?php 
echo $settings_html;
//phpcs:ignore
?>
			</table>
		</div>

		<div class="flexible-shipping-sidebar" style="height: auto;">
			<?php 
\do_action("{$service_id}_settings_sidebar");
?>
		</div>
	</div>
</div>
<?php 
