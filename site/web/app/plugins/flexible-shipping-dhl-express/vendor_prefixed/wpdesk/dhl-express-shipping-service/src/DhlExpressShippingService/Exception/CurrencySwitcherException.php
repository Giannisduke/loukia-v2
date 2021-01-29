<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService\Exception;

use DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings;
/**
 * Exception thrown when switcher is not accepted.
 *
 * @package WPDesk\DhlExpressShippingService\Exception
 */
class CurrencySwitcherException extends \RuntimeException
{
    /**
     * @param ShopSettings $shop_settings .
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings $shop_settings)
    {
        $locale = $shop_settings->get_locale();
        $is_pl = 'pl_PL' === $locale;
        $pro_link = $is_pl ? 'https://wpde.sk/dhl-express-pro-cart-currency-pl' : 'https://wpde.sk/dhl-express-pro-cart-currency';
        $message = \sprintf(\__('Multicurrency is supported by %1$sFlexible Shipping DHL Express PRO →%2$s', 'flexible-shipping-dhl-express'), '<a href="' . \esc_url($pro_link) . '" target="_blank">', '</a>');
        parent::__construct($message);
    }
}
