<?php
//https://rudrastyh.com/woocommerce/payment-gateway-plugin.html
//https://rudrastyh.com/woocommerce/checkout-block-payment-method-integration.html

/**
 * Plugin Name: Payment In Store For Local Pickup Orders
 * Plugin URI: https://github.com/CavojovaEmma/custom-woocommerce-payment-upon-local-pickup
 * Description: A custom payment gateway plugin for WooCommerce that gives customers selecting local pickup the option to pay in-store, either with cash or by card.
 * Version: 1.0
 * Author: Emma Čavojová
 * Author URI: https://dev-emma-cavojova.pantheonsite.io/
 * Requires at least: 6.5+
 * Tested up to: 6.6.2
 * Requires PHP: 7.4+
 * Text Domain: payment-in-store-for-local-pickup-orders
 * License: GPL v`2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Payment In Store For Local Pickup Orders is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Payment In Store For Local Pickup Orders is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Payment In Store For Local Pickup Orders. If not, see https://www.gnu.org/licenses/.
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'PULP_PATH' ) ) {
    define('PULP_PATH', plugin_dir_path(__FILE__));
}

/**
 * This action hook registers WC_Payment_Upon_Local_Pickup_Gateway class as a WooCommerce payment gateway
 */
function add_pulp_gateway_class( $gateways )
{
    $gateways[] = 'WC_Payment_In_Store_For_Local_Pickup_Orders_Gateway';
    return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'add_pulp_gateway_class');


/**
 * Initialize WC_Payment_Upon_Local_Pickup_Gateway class when plugins are loaded
 */
function init_wc_pulp_gateway_class(): void
{
    if ( ! class_exists('WC_Payment_In_Store_For_Local_Pickup_Orders_Gateway') ) {

        include_once PULP_PATH . 'includes/class-payment-in-store-for-local-pickup-orders.php';

    }
}
add_action( 'plugins_loaded', 'init_wc_pulp_gateway_class');


/**
 * Register gateway block support
 */
function gateway_block_support(): void
{
    require_once __DIR__ . '/includes/class-payment-in-store-for-local-pickup-orders-gateway-blocks-support.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
            $payment_method_registry->register( new WC_Payment_In_Store_For_Local_Pickup_Orders_Gateway_Blocks_Support );
        }
    );

}
add_action( 'woocommerce_blocks_loaded', 'gateway_block_support' );
