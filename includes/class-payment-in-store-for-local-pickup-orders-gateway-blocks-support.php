<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Payment_In_Store_For_Local_Pickup_Orders_Gateway_Blocks_Support extends AbstractPaymentMethodType {

    private $gateway;

    protected $name = 'payment-in-store-for-local-pickup-orders';

    public function initialize(): void
    {
        $this->settings = get_option( "woocommerce_{$this->name}_settings", array() );
         $gateways = WC()->payment_gateways->payment_gateways();
         $this->gateway = $gateways[ $this->name ];
    }

    public function is_active(): bool
    {
        return ! empty( $this->settings[ 'enabled' ] ) && 'yes' === $this->settings[ 'enabled' ];
    }

    public function get_payment_method_data(): array
    {
        return array(
            'title'        => $this->get_setting( 'title' ),
            'description'  => $this->get_setting( 'description' ),
       );
    }

}

