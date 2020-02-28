<?php
/**
 * Import Util Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Import Util 
 */
class Import_Util
{
    /**
     * Imports a post
     * 
     * @param array product to be imported
     */
    public static function import_post($product)
    {
        // initial post info
        $wc_product = array(
            'post_title'=> $product['product_name'],
            'post_status' => 'publish',
            'post_type' => 'product',
            'post_excerpt' => $product['part_description']
        );

        $post_id = wp_insert_post($wc_product);

        return $post_id;
    }

    /**
     * Imports product dimensions
     * 
     * @param int id of associated product(post)
     * @param array dimensions to be imported
     */
    public static function import_dimensions($post_id, $dimensions)
    {
        update_post_meta($post_id, '_weight', $dimensions['weight']);
        update_post_meta($post_id, '_length', $dimensions['length']);
        update_post_meta($post_id, '_width', $dimensions['width']);
        update_post_meta($post_id, '_height', $dimensions['height']);
    }

    /**
     * Imports product cateogories
     * 
     * @param int id of associated product(post)
     * @param array product that contains the categories
     */
    public static function import_categories($post_id, $product)
    {
        // set brand
        $brand_term_id = self::get_category_id($product['brand']);
        wp_set_object_terms($post_id, $brand_term_id, 'product_cat', true);
        // set category
        $category_term_id = self::get_category_id($product['category']);
        wp_set_object_terms($post_id, $category_term_id, 'product_cat', true);
        // set subcategory
        $subcategory_term_id = self::get_subcategory_id($category_term_id, $product['subcategory']);
        wp_set_object_terms($post_id, $subcategory_term_id, 'product_cat', true);
    }

    /**
     * Imports custom attributes such as Turn14 id
     * 
     * @param int id of associated product(post)
     * @param int id of turn14 product
     */
    public static function import_attributes($post_id, $product_id)
    {
        update_post_meta(
            $post_id,
            '_product_attributes',
            array(
                array(
                    'name' => 'turn14_id',
                    'value' => $product_id,
                    'position' => 1,
                    'is_visible' => 0,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                ),
            )
        );
    }

