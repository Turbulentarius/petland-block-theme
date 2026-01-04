<?php

/**
 * @package Petland
 * @author Turbulentarius
 */

namespace Petland;

use WP_Customize_Media_Control;
use WP_Exception;

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

class Theme
{
  public function __construct()
  {
    $this->boot_theme();
  }

  /**
   * Registers hooks and shortcodes
   * @return void 
   * @throws WP_Exception 
   */
  private function boot_theme()
  {
    add_action('after_setup_theme', [$this, 'theme_setup']);
    add_action('init', [$this, 'theme_init']);
    add_filter('template_include', [$this, 'template_include']);
    add_filter('allowed_block_types_all', [$this, 'example_allowed_block_types'], 10, 2);
    add_action('customize_register', [$this, 'customize_register']);
    add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    add_action('widgets_init', [$this, 'register_sidebars']);

    add_shortcode('petland_template', [$this, 'petland_template_shortcode']);
    // Include theme colors so we can use them in theme.json
    add_action('enqueue_block_editor_assets', function () {
      wp_enqueue_style('petland-editor-vars', get_template_directory_uri() . '/dist/petstyleColors.css');
    });


    // Uncomment the line below to activate the migration on the live server
    // $this->hook_migration();
  }

  public function hook_migration()
  {
    add_action('init', [$this, 'replace_elementor_pages_by_id']);
  }

  public function template_include($template)
  {
    return $template;
  }

  public function enqueue_assets()
  {
    // Include primary front-end styling (petstyle.css)
    wp_enqueue_style('petland-stylesheet', get_template_directory_uri() . '/dist/petstyle.css', [], filemtime(get_template_directory() . '/dist/petstyle.css'));

    // Include the account page styling on the account page
    if (is_account_page()) {
      $style_path = get_template_directory() . '/dist/petWoocommerceMyaccount.css';
      wp_enqueue_style(
        'petland-woo-myaccount-stylesheet',
        get_template_directory_uri() . '/dist/petWoocommerceMyaccount.css',
        [],
        file_exists($style_path) ? filemtime($style_path) : null // Check if file exist (It should always exist, but meh..)
      );
    }
    // Include the styles for the checkout flow
    if (is_cart() || is_checkout()) {
      $style_path = get_template_directory() . '/dist/petWoocommerceCheckoutFlow.css';
      wp_enqueue_style(
        'petland-woo-flow',
        get_template_directory_uri() . '/dist/petWoocommerceCheckoutFlow.css',
        [],
        file_exists($style_path) ? filemtime($style_path) : null // Check if file exist
      );
    }

    // Make sure we can target WooCommerce pages with targeted styles in the CSS
    add_filter('body_class', function ($classes) {
      if (!function_exists('is_cart')) return $classes;

      if (is_account_page()) {
        $classes[] = 'petland-account-page';
      }

      if (is_cart()) {
        $classes[] = 'petland-checkout-flow';
        $classes[] = 'petland-cart-page';
      }
      if (is_checkout()) {
        $classes[] = 'petland-checkout-flow';
        $classes[] = 'petland-checkout-page';
      }

      return $classes;
    });


    // Petland specific scripts. E.g. Category filters (Price and categories), as well as menu and sticky header.
    wp_enqueue_script('petland-category-filter', get_template_directory_uri() . '/dist/categoryFilter.js', [], null, true);
    wp_enqueue_script('petland-price-filter', get_template_directory_uri() . '/dist/priceFilter.js', [], null, true);
    wp_enqueue_script('petland-aside-menu', get_template_directory_uri() . '/dist/asideMenu.js', [], null, true);
    wp_enqueue_script('petland-mega-menu', get_template_directory_uri() . '/dist/megaMenu.js', [], null, true);
    wp_enqueue_script('petland-sticky-header', get_template_directory_uri() . '/dist/stickyHeader.js', [], null, true);


    // Unload the plain CSS file so it is not called by Browsers (We use our own CSS in /dist/petland.css)
    wp_dequeue_style('theme-style');
    wp_deregister_style('theme-style');
    // add_editor_style('style.css');


    // Unload WooCommerce specific styling, since we are creating our own.
    // Note. WooCommerce might change their layout on rare occasions, in which case we might need to update our styling
    // wp_dequeue_style('wc-block-vendors-style');
    // wp_dequeue_style('wc-block-style');
    wp_dequeue_style('woocommerce-general');
    wp_dequeue_style('woocommerce-layout');
    wp_dequeue_style('woocommerce-smallscreen');
  }

