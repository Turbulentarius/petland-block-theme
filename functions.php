<?php

/**
 * This is a classis Wordpress theme developed for Gutenberg, WooCommerce
 * 
 * @package Petland
 * @author Turbulentarius
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the theme dependencies
$theme_includes = [
    'lib/settings.php',
    'lib/theme.php',
    'lib/cpt/template/register.php',
    'lib/cpt/template/init.php',
    'includes/woocommerce/layout.php',
    'includes/woocommerce/woo-category-petland-top-description.php',
    'includes/woocommerce/tags-whitelist.php'
];

foreach ($theme_includes as $file) {
    $filepath = get_template_directory() . '/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

