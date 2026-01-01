<?php
/**
 * WooCommerce recommends to use hooks rather than overriding template files,
 * this is where we do that stuff. The Petland theme relies on a few modifications to display properly (Added wrappers mainly)
 * 
 * @package Petland
 * @author Turbulentarius
 */

namespace Petland\WooCommerce;

use WP_Exception;
use Error;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Layout
{
    public function __construct()
    {
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

        add_action('woocommerce_before_main_content', [$this, 'open_main_wrapper'], 5);
        add_action('woocommerce_before_main_content', [$this, 'open_main_inner'], 10);
        add_action('woocommerce_after_main_content', [$this, 'close_main_inner'], 10);
        add_action('woocommerce_single_product_summary', [$this, 'insert_product_template'], 100);
        add_action('woocommerce_shop_loop_header', [$this, 'open_content_row']);
        add_action('woocommerce_sidebar', [$this, 'close_all_wrappers']);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10); // Rm and move to fix layout of product listings
        add_action('woocommerce_after_shop_loop_item_title', [$this, 'open_price_wrap']);
        add_action('woocommerce_after_shop_loop_item', [$this, 'close_price_wrap']);

        if (!function_exists('woocommerce_template_loop_price')) {
            function woocommerce_template_loop_price() {}
        }
    }

    /**
     * Add a "main-wrapper" to better control the layout, and insert the WC breadcrumbs
     * @return void 
     */
    public function open_main_wrapper()
    {
        echo '<div class="main-wrapper">';
        woocommerce_breadcrumb();
    }

    /**
     * The main-inner-wrap is used to apply a min-/max width (also applied to Breadcrumbs)
     * @return void 
     * @throws WP_Exception 
     */
    public function open_main_inner()
    {
        echo '<div class="main-inner-wrap"><div id="main">';
        if (!is_paged()) echo '<div class="row secondary-content page-content">';
    }

    public function close_main_inner()
    {
        if (!is_paged()) echo '</div>';
        echo '</div>';
    }

    /**
     * Add extra content to the end of product summeries.
     * Note. The prioity of the hook determines whether it's added to the beginning or end of the element.
     * @return void 
     */
    public function insert_product_template()
    {
        echo do_shortcode('[petland_template slug="petland-template-product-content"]');
    }

    /**
     * An extra "page-content" class is needed to properly identify the content row
     * @return void 
     */
    public function open_content_row()
    {
        echo '</div><div class="row page-content">';
    }

    public function close_all_wrappers()
    {
        echo '</div></div>';
    }

    public function open_price_wrap()
    {
        global $product;
        echo '<div class="price-buybtn-wrap"><div class="price">' . $product->get_price_html() . '</div>';
    }

    public function close_price_wrap()
    {
        echo '</div>';
    }
}

new Layout();