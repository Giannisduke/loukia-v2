<?php

namespace FSVendor\WPDesk\Helper;

use FSVendor\WPDesk\Notice\Notice;
use FSVendor\WPDesk_Basic_Requirement_Checker;
/**
 * Shows notice that you should upgrade your environment soon.
 *
 * @package WPDesk\Helper
 */
class UpgradeSoonNotice
{
    const SUPPORTED_PHP = '7.0';
    const SUPPORTED_WC = '4.0';
    const SUPPORTED_WP = '5.0';
    /**
     * @return bool
     */
    private function is_old_wc()
    {
        return !\FSVendor\WPDesk_Basic_Requirement_Checker::is_wc_at_least(self::SUPPORTED_WC);
    }
    /**
     * @return bool
     */
    private function is_old_wp()
    {
        return !\FSVendor\WPDesk_Basic_Requirement_Checker::is_wp_at_least(self::SUPPORTED_WP);
    }
    /**
     * @return bool
     */
    private function is_old_php()
    {
        return !\FSVendor\WPDesk_Basic_Requirement_Checker::is_php_at_least(self::SUPPORTED_PHP);
    }
    /**
     * Returns true only first time per WP request.
     *
     * @return bool
     */
    private function has_not_shown_earlier()
    {
        $mutex_filter = 'wpdesk_helper_upgrade_notice_already_shown';
        if (\apply_filters($mutex_filter, \true)) {
            \add_filter($mutex_filter, static function () {
                return \false;
            });
            return \true;
        }
        return \false;
    }
    /**
     * Shows notice that you should upgrade your environment soon. Notice will be shown only once per WP request.
     */
    public function show_info_about_upgrade_if_old_env()
    {
        \add_action('plugins_loaded', function () {
            if ($this->has_not_shown_earlier()) {
                if ($this->is_old_php()) {
                    new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('The PHP version your shop is currently using is deprecated. We highly advise to upgrade it to at least %s since the support for this one will be dropped soon.', 'flexible-shipping'), self::SUPPORTED_PHP));
                }
                if ($this->is_old_wc()) {
                    new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('The WooCommerce version your shop is currently using is deprecated. We highly advise to upgrade it to at least %s since the support for this one will be dropped soon.', 'flexible-shipping'), self::SUPPORTED_WC));
                }
                if ($this->is_old_wp()) {
                    new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('The WordPress version your shop is currently using is deprecated. We highly advise to upgrade it to at least %s since the support for this one will be dropped soon.', 'flexible-shipping'), self::SUPPORTED_WP));
                }
            }
        });
    }
}
