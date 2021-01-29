<?php

namespace FSVendor\WPDesk\License\Page;

use FSVendor\WPDesk\License\Page\License\Action\LicenseActivation;
use FSVendor\WPDesk\License\Page\License\Action\LicenseDeactivation;
use FSVendor\WPDesk\License\Page\License\Action\Nothing;
/**
 * Action factory.
 *
 * @package WPDesk\License\Page\License
 */
class LicensePageActions
{
    /**
     * Creates action object according to given param
     *
     * @param string $action
     *
     * @return Action
     */
    public function create_action($action)
    {
        if ($action === 'activate') {
            return new \FSVendor\WPDesk\License\Page\License\Action\LicenseActivation();
        }
        if ($action === 'deactivate') {
            return new \FSVendor\WPDesk\License\Page\License\Action\LicenseDeactivation();
        }
        return new \FSVendor\WPDesk\License\Page\License\Action\Nothing();
    }
}
