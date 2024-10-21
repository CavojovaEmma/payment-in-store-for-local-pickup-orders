<?php

if ( ! defined('ABSPATH') ) {
    exit;
}

class WC_Payment_In_Store_For_Local_Pickup_Orders_Gateway extends WC_Payment_Gateway {

    /**
     * Class constructor
     **/
    public function __construct()
    {
        $this->id = 'payment-in-store-for-local-pickup-orders';
        $this->icon = '';
        $this->method_title = __( 'Payment In Store For Local Pickup Orders Gateway' );
        $this->method_description = __( 'Gives customers selecting local pickup the option to pay in-store, either with cash or by card.' );
        $this->supports = array(
            'products'
        );

        $this->pulp_init_admin_settings_fields();
        $this->init_settings();
        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option( 'enabled' );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_filter( 'woocommerce_available_payment_gateways', array( $this, 'display_for_local_pickup') );
    }

    /**
     * Create gateway instance when local pickup shipping method is selected
     **/
    function display_for_local_pickup( $gateways )
    {
        if ( is_admin() || ! is_checkout() || !  WC()->session->get( 'chosen_shipping_methods' )) {
            return $gateways;
        }

        $chosen_shipping = WC()->session->get( 'chosen_shipping_methods' )[0];

        if ( isset( $chosen_shipping ) && str_contains( $chosen_shipping, 'local_pickup' ) ) {
            if ( ! isset( $gateways['payment-in-store-for-local-pickup-orders' ] ) ) {
                $gateways['payment-in-store-for-local-pickup-orders' ] = new self();
            }
        } else {
            if ( isset( $gateways[ 'payment-in-store-for-local-pickup-orders' ] ) ) {
                unset( $gateways[ 'payment-in-store-for-local-pickup-orders' ] );
            }
        }
        return $gateways;
    }

    /**
     * Plugin options
     **/
    public function pulp_init_admin_settings_fields(): void
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'       => 'Enable/Disable',
                'label'       => 'Enable Payment In Store For Local Pickup Orders Gateway',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default'     => __( 'Payment In Store For Local Pickup Orders in cash or by card', 'payment-in-store-for-local-pickup-orders' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default'     => __( 'You will pay at the store when picking up the order in cash or by card' ),
            ),
        );

    }

    /**
     * Process the payment
     * @param $order_id
     * @return array|null
     */
    public function process_payment( $order_id ): ?array
    {
        $order = wc_get_order( $order_id );

        $order->set_status('on-hold');

        WC()->cart->empty_cart();

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );
    }
}