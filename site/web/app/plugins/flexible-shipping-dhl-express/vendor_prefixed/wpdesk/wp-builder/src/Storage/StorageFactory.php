<?php

namespace DhlVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \DhlVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
