<?php
/**
 * Displays Beacon script.
 *
 * @package WPDesk\FlexibleShippingDhl
 *
 * @var $button_image_src string .
 * @var $beacon_id string .
 */

?><div id="wpdesk-helpscout-beacon">
	<div class="wpdesk-helpscout-beacon-frame">
		<input type="image" src="<?php echo esc_html( $button_image_src ); ?>" class="wpdesk-helpscout-beacon-button" style="position: fixed; bottom: 37px; right: 37px; outline: none;" />
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.wpdesk-helpscout-beacon-button').click(function () {
			jQuery(this).blur();
			if (confirm('<?php echo esc_attr( __( 'When you click OK we will open our HelpScout beacon where you can find answers to your questions. This beacon will load our support data and also potentially set cookies.', 'flexible-shipping-dhl-express' ) ); ?>')) {
				!function (e, t, n) {
					function a() {
						var e = t.getElementsByTagName("script")[0], n = t.createElement("script");
						n.type = "text/javascript", n.async = !0, n.src = "https://beacon-v2.helpscout.net", e.parentNode.insertBefore(n, e)
					}

					if (e.Beacon = n = function (t, n, a) {
						e.Beacon.readyQueue.push({method: t, options: n, data: a})
					}, n.readyQueue = [], "complete" === t.readyState) return a();
					e.attachEvent ? e.attachEvent("onload", a) : e.addEventListener("load", a, !1)
					}(window, document, window.Beacon || function () {
				});
				window.Beacon('init', '<?php echo esc_attr( $beacon_id ); ?>');
				window.Beacon('open');
				jQuery(this).fadeOut("slow");
			}
		})
	});
</script>
