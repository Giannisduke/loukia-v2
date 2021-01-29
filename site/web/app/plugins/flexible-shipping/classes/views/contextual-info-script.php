<?php
/**
 * Display contextual info script.
 *
 * @package Contextual Info.
 *
 * @var $html_elements_ids string
 * @var $info_id string
 * @var $phrases_in string[]
 * @var $info_html string
 * @var $phrases_not_in string[]
 */

?><script type="text/javascript">
	jQuery( document ).ready(
		function(){
			jQuery( "<?php echo esc_attr( $html_elements_ids ); ?>" ).contextualInfo(
				{
					'id': '<?php echo esc_attr( $info_id ); ?>',
					'phrases_in': <?php echo json_encode( $phrases_in ); // phpcs:ignore ?>,
					'info_html': <?php echo json_encode( $info_html ); // phpcs:ignore ?>,
					'phrases_not_in': <?php echo json_encode( $phrases_not_in ); // phpcs:ignore ?>
				}
			);
		}
	);
</script>