  public function customize_register($wp_customize)
  {
    $wp_customize->add_setting('petland_sticky_logo', [
      'default'           => 0,
      'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control(new WP_Customize_Media_Control(
      $wp_customize,
      'petland_sticky_logo',
      [
        'label'    => __('Sticky Header Logo', 'petlandtextdomain'),
        'section'  => 'title_tagline',
        'settings' => 'petland_sticky_logo',
        'mime_type' => 'image',
      ]
    ));
  }

  public function theme_init()
  {
    load_theme_textdomain('petlandtextdomain', get_template_directory() . '/languages');

    register_block_type_from_metadata(
      get_template_directory() . '/blocks/latest-posts'
    );
    register_block_type_from_metadata(
      get_template_directory() . '/blocks/product-listing'
    );
    register_block_type_from_metadata(
      get_template_directory() . '/blocks/navigation'
    );
  }

  public function theme_setup()
  {
    register_nav_menus([
      'primary' => __('Primary Menu'),
      'footer'  => __('Footer Menu'),
    ]);

    foreach (
      [
        'custom-logo',
        'post-thumbnails',
        'woocommerce',
        'wc-product-gallery-zoom',
        'wc-product-gallery-lightbox',
        'wc-product-gallery-slider',
        'responsive-embeds',
        'align-wide',
        'editor-styles',
        'wp-block-styles',
      ] as $feature
    ) {
      add_theme_support($feature);
    }

    add_theme_support(
      'html5',
      [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
        'navigation-widgets',
      ]
    );
    add_theme_support('automatic-feed-links');
    add_post_type_support('page', 'excerpt');
    add_editor_style('/dist/petstyleEditor.css');
  }

  public function register_sidebars()
  {
    register_sidebar([
      'name'          => __('Primary Sidebar', 'petlandtextdomain'),
      'id'            => 'primary-sidebar',
      'before_widget' => '<div class="petland-widget">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="text-lg font-bold mb-2">',
      'after_title'   => '</h2>',
    ]);
  }

  public function petland_template_shortcode($atts)
  {
    $atts = shortcode_atts([
      'slug' => '',
    ], $atts, 'petland_template');

    if (!$atts['slug']) {
      return '';
    }

    $template = get_posts([
      'post_type'   => 'petland_template',
      'name'        => $atts['slug'],
      'post_status' => 'publish',
      'numberposts' => 1,
    ]);

    if (empty($template)) {
      return '';
    }

    return apply_filters('the_content', $template[0]->post_content);
  }

  public function example_allowed_block_types($allowed_blocks, $editor_context)
  {
    // Bail early if we’re not in a block editor context
    if (empty($editor_context)) {
      return $allowed_blocks;
    }

    // Define any blocks you want to *disallow*
    $disallowed_blocks = ['core/latest-posts'];

    // Get all registered block names (cheap, no recursion)
    $registry        = \WP_Block_Type_Registry::get_instance();
    $all_block_names = array_keys($registry->get_all_registered());

    // Filter out the disallowed ones
    $allowed_blocks = array_values(array_diff($all_block_names, $disallowed_blocks));
    return $allowed_blocks;
  }

  // -----------------------------------------------------
  // -------------- Migration stuff ----------------------
  // -----------------------------------------------------
  public static function replace_elementor_pages_by_id()
  {
    $pages = [
      get_option('woocommerce_shop_page_id')           => '<!-- wp:shortcode -->[products]<!-- /wp:shortcode -->',                 // Shop
      get_option('woocommerce_cart_page_id')           => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',         // Cart
      get_option('woocommerce_checkout_page_id')       => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',     // Checkout
      get_option('woocommerce_myaccount_page_id')      => '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->',   // My Account
    ];

    foreach ($pages as $id => $shortcode) {
      $page = get_post($id);
      if ($page) {
        wp_update_post([
          'ID' => $id,
          'post_content' => $shortcode,
        ]);
      }
    }
  }
}

new Theme();
