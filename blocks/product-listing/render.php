<?php

namespace Petland\Blocks;

use WP_Term;

$current_cat = get_queried_object() instanceof WP_Term ? get_queried_object() : null;

$count = isset($attributes['postsToShow']) ? (int) $attributes['postsToShow'] : 10;
$align = isset($attributes['align']) ? 'align' . $attributes['align'] : '';
$category_id = isset($current_cat->term_id) ? (int) $current_cat->term_id : 0;

$classes = ['petland-woo-products'];

if ($align) {
  $classes[] = $align;
}

if (!empty($attributes['backgroundColor'])) {
  $classes[] = 'has-' . sanitize_html_class($attributes['backgroundColor']) . '-background-color';
}
if (!empty($attributes['textColor'])) {
  $classes[] = 'has-' . sanitize_html_class($attributes['textColor']) . '-color';
}

$style = '';
if (!empty($attributes['style']['color']['background'])) {
  $style .= 'background-color:' . esc_attr($attributes['style']['color']['background']) . ';';
}
if (!empty($attributes['style']['color']['text'])) {
  $style .= 'color:' . esc_attr($attributes['style']['color']['text']) . ';';
}

$paged = (int) max(1, get_query_var('paged'), get_query_var('page'));

$ordering_args = WC()->query->get_catalog_ordering_args();

$args = [
  'post_type'           => 'product',
  'posts_per_page'      => $count,
  'post_status'         => 'publish',
  'ignore_sticky_posts' => 1,
  'paged'               => $paged,
  'meta_query'          => WC()->query->get_meta_query(),
  'tax_query'           => WC()->query->get_tax_query(),
  'orderby'             => $ordering_args['orderby'],
  'order'               => $ordering_args['order'],
];

if (!empty($ordering_args['meta_key'])) {
  $args['meta_key'] = $ordering_args['meta_key'];
}

if ($category_id) {
  $args['tax_query'] = [
    [
      'taxonomy' => 'product_cat',
      'field'    => 'term_id',
      'terms'    => [$category_id],
      'operator' => 'IN',
    ],
  ];
}

$q = new \WP_Query($args);

$pagination = paginate_links([
  'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
  'format'    => '',
  'current'   => $paged,
  'total'     => max(1, (int) $q->max_num_pages),
  'type'      => 'list',
  'prev_text' => '←',
  'next_text' => '→',
]);


if ($pagination) {
  echo '<nav class="wp-block-petland-product-listing-pagination">' . wp_kses_post($pagination) . '</nav>';
}

echo '<ul class="' . esc_attr(implode(' ', $classes)) . '"' . ($style ? ' style="' . esc_attr($style) . '"' : '') . '>';

while ($q->have_posts()) {
  $q->the_post();
  global $product;

  if (!$product instanceof \WC_Product) {
    continue;
  }

  $post_classes = implode(' ', wc_get_product_class('', $product));

  echo '<li class="' . esc_attr($post_classes) . '">';
  echo '<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="' . esc_url(get_permalink()) . '">';
    woocommerce_template_loop_product_thumbnail();
    woocommerce_template_loop_product_title();
  echo '</a>';

  woocommerce_template_loop_price();
  woocommerce_template_loop_add_to_cart();

  echo '</li>';
}

echo '</ul>';

if ($pagination) {
  echo '<nav class="wp-block-petland-product-listing-pagination">' . wp_kses_post($pagination) . '</nav>';
}

wp_reset_postdata();
