<?php
/**
 * Gadgets Mela 2.0 theme functions.
 *
 * @package GadgetsMelaTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

const GM_THEME_STORE_URL_DEFAULT = 'https://store.gadgetsmela2.com';
const GM_THEME_STORE_TEXT_DEFAULT = 'Visit Store';

function gm_theme_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 96,
        'width' => 320,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Header Menu', 'gadgets-mela-theme'),
    ]);
}
add_action('after_setup_theme', 'gm_theme_setup');

function gm_theme_enqueue_assets(): void {
    wp_enqueue_style('gadgets-mela-theme-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_script('gadgets-mela-theme-header', get_template_directory_uri() . '/assets/js/header.js', [], wp_get_theme()->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'gm_theme_enqueue_assets');

function gm_theme_customize_register(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('gm_theme_store_cta', [
        'title' => __('Store CTA Button', 'gadgets-mela-theme'),
        'description' => __('Control the premium header button that sends blog visitors to the Gadgets Mela store app.', 'gadgets-mela-theme'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('gm_store_cta_enabled', [
        'default' => true,
        'sanitize_callback' => 'gm_theme_sanitize_checkbox',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('gm_store_cta_enabled', [
        'label' => __('Enable Visit Store button', 'gadgets-mela-theme'),
        'section' => 'gm_theme_store_cta',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('gm_store_cta_text', [
        'default' => GM_THEME_STORE_TEXT_DEFAULT,
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('gm_store_cta_text', [
        'label' => __('Button text', 'gadgets-mela-theme'),
        'section' => 'gm_theme_store_cta',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('gm_store_app_url', [
        'default' => GM_THEME_STORE_URL_DEFAULT,
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('gm_store_app_url', [
        'label' => __('Store App URL', 'gadgets-mela-theme'),
        'section' => 'gm_theme_store_cta',
        'type' => 'url',
        'input_attrs' => [
            'placeholder' => GM_THEME_STORE_URL_DEFAULT,
        ],
    ]);
}
add_action('customize_register', 'gm_theme_customize_register');

function gm_theme_sanitize_checkbox($checked): bool {
    return (bool) $checked;
}

function gm_theme_store_cta_enabled(): bool {
    return (bool) get_theme_mod('gm_store_cta_enabled', true);
}

function gm_theme_store_cta_text(): string {
    $text = trim((string) get_theme_mod('gm_store_cta_text', GM_THEME_STORE_TEXT_DEFAULT));
    return $text !== '' ? $text : GM_THEME_STORE_TEXT_DEFAULT;
}

function gm_theme_store_app_url(): string {
    $url = trim((string) get_theme_mod('gm_store_app_url', GM_THEME_STORE_URL_DEFAULT));
    return $url !== '' ? $url : GM_THEME_STORE_URL_DEFAULT;
}

function gm_theme_store_cta(string $class = 'gm-store-cta'): void {
    if (!gm_theme_store_cta_enabled()) {
        return;
    }

    printf(
        '<a class="%1$s" href="%2$s">%3$s</a>',
        esc_attr($class),
        esc_url(gm_theme_store_app_url()),
        esc_html(gm_theme_store_cta_text())
    );
}

function gm_theme_fallback_menu($args = null): void {
    $links = [
        ['label' => __('Home', 'gadgets-mela-theme'), 'url' => home_url('/')],
        ['label' => __('Blog', 'gadgets-mela-theme'), 'url' => home_url('/blog/')],
        ['label' => __('Reviews', 'gadgets-mela-theme'), 'url' => home_url('/reviews/')],
        ['label' => __('Best Deals', 'gadgets-mela-theme'), 'url' => home_url('/best-deals/')],
        ['label' => __('Amazon Finds', 'gadgets-mela-theme'), 'url' => home_url('/amazon-finds/')],
        ['label' => __('Contact', 'gadgets-mela-theme'), 'url' => home_url('/contact/')],
    ];

    $menu_class = is_object($args) && !empty($args->menu_class) ? $args->menu_class : 'gm-menu';

    echo '<ul class="' . esc_attr($menu_class) . '">';
    foreach ($links as $link) {
        printf(
            '<li><a href="%1$s">%2$s</a></li>',
            esc_url($link['url']),
            esc_html($link['label'])
        );
    }
    echo '</ul>';
}
