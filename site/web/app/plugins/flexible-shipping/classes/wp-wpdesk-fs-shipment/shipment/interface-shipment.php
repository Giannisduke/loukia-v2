<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! interface_exists( 'WPDesk_Flexible_Shipping_Shipment_Interface' ) ) {

    interface WPDesk_Flexible_Shipping_Shipment_Interface  {

        /**
         * @param array $fs_method
         * @param array $package
         * @return void
         * Executes on woocommerce checkout when order is created
         */
        public function checkout( array $fs_method, $package );

        /**
         * @return void
         * Displays metabox in woocommerce order
         */
        public function order_metabox();

        /**
         * @return string
         * Returns woocommerce metabox title
         */
        public function get_order_metabox_title();

        /**
         * @param string $action
         * @param array $data
         * @return void
         * Executes on ajax request. $data contains all woocommerce order metabox fields values from metabox generated in order_metabox() method.
         */
        public function ajax_request( $action, $data );

        /**
         * @return string
         * Returns error message
         */
        public function get_error_message();

        /**
         * @return string
         * Returns tracking number for shipment
         */
        public function get_tracking_number();

        /**
         * @return string
         * Returns tracking URL for shipping
         */
        public function get_tracking_url();

        /**
         * @return array
         * Return label data foe shipping in array:
         *      'label_format' => 'pdf'
         *      'content' => pdf content,
         *      'file_name' => file name for label
         */
        public function get_label();

        /**
         * @return mixed
         */
        public function get_after_order_table();

        /**
         * @return mixed
         */
        public function get_email_after_order_table();

    }

}
