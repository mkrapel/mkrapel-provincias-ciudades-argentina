<?php
/**
 * Legacy flat rate settings. *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/>' . __( 'Supports the following placeholders: <code>[qty]</code> = number of items, <code>[cost]</code> = cost of items, <code>[fee percent="10" min_fee="20"]</code> = Percentage based fee.', 'woocommerce' );

$settings =  array(
    'single_method' => array(
        'title' 		=> __( 'Método de Envío Único' ),
        'type' 			=> 'select',
        'description' 	=> __( 'Al hacer un método de envío único, elimina todos los demás e impone sus propias reglas' ),
        'class'         => 'wc-enhanced-select',
        'default' 		=> 'no',
        'desc_tip'		=> true,
        'options'		=> array(
            'yes' 	=> __( 'Yes', 'woocommerce' ),
            'no'    => __( 'No', 'woocommerce' )
        )
    ),

    'title' => array(
        'title' 		=> __( 'Nombre del Tipo de Envío', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Esto controla el título que ve el usuario durante el pago. en el CheckOut.', 'woocommerce' ),
        'default'		=> __( 'Filtro de envío por Distrito/Ciudad/Municipio', 'woocommerce' ),
        'desc_tip'		=> true
    ),
    'tax_status' => array(
        'title' 		=> __( 'Estado Impuesto', 'woocommerce' ),
        'type' 			=> 'select',
        'class'         => 'wc-enhanced-select',
        'default' 		=> 'taxable',
        'options'		=> array(
            'taxable' 	=> __( 'Taxable', 'woocommerce' ),
            'none' 		=> _x( 'None', 'Tax status', 'woocommerce' )
        )
    ),
    'cost' => array(
        'title' => __('Monto'),
        'type' 			=> 'text',
        'description' 	=> $cost_desc,
        'default'		=> '0',
        'desc_tip'		=> true
    ),
    'cities' => array(
        'title' => __('Distritos/Ciudades/Municipios'),
        'type' => 'multiselect',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Seleccione los Distritos/Ciudades/Municipios que hace referencia al Departamento/Región/Provincia que ha agregado anteriormente como Zona de Envío' ),
        'options' => $this->showCitiesRegions(),
        'desc_tip'    => true,
    )
);

$shipping_classes = WC()->shipping->get_shipping_classes();

if ( ! empty( $shipping_classes ) ) {
    $settings['class_costs'] = array(
        'title'       => __( 'Costos de Clase de Envío', 'woocommerce' ),
        'type'        => 'title',
        'default'     => '',
        /* translators: %s: URL for link. */
        'description' => sprintf( __( 'Estos costos se pueden agregar opcionalmente según la <a href="%s"> clase de envío del producto.</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
    );
    foreach ( $shipping_classes as $shipping_class ) {
        if ( ! isset( $shipping_class->term_id ) ) {
            continue;
        }
        $settings[ 'class_cost_' . $shipping_class->term_id ] = array(
            /* translators: %s: shipping class name */
            'title'             => sprintf( __( '"%s" costo de la clase de envío', 'woocommerce' ), esc_html( $shipping_class->name ) ),
            'type'              => 'text',
            'placeholder'       => __( 'N/A', 'woocommerce' ),
            'description'       => $cost_desc,
            'default'           => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names.
            'desc_tip'          => true,
            'sanitize_callback' => array( $this, 'sanitize_cost' ),
        );
    }
    $settings['no_class_cost'] = array(
        'title'             => __( 'Sin costo de clase de envío', 'woocommerce' ),
        'type'              => 'text',
        'placeholder'       => __( 'N/A', 'woocommerce' ),
        'description'       => $cost_desc,
        'default'           => '',
        'desc_tip'          => true,
        'sanitize_callback' => array( $this, 'sanitize_cost' ),
    );
    $settings['type'] = array(
        'title'   => __( 'Tipo de cálculo', 'woocommerce' ),
        'type'    => 'select',
        'class'   => 'wc-enhanced-select',
        'default' => 'class',
        'options' => array(
            'class' => __( 'Por clase: Cargue el envío para cada clase de envío individualmente', 'woocommerce' ),
            'order' => __( 'Por pedido: cargo de envío para la clase de envío más cara', 'woocommerce' ),
        ),
    );
}
return $settings;