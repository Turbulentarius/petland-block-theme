<?php

/**
 * Will create and lock standard templates that are used by the Petland theme.
 * E.g. Locking means that the templates cannot be deleted in Wordpress administration, only editing is allowed to prevent errors.
 * @package Petland
 * @author Turbulentarius
 */

namespace Petland\Cpt\Template;

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

class Petland_Templates_init extends Petland_Templates
{

  /**
   * @var string[] $default_templates
   *   Map of seeding method names to post slugs for default templates.
   *   Key   = method name that creates the template.
   *   Value = corresponding post_name (slug) of the template.
   */
  private array $default_templates = [
    'create_default_404'    => 'petland-template-404',
    'create_default_footer' => 'petland-template-footer',
    'create_default_user_content' => 'petland-template-header-user-content',
    'create_default_header_top' => 'petland-template-header-top-content',
    'create_default_product_content' => 'petland-template-product-content'
  ];

  public function template_seeder()
  {
    foreach ($this->default_templates as $method_name => $template_name) {
      add_action('init', [$this, $method_name], 25);
    }
    add_filter('post_row_actions', [$this, 'protect_default_templates'], 10, 2);
  }

  public function create_default_404()
  {

    if ($this->post_exist('petland-template-404')) {
      return;
    }

    $imageId = $this->insert_theme_image('404.png', 'Default 404 Image');
    $image   = wp_get_attachment_image_src($imageId, 'medium_large');
    $alt     = trim(strip_tags(get_post_meta($imageId, '_wp_attachment_image_alt', true)));

    // For images, different sizes are possible: thumbnail, medium, medium_large, large, full
    wp_insert_post([
      'post_title'   => 'Template: 404 - ' . esc_html__('Not Found', 'petlandtextdomain'),
      'post_type'    => 'petland_template',
      'post_name'    => 'petland-template-404',
      'post_status'  => 'publish',
      'post_content' => '
          <!-- wp:heading {"lock":{"move":false,"remove":true}} -->
            <h2>' . esc_html__('Not found...', 'petlandtextdomain') . '</h2>
          <!-- /wp:heading -->
          <!-- wp:image {"sizeSlug":"medium_large","linkDestination":"none"} -->
			      <figure class="wp-block-image size-medium_large petland-404-image">
				      <img src="' . esc_url($image[0]) . '" alt="' . esc_attr($alt) . '" />
			      </figure>
			    <!-- /wp:image -->
          <!-- wp:paragraph -->' . esc_html__('The requested resource was not found...', 'petlandtextdomain') . '<!-- /wp:paragraph -->',
    ]);
  }

  public function create_default_footer()
  {

    if ($this->post_exist('petland-template-footer')) {
      return;
    }

    $columns = '';

    for ($i = 1; $i <= 5; $i++) {
      $columns .= '
            <!-- wp:column -->
            <div class="wp-block-column">
                <!-- wp:heading {"level":2} -->
                <h2>Column Heading</h2>
                <!-- /wp:heading -->

                <!-- wp:list {"ordered":true} -->
                <ol>
                    <li><a href="#">Link 1</a></li>
                    <li><a href="#">Link 2</a></li>
                </ol>
                <!-- /wp:list -->
            </div>
            <!-- /wp:column -->';
    }

    $content = '
        <!-- wp:columns -->
        <div class="wp-block-columns">' . $columns . '
        </div>
        <!-- /wp:columns -->';

    wp_insert_post([
      'post_title'   => 'Template: Footer',
      'post_type'    => 'petland_template',
      'post_name'    => 'petland-template-footer',
      'post_status'  => 'publish',
      'post_content' => $content,
    ]);
  }
  public function create_default_header_top()
  {
    if ($this->post_exist('petland-template-header-top-content')) {
      return;
    }

    wp_insert_post([
      'post_title'   => 'Template: Header - ' . esc_html__('Top content', 'petlandtextdomain'),
      'post_type'    => 'petland_template',
      'post_name'    => 'petland-template-header-top-content',
      'post_status'  => 'publish',
      'post_content' => '',
    ]);
  }

  public function create_default_user_content()
  {

    if ($this->post_exist('petland-template-header-user-content')) {
      return;
    }

    $content = '<!-- wp:woocommerce/customer-account {"iconClass":"wc-block-customer-account__account-icon","className":"my_account_button"} /-->
<!-- wp:woocommerce/mini-cart {"hasHiddenPrice":false,"productCountVisibility":"always"} /-->';

    wp_insert_post([
      'post_title'   => 'Template: Header - ' . esc_html__('user content', 'petlandtextdomain'),
      'post_type'    => 'petland_template',
      'post_name'    => 'petland-template-header-user-content',
      'post_status'  => 'publish',
      'post_content' => $content,
    ]);
  }

  public function create_default_product_content()
  {

    if ($this->post_exist('petland-template-product-content')) {
      return;
    }
    wp_insert_post([
      'post_title'   => 'Template: ' . esc_html__('Product content', 'petlandtextdomain'),
      'post_type'    => 'petland_template',
      'post_name'    => 'petland-template-product-content',
      'post_status'  => 'publish',
      'post_content' => '',
    ]);
  }

  public function protect_default_templates($actions_or_caps, $post_or_caps, $args = [], $user = null)
  {
    // Admin list table: hide Trash/Delete links
    if (is_array($actions_or_caps) && isset($post_or_caps->post_type)) {
      $post = $post_or_caps;
      if ($post->post_type === 'petland_template' && in_array($post->post_name, $this->default_templates, true)) {
        unset($actions_or_caps['trash']);
        unset($actions_or_caps['delete']);
      }
      return $actions_or_caps;
    }

    // Capability check: block deletion
    if (is_array($actions_or_caps) && !empty($args[0]) && in_array($args[0], ['delete_post', 'delete_page'])) {
      $post_id = $args[2] ?? 0;
      $post = get_post($post_id);
      if ($post && $post->post_type === 'petland_template' && in_array($post->post_name, $this->default_templates, true)) {
        $actions_or_caps[$args[0]] = false;
      }
      return $actions_or_caps;
    }

    return $actions_or_caps;
  }


  /**
   * Helper function to insert images
   * 
   */
  private function insert_theme_image($filename, $title = '')
  {
    $theme_dir = get_template_directory(); // path to theme
    $file = $theme_dir . '/assets/images/' . $filename;

    if (!file_exists($file)) {
      return false;
    }

    // Check if attachment with this file name already exists
    $existing = get_posts([
      'post_type'   => 'attachment',
      'meta_query'  => [
        [
          'key'     => '_wp_attachment_metadata',
          'value'   => $filename,
          'compare' => 'LIKE'
        ]
      ],
      'numberposts' => 1,
    ]);

    if (!empty($existing)) {
      return $existing[0]->ID;
    }

    // Copy the file into the uploads directory
    $upload = wp_upload_bits($filename, null, file_get_contents($file));
    if ($upload['error']) {
      return false;
    }

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = [
      'post_mime_type' => $wp_filetype['type'],
      'post_title'     => $title ?: sanitize_file_name($filename),
      'post_content'   => '',
      'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment($attachment, $upload['file']);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
  }

  private function post_exist(string $post_name)
  {
    /* @var WP_Post $existing */
    $existing = get_posts([
      'name'   => $post_name,
      'post_type'   => 'petland_template',
      'post_status' => 'any',
      'numberposts' => 1,
    ]);

    if (empty($existing)) {
      return false;
    }
    return true;
  }
}

new Petland_Templates_init();
