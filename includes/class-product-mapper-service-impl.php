<?php
/**
 * Import Worker Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Import Worker completes import jobs
 */
class Product_Mapper_Service_Impl implements Product_Mapper_Service
{
    private $product;
    private $product_attributes;
    private $product_categories;
    private $product_gallery_images;
    private $turn14_rest_client;

    /**
     * Default Constructor
     */
    public function __construct($rest_client)
    {
        $this->product_attributes = array();
        $this->product_categories = array();
        $this->product_gallery_images = array();
        $this->turn14_rest_client = $rest_client;
    }

    /**
     * Helper function for importing a single product
     *
     * @param array product
     */
    public function map_product($item)
    {
        try {
            $item_attributes = $item['attributes'];
            if ($item_attributes != null) {
                $this->product = wc_get_products(array('sku' => $item_attributes['mfr_part_number']))[0];
                if (!$this->product) {
                    $this->product = new WC_Product_Simple();
                }
        
                $this->map_attribute('tun14_id', $item['id'], false);
                $this->product->set_name($item_attributes['product_name']);
                $this->product->set_status('publish');
                $this->product->set_catalog_visibility('visible');
                $this->product->set_short_description($item_attributes['part_description']);
                $this->map_image($item_attributes['thumbnail'], true);
                $this->product->set_sku($item_attributes['mfr_part_number']);
                $this->map_category($item_attributes['category']);
                $this->map_category($item_attributes['brand']);
                $this->map_subcategory($item_attributes['category'], $item_attributes['subcategory']);
                $dimensions = $item_attributes['dimensions'][0];
                if ($dimensions != null) {
                    $this->product->set_weight($dimensions['weight']);
                    $this->product->set_length($dimensions['length']);
                    $this->product->set_width($dimensions['width']);
                    $this->product->set_height($dimensions['height']);
                }
                $this->map_media($item);
                $this->map_pricing($item);
                $this->map_inventory($item);
        
                $this->product->set_attributes($this->product_attributes);
                $this->product->set_category_ids($this->product_categories);
                $this->product->set_gallery_image_ids($this->product_gallery_images);
            }
        } catch (\Throwable $th) {
            error_log('Something went wrong importing '. $item_attributes['product_name'] . ': ' . $th->getMessage());
        }
        
        // reset for next product
        $this->product_attributes = array();
        $this->product_categories = array();
        $this->product_gallery_images = array();

        return $this->product;
    }

    /**
     * Helper function for importing a single media
     *
     * @param array media
     */
    private function map_media($item)
    {
        $media = $this->turn14_rest_client->get_item_media($item['id'])[0];
        if ($media != null) {
            $descriptions = $media['descriptions'];
            if ($descriptions != null && is_array($descriptions)) {
                foreach ($descriptions as $description) {
                    $description_type = $description['type'];
                    if ($description_type != null) {
                        if ($description_type == 'Market Description') {
                            $this->product->set_description($item_attributes['description']);
                        } elseif ($description_type == 'Application Summary') {
                            $this->map_attribute('Fitment', $description['description']);
                        }
                    }
                }
            }

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
                                    $this->map_image($image_link['url'], true);
                                    break;
                                }
                            }
                        } else {
                            foreach ($image_links as $image_link) {
                                if ($image_link['size'] == 'L') {
                                    $this->map_image($image_link['url']);
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
     * Helper function for importing a single pricing
     *
     * @param array pricing
     */
    private function map_pricing($item)
    {
        $pricing = $this->turn14_rest_client->get_item_pricing($item['id']);
        if ($pricing != null) {
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
                $this->product->set_regular_price($product_prices['Retail']);
                $this->product->set_price($product_prices['MAP']);
                $this->product->set_sale_price($product_prices['MAP']);
            } elseif ($product_prices['Retail'] != null) {
                $this->product->set_regular_price($product_prices['Retail']);
                $this->product->set_price($product_prices['Retail']);
            } elseif ($product_prices['MAP'] != null) {
                $this->product->set_regular_price($product_prices['MAP']);
                $this->product->set_price($product_prices['MAP']);
            } elseif ($product_prices['Jobber'] != null) {
                $this->product->set_regular_price($product_prices['Jobber']);
                $this->product->set_price($product_prices['Jobber']);
            }
        }
    }

    /**
     * Helper function for importing a single inventory
     *
     * @param array inventory
     */
    private function map_inventory($item)
    {
        $total_stock = 0;
        $inventory = $this->turn14_rest_client->get_item_inventory($item['id']);
        if ($inventory != null) {
            $turn14_inventory = $inventory[0]['attributes']['inventory'];
            if ($turn14_inventory != null) {
                foreach ($turn14_inventory as $warehouse_inventory) {
                    if ($warehouse_inventory > 0) {
                        $total_stock = $total_stock + $warehouse_inventory;
                    }
                }
            }

            $mfg_inventory = $inventory[0]['attributes']['manufacturer'];
            if ($mfg_inventory != null) {
                if ($mfg_inventory['stock'] > 0) {
                    $total_stock = $total_stock + $mfg_inventory['stock'];
                }
            }
        }

        $this->product->set_manage_stock(1);
        $this->product->set_backorders('notify');
        $this->product->set_stock_quantity($total_stock);
        $this->product->set_stock_status('instock');
    }

    /**
     * Helper method for importing an image
     *
     * @param int id of associated product(post)
     * @param string image url
     * @param boolean optional flag o set the primary image, defaults false to the product gallery
     */
    private function map_image($image_url, $primary_flag = false)
    {
        // TODO: check if image already exists
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
            $this->product->set_image_id($attach_id);
        } else {
            array_push($this->product_gallery_images, $attach_id);
        }
    }

    /**
     * Fetches category id based on name. Creates a new one if not already existant
     *
     * @param string category name
     *
     * @return int id of category
     */
    private function map_category($product_category)
    {
        if (!term_exists($product_category) && $product_category != null) {
            $term = wp_insert_term($product_category, 'product_cat');
            array_push($this->product_categories, $term['term_id']);
        }

        $term = get_term_by('name', $product_category, 'product_cat');
        array_push($this->product_categories, $term->term_id);
    }

    /**
     * Fetches subcategory id based on name. Creates a new one if not already existant
     *
     * @param string parent category name
     * @param string subcategory name
     *
     * @return int id of subcategory
     */
    private function map_subcategory($parent_category, $product_subcategory)
    {
        if (!term_exists($product_subcategory) && $product_subcategory != null) {
            $term = wp_insert_term(
                $product_subcategory,
                'product_cat',
                array(
                    'parent' => $parent_category
                )
            );
            array_push($this->product_categories, $term['term_id']);
        }

        $term = get_term_by('name', $product_subcategory, 'product_cat');
        array_push($this->product_categories, $term->term_id);
    }

    /**
     *
     */
    private function map_attribute($name, $value, $visibile=true)
    {
        $attribute = new WC_Product_Attribute();
        $attribute->set_name($name);
        $attribute->set_options(array($value));
        $attribute->set_position(1);
        $attribute->set_visible($visibile);
        $attribute->set_variation(0);
        array_push($this->product_attributes, $attribute);
    }
}
