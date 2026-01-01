<?php

namespace Petland\WooCommerce;

class Product_Category_Top_Description
{
    public function __construct()
    {
        add_action('product_cat_edit_form_fields', [$this, 'render_editor'], 10, 1);
        add_action('edited_product_cat', [$this, 'save_description'], 10, 1);
        add_action('create_product_cat', [$this, 'save_description'], 10, 1);
        add_action('woocommerce_before_main_content', [$this, 'display_description'], 8);
    }

    public function render_editor($term)
    {
        $content = get_term_meta($term->term_id, 'petland-top-description', true);
        ?>
        <tr class="form-field">
            <th scope="row"><?php _e('Top Description', 'petlandtextdomain'); ?></th>
            <td colspan="2">
                <?php
                wp_editor(
                    $content,
                    'petland-top-description-' . $term->term_id,
                    [
                        'textarea_name' => 'petland-top-description',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'tinymce'       => true,
                    ]
                );
                ?>
                <p class="description"><?php _e('This content will appear at the top of the category page.', 'petlandtextdomain'); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_description($term_id)
    {
        if (isset($_POST['petland-top-description'])) {
            update_term_meta($term_id, 'petland-top-description', wp_kses_post($_POST['petland-top-description']));
        }
    }

    public function display_description()
    {
        if (!is_product_category()) {
            return;
        }

        $term = get_queried_object();

        if ($term && !is_wp_error($term)) {
            echo '<h1 class="woocommerce-products-header__title page-title">' . esc_html($term->name) . '</h1>';

            if (!is_paged() && empty($_GET)) {
                $top_description = do_shortcode(get_term_meta($term->term_id, 'petland-top-description', true));
                if (!empty($top_description)) {
                    echo '<div class="petland-top-description">' . $top_description . '</div>';
                }
            }
        }
    }
}

new Product_Category_Top_Description();