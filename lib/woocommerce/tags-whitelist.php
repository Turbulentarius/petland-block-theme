<?php

namespace Petland\WooCommerce;

if (!defined('ABSPATH')) exit;

class Category_Filter_Tags
{
  public function __construct()
  {
    add_action('product_cat_edit_form_fields', [$this, 'render_field']);
    add_action('edited_product_cat', [$this, 'save_field']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
  }

  public function render_field($term)
  {
    $selected = (array) get_term_meta($term->term_id, 'filter_tags', true);
    $tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false, 'orderby' => 'name']);

    echo '<tr class="form-field">';
    echo '<th scope="row"><label>' . esc_html__('Filter Tags', 'petlandtextdomain') . '</label></th>';
    echo '<td>';
    echo '<div id="petland-filter-tags-wrap">';

    echo '<div class="filter-box" style="display:flex;gap:4px;margin-bottom:6px;">';
    echo '<input type="text" id="filter_tags_search" list="filter_tags_datalist" placeholder="' . esc_attr__('Type to search…', 'petlandtextdomain') . '" style="flex:1;">';
    echo '<button type="button" class="button" id="filter_tags_add">' . esc_html__('Add', 'petlandtextdomain') . '</button>';
    echo '</div>';

    echo '<datalist id="filter_tags_datalist">';
    foreach ($tags as $tag) {
      echo '<option value="' . esc_attr($tag->name) . '" data-id="' . esc_attr($tag->term_id) . '">';
    }
    echo '</datalist>';

    echo '<ul id="filter_tags_list">';
    foreach ($selected as $id) {
      $tag = get_term($id, 'product_tag');
      if ($tag && !is_wp_error($tag)) {
        echo '<li data-id="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . ' ✕</li>';
      }
    }
    echo '</ul>';

    // Hidden input so form submission works
    echo '<input type="hidden" id="filter_tags_hidden" name="filter_tags" value="' . esc_attr(implode(',', $selected)) . '">';
    echo '<p class="description">' . esc_html__('Type or pick tags, click “Add”. Click a tag to remove it.', 'petlandtextdomain') . '</p>';

    echo '</div></td></tr>';
  }

  public function save_field($term_id)
  {
    if (empty($_POST['filter_tags'])) {
      delete_term_meta($term_id, 'filter_tags');
      return;
    }
    $ids = array_map('intval', array_filter(explode(',', sanitize_text_field($_POST['filter_tags']))));
    update_term_meta($term_id, 'filter_tags', $ids);
  }

  public static function get_whitelisted_tags($term_id)
  {
    $ids = get_term_meta($term_id, 'filter_tags', true);
    if (!is_array($ids)) return [];
    return get_terms([
      'taxonomy' => 'product_tag',
      'include' => $ids,
      'orderby' => 'name',
      'hide_empty' => false,
    ]);
  }

  public function enqueue_assets($hook)
  {
    if (!in_array($hook, ['term.php', 'edit-tags.php'], true)) return;
    $screen = get_current_screen();
    if (empty($screen->taxonomy) || $screen->taxonomy !== 'product_cat') return;

    add_action('admin_enqueue_scripts', function ($hook_suffix) {
      $screen = get_current_screen();

      if (!$screen) {
        return;
      }

      if ($screen->base === 'term' && $screen->taxonomy === 'product_cat') {
        wp_enqueue_script('petland-tags-admin', get_template_directory_uri() . '/dist/tagsFilterAdmin.js', [], null, true);
        
      }
      
      wp_enqueue_style('petland-woo-admin-stylesheet', get_template_directory_uri() . '/dist/petWoocommerceAdmin.css', [], filemtime(get_template_directory() . '/dist/petWoocommerceAdmin.css'));
    }, 20);
  }
}


new Category_Filter_Tags();
