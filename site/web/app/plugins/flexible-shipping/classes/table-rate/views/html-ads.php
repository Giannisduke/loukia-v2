<?php if ( ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' ) ) : ?>
<div class="fs-flexible-shipping-sidebar" style="height: auto;">
    <div class="wpdesk-metabox">
        <div class="wpdesk-stuffbox">
            <h3 class="title"><?php _e( 'Get Flexible Shipping PRO!', 'flexible-shipping' ); ?></h3>
            <?php
                $fs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/flexible-shipping-pro-woocommerce/' : 'https://flexibleshipping.com/products/flexible-shipping-pro-woocommerce/';
                $utm = get_locale() === 'pl_PL' ? '?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=button&utm_term=upgrade-now&utm_content=fs-shippingzone-upgradenow' : '?utm_source=fs-settings&utm_medium=link&utm_campaign=settings-upgrade-link';
            ?>

            <div class="inside">
                <div class="main">
                    <ul>
                        <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Shipping Classes support', 'flexible-shipping' ); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Product count based costs', 'flexible-shipping' ); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Stopping, Cancelling a rule', 'flexible-shipping' ); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php _e( 'Additional calculation methods', 'flexible-shipping' ); ?></li>
                    </ul>

                    <a class="button button-primary" href="<?php echo esc_attr( $fs_link . $utm ); ?>" target="_blank"><?php _e( 'Upgrade Now &rarr;', 'flexible-shipping' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="clear"></div>