    /**
     * Imports product descriptions
     * 
     * @param int id of associated product(post)
     * @param array media that contains descriptions
     */
    public static function import_descriptions($post_id, $media)
    {
        foreach ($media['descriptions'] as $description) {
            if ($description['type'] == 'Market Description') {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_content' => $description['description']
                ));
            } elseif ($description['type'] == 'Application Summary') {
                $product_attributes = get_post_meta($post_id, '_product_attributes', true);
                array_push($product_attributes, array(
                    'name' => 'Fitment',
                    'value' => $description['description'],
                    'position' => 1,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                ));
                update_post_meta(
                    $post_id,
                    '_product_attributes',
                    $product_attributes
                );
            }
        }
    }

    /**
     * Imports product images
     * 
     * @param int id of associated product(post)
     * @param array media that contains images
     */
    public static function import_images($post_id, $media)
    {
        if ($post_id != null && $media != null) {
            $files = $media['files'];
            if ($files != null && is_array($files)) {
                foreach ($files as $file) {
                    $content = $file['media_content'];
                    $image_links = $file['links'];
                    if ($content != null && $image_links != null) {
                        if ($content == 'Photo - Primary') {
                            foreach ($image_links as $image_link) {
                                // only upload if its big
                                if ($image_link['size'] == 'L') {
                                    self::import_image($post_id, $image_link['url'], true);
                                    break;
                                }
                            }
                        } else {
                            foreach ($image_links as $image_link) {
                                if ($image_link['size'] == 'L') {
                                    self::import_image($post_id, $image_link['url']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Imports product pricing
     * 
     * @param int id of associated product(post)
     * @param array pricing of product
     */
    public static function import_pricing($post_id, $pricing)
    {
        $price_list = $pricing['attributes']['pricelists'];
        $product_prices = array(
            'Retail' => null,
            'Jobber' => null,
            'MAP' => null
        );

        foreach ($price_list as $price) {
            if ($price['name'] == 'Retail') {
                $product_prices['Retail'] = $price['price'];
            } elseif ($price['name'] == 'Jobber') {
                $product_prices['Jobber'] = $price['price'];
            } elseif ($price['name'] == 'MAP') {
                $product_prices['MAP'] = $price['price'];
            }
        }

        if ($product_prices['Retail'] != null && $product_prices['MAP'] != null) {
            update_post_meta($post_id, '_regular_price', $product_prices['Retail']);
            update_post_meta($post_id, '_price', $product_prices['MAP']);
            update_post_meta($post_id, '_sale_price', $product_prices['MAP']);
        } elseif ($product_prices['Retail'] != null) {
            update_post_meta($post_id, '_regular_price', $product_prices['Retail']);
            update_post_meta($post_id, '_price', $product_prices['Retail']);
        } elseif ($product_prices['MAP'] != null) {
            update_post_meta($post_id, '_regular_price', $product_prices['MAP']);
            update_post_meta($post_id, '_price', $product_prices['MAP']);
        }
    }

    /**
     * Imports product inventory
     * 
     * @param int id of associated product(post)
     * @param array inventory of product
     */
    public static function import_inventory($post_id, $inventory)
    {
        $total_stock = 0;

        $turn14_inventory = $inventory[0]['attributes']['inventory'];
        if ($turn14_inventory !== null) {
            foreach ($turn14_inventory as $warehouse_inventory) {
                if ($warehouse_inventory > 0) {
                    $total_stock = $total_stock + $warehouse_inventory;
                }
            }
        }

        $mfg_inventory = $inventory[0]['attributes']['manufacturer'];

        if ($mfg_inventory['stock'] > 0) {
            $total_stock = $total_stock + $mfg_inventory['stock'];
        }

        update_post_meta($post_id, '_manage_stock', 'yes');
        wc_update_product_stock($post_id, $total_stock, 'set');
        update_post_meta($post_id, '_backorders', 'no');
    }

    /**
     * Helper method for importing an image
     * 
     * @param int id of associated product(post)
     * @param string image url
     * @param boolean optional flag o set the primary image, defaults false to the product gallery
     */
    private static function import_image($post_id, $image_url, $primary_flag = false)
    {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
                          
        file_put_contents($file, $image_data);
                          
        $wp_filetype = wp_check_filetype($filename, null);
                          
        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name($filename),
                            'post_content' => '',
                            'post_status' => 'inherit'
                          );
                          
        $attach_id = wp_insert_attachment($attachment, $file);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        if ($primary_flag) {
            set_post_thumbnail($post_id, $attach_id);
        } else {
            // Add gallery image to product
            $attach_id_array = get_post_meta($post_id, '_product_image_gallery', true);
            $attach_id_array .= ','.$attach_id;
            update_post_meta($post_id, '_product_image_gallery', $attach_id_array);
        }
    }

    /**
     * Fetches category id based on name. Creates a new one if not already existant
     * 
     * @param string category name
     * 
     * @return int id of category
     */
    private static function get_category_id($product_category)
    {
        if (!term_exists($product_category) && $product_category != null) {
            $term = wp_insert_term($product_category, 'product_cat');
            return $term['term_id'];
        }

        $term = get_term_by('name', $product_category, 'product_cat');
        return $term->term_id;
    }

    /**
     * Fetches subcategory id based on name. Creates a new one if not already existant
     * 
     * @param string parent category name
     * @param string subcategory name
     * 
     * @return int id of subcategory
     */
    private static function get_subcategory_id($parent_category, $product_subcategory)
    {
        if (!term_exists($product_subcategory) && $product_subcategory != null) {
            $term = wp_insert_term(
                $product_subcategory,
                'product_cat',
                array(
                    'parent' => $parent_category
                )
            );
            return $term['term_id'];
        }

        $term = get_term_by('name', $product_subcategory, 'product_cat');
        return $term->term_id;
    }
}
