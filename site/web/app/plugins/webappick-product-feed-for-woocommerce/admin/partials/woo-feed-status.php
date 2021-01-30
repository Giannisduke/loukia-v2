<?php
/**
 * Status Page
 *
 * @link       https://webappick.com/
 * @since      5.1.7
 * @version    5.1.6
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */
?>
<div class="wrap wapk-admin">
    <div class="wapk-section">
        <h1 class="wp-heading-inline"><?php _e( 'System Status', 'woo-feed' ); ?></h1>
        <hr class="wp-header-end">
        <?php WPFFWMessage()->displayMessages(); ?>
        <div class="woo-feed-status-table-wrapper">
            <table class="woo-feed-status-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Environment', 'woo-feed'); ?></th>
                        <th><?php esc_html_e('Value', 'woo-feed'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $system_datas = woo_feed_get_system_status();
                    foreach ( $system_datas as $key => $value ) {
                        $system_name = array_key_first($value);

                        //for debug mode rename the value
                        if ( "wp_debug_mode" === $key ) {
                            $value = $value ? 'On' : 'Off';
                        }

                        //when array get the first value
                        $system_value = is_array($value) ? array_value_first($value) : $value;
                        ?>
                        <tr data-status="<?php esc_attr($key); ?>">
                            <?php echo sprintf('<td>%s</td>', esc_html__($system_name, 'woo-feed')); ?>
                            <?php echo sprintf('<td>%s</td>', esc_html__($system_value, 'woo-feed')); ?>
                        </tr>
                    <?php
                    }
                ?>
                <tr>
                    <td><?php esc_html_e('Debug Feed File', 'woo-feed'); ?></td>
                    <td>
                        <form action="" method="post">
                            <label for="woo-feed-test-options" style="display: none;">Test Option</label>
                            <input type="text" id="woo-feed-test-options" name="woo-feed-test-options" placeholder="option name">
                            <input type="submit" name="woo-feed-test-options-submit" value="Test">
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
        if ( isset( $_POST['woo-feed-test-options-submit'] ) ) {
            $option_name = sanitize_text_field($_POST['woo-feed-test-options']);

            $get_option_val = maybe_unserialize(get_option($option_name));
            echo "<pre>";
            print_r($get_option_val);
            echo "</pre>";
        }
        ?>
    </div>
</div>