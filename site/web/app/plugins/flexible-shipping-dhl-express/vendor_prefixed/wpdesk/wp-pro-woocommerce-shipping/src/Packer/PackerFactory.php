<?php

namespace DhlVendor\WPDesk\WooCommerceShippingPro\Packer;

use DhlVendor\WPDesk\Packer\Box;
use DhlVendor\WPDesk\Packer\Packer;
use DhlVendor\WPDesk\Packer\PackerSeparately;
/**
 * Can create a ready to use  packer.
 *
 * @package WPDesk\WooCommerceShippingPro\Packer
 */
class PackerFactory
{
    /** @var string */
    private $packaging_method;
    /**
     * PackerFactory constructor.
     *
     * @param string $packaging_method One of packaging method names
     */
    public function __construct($packaging_method)
    {
        $this->packaging_method = $packaging_method;
    }
    /**
     * Create packer that can pack to given boxes.
     *
     * @param Box[] $boxes Boxes to pack.
     *
     * @return Packer
     */
    public function create_packer(array $boxes)
    {
        if ($this->packaging_method === \DhlVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings::PACKING_METHOD_SEPARATELY) {
            $packer = new \DhlVendor\WPDesk\Packer\PackerSeparately();
        } else {
            $packer = new \DhlVendor\WPDesk\Packer\Packer();
            foreach ($boxes as $box) {
                $packer->add_box($box);
            }
        }
        return $packer;
    }
}
