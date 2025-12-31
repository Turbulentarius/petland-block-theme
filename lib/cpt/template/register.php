<?php

/**
 * Creates the petland_template Custom Post Type, which is used to manage
 * content in the 404, header, and top-description of category pages.
 * It also allows the creation of custom templates, which can be inserted relevant places using shortcodes.
 * 
 * @package Petland
 * @author Turbulentarius
 */

namespace Petland\Cpt\Template;

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

abstract class Petland_Templates
{
  abstract public function template_seeder();

  public function __construct()
  {
    add_action('init', [$this, 'register_cpt'], 10);
    add_action('init', [$this, 'template_seeder'], 20);

    if (is_admin()) {
      add_filter('manage_petland_template_posts_columns', [$this, 'add_shortcode_column']);
      add_action('manage_petland_template_posts_custom_column', [$this, 'render_shortcode_column'], 10, 2);
    }
  }

  public function register_cpt()
  {
    register_post_type('petland_template', [
      'labels' => [
        'name' => 'Templates',
        'singular_name' => 'Template',
      ],
      'public' => false,             // Not publicly queryable
      'show_in_rest' => true,        // Gutenberg/editor support
      'show_ui' => true,             // Still appears in admin
      'show_in_menu' => true,        // Admin menu
      'has_archive' => false,
      'supports' => ['title', 'editor'],
      'menu_icon' => 'dashicons-layout',
      'rewrite' => false, // No front-end URL
      'exclude_from_search' => true // Do not show in search
    ]);

    // Default block layout
    $post_type_object = get_post_type_object('petland_template');
    $post_type_object->template = array(
      array('core/heading'),
      array('core/image'),
      array('core/paragraph')
    );
  }

  public function add_shortcode_column($columns)
  {
    $columns['shortcode'] = __('Shortcode', 'petlandtextdomain');
    return $columns;
  }
  public function render_shortcode_column($column, $post_id)
  {
    if ($column === 'shortcode') {
      $slug = get_post_field('post_name', $post_id);
      $value = '[petland_template slug="' . esc_attr($slug) . '"]';

      echo '<input type="text" readonly value="' . esc_attr($value) . '" onclick="this.select();" style="width:100%;font-family:monospace;">';
    }
  }
}
