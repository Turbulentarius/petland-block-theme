<?php
/**
 * Allows to change various settings in the customizer
 * @package Petland
 * @author Turbulentarius
 * 
 */

namespace Petland;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings
{
    public function __construct()
    {
        add_action('customize_register', [$this, 'register_customizer']);
        add_filter('loop_shop_per_page', [$this, 'set_results_per_page'], 10);
    }

    public function register_customizer($wp_customize)
    {
        $wp_customize->add_section('petland_shop', [
            'title'    => __('Shop Settings', 'petlandtextdomain'),
            'priority' => 30,
        ]);

        $wp_customize->add_setting('petland_results_per_page', [
            'default'           => 24,
            'sanitize_callback' => 'absint',
        ]);

        $wp_customize->add_control('petland_results_per_page', [
            'label'       => __('Products per page', 'petlandtextdomain'),
            'section'     => 'petland_shop',
            'type'        => 'number',
            'input_attrs' => [
                'min' => 1,
                'max' => 100,
            ],
        ]);
    }

    public function set_results_per_page($results_per_page)
    {
        return get_theme_mod('petland_results_per_page', 24);
    }
}

new Settings();