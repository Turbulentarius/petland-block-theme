<?php

namespace Petland\Blocks;

$menu = wp_nav_menu([
  'theme_location' => 'primary',
  'fallback_cb'    => false,
  'container'      => false,
  'echo'           => false,
]);

if ($menu) {
  $menu = preg_replace(
    '/(<li[^>]*class="[^"]*menu-item-has-children[^"]*"[^>]*>)(\s*<a[^>]*>[^<]*)(<\/a>)/',
    '$1$2<button class="submenu-toggle" aria-expanded="false"></button>$3',
    $menu
  );

?>
  <button class="nav-burger">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 7L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
      <path d="M20 12L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
      <path d="M20 17L4 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
    </svg>
  </button>
<?php

  echo '<nav class="petland-navigation">
    <button class="nav-close-btn" type="button">
    <span class="sr-only">' . esc_html__('Close menu', 'petlandtextdomain') . '</span>
      <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>';
  echo $menu;
  if ($custom_logo_id = get_theme_mod('custom_logo')) {
    echo '<div class="nav-logo-wrap">';
    echo '<a href="' . esc_url(home_url('/')) . '" ' . (is_front_page() ? 'aria-current="page"' : '') . '>'
      . wp_get_attachment_image($custom_logo_id, 'full', false, [
        'class' => 'custom-logo-in-nav',
      ])  . '</a>';
    echo '</div>';
  }
  echo '</nav>';
}
