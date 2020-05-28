<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configures custom fields for a WC Product.
 *
 * @author Sam Hall <hallsamuel90@gmail.com>
 */
class Field_Config
{
    /**
     *
     */
    public function register_fields()
    {
        add_action('woocommerce_product_options_general_product_data', array($this, 'register_brand_id'));
        add_action('woocommerce_process_product_meta', array($this, 'save_brand_id'));
    }

    /**
     * Registers the brand_id field.
     */
    public function register_brand_id()
    {
        $args = array(
            'id' => 'brand_id',
            'label' => __('Brand ID', 'brand_id_field'),
            'class' => 'brand-id-field',
            'desc_tip' => true,
            'description' => __('The Turn14 Brand Id of the product.', 'brand_id_field'),
        );
        woocommerce_wp_text_input($args);
    }

    /**
     * Saves the brand_id field.
     */
    public function save_brand_id($post_id)
    {
        $product = wc_get_product($post_id);
        $title = isset($_POST['brand_id']) ? $_POST['brand_id'] : '';
        $product->update_meta_data('brand_id', sanitize_text_field($title));
        $product->save();
    }

}
