<?php

namespace Petland\Blocks;

$count = isset($attributes['postsToShow']) ? (int) $attributes['postsToShow'] : 6;
$align = isset($attributes['align']) ? 'align' . $attributes['align'] : '';

$classes = ['wp-block-petland-latest-posts', 'grid', 'gap-6', 'md:grid-cols-4', 'list-none', 'p-0', 'm-0'];

// Add alignment
if ($align) {
  $classes[] = $align;
}

// Add background/text color classes (if palette colors are used)
if (!empty($attributes['backgroundColor'])) {
  $classes[] = 'has-' . sanitize_html_class($attributes['backgroundColor']) . '-background-color';
}
if (!empty($attributes['textColor'])) {
  $classes[] = 'has-' . sanitize_html_class($attributes['textColor']) . '-color';
}

// Inline styles (for custom color pickers)
$style = '';
if (!empty($attributes['style']['color']['background'])) {
  $style .= 'background-color:' . esc_attr($attributes['style']['color']['background']) . ';';
}
if (!empty($attributes['style']['color']['text'])) {
  $style .= 'color:' . esc_attr($attributes['style']['color']['text']) . ';';
}

$q = new \WP_Query(['posts_per_page' => $count, 'post_status' => 'publish']);

echo '<ul class="' . esc_attr(implode(' ', $classes)) . '"' . ($style ? ' style="' . esc_attr($style) . '"' : '') . '>';

while ($q->have_posts()) {
  $q->the_post();
  echo '<li>';
  echo '<a href="' . esc_url(get_permalink()) . '">';
  if (has_post_thumbnail()) {
    echo get_the_post_thumbnail(null, 'medium');
  }
  echo '<h2>' . esc_html(get_the_title()) . '</h2>';
  echo '<p>' . esc_html(get_the_date()) . '</p></a>';
  echo '</li>';
}

echo '</ul>';
wp_reset_postdata();
