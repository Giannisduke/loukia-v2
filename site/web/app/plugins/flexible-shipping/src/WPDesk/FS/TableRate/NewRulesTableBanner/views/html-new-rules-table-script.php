<?php
/**
 * @var string $banner_file
 * @var string $trigger
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

?><script type="text/javascript">
	jQuery(document).ready(function () {

		var trigger = '<?php echo esc_attr( $trigger ); ?>';

		jQuery(".flexible_shipping_method_rules:first").after(<?php
			ob_start();
			include $banner_file;
			$banner = ob_get_clean();
			echo json_encode( $banner );
			?>
		);

		if (jQuery('#fs_new_rules_table_banner_for_new_table').length) {
			jQuery("body,html").animate(
				{
					scrollTop: jQuery("#fs_new_rules_table_banner_for_new_table").offset().top
				},
				800 //speed
			);
		}

		jQuery(document).on('click', '#fs_new_rules_table_banner button.show-new-rules-table', function (e) {
			e.preventDefault();
			window.open('<?php echo admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . sanitize_key( $_GET['instance_id'] ) . '&method_id=' . isset( $_GET['method_id'] ) ? sanitize_key( $_GET['method_id'] ) : '' . '&action=edit&' . \WPDesk\FS\TableRate\NewRulesTableBanner\RulesPointerBannerForOldTable::NEW_RULES_TABLE_PARAMETER . '=1' ); ?>');
		});

		jQuery(document).on('click', '#fs_new_rules_table_banner_for_new_table button.i-like-it', function (e) {
			e.preventDefault();
			jQuery(this).attr('disabled', 'disabled');
			jQuery('#fs_new_rules_table_banner_for_new_table button.i-dont-like-it').attr('disabled', 'disabled');

			jQuery.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'POST',
				data: {
					action: '<?php echo \WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerLikeOption::AJAX_ACTION; ?>',
					security: '<?php echo wp_create_nonce( \WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerLikeOption::AJAX_ACTION ); ?>'
				},
				success: function (response) {
					jQuery('#fs_new_rules_table_banner_for_new_table').closest('tr').remove();
				}
			});
		});

		jQuery(document).on('click', '#fs_new_rules_table_banner_for_new_table button.i-dont-like-it', function (e) {
			e.preventDefault();
			jQuery(this).attr('disabled', 'disabled');
			jQuery('#fs_new_rules_table_banner_for_new_table button.i-like-it').attr('disabled', 'disabled');

			jQuery.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'POST',
				data: {
					action: '<?php echo \WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerDontLikeOption::AJAX_ACTION; ?>',
					security: '<?php echo wp_create_nonce( \WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerDontLikeOption::AJAX_ACTION ); ?>'
				},
				success: function (response) {
					window.location.reload();
				}
			});
		});

	});
</script>
