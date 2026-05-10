<?php
/**
 * Theme header with premium Visit Store CTA.
 *
 * @package GadgetsMelaTheme
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="gm-site-header" id="site-header">
    <div class="gm-header-inner">
        <div class="gm-logo">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>

        <nav class="gm-desktop-nav" aria-label="<?php esc_attr_e('Header navigation', 'gadgets-mela-theme'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'gm-menu',
                'fallback_cb' => 'gm_theme_fallback_menu',
                'depth' => 1,
            ]);
            gm_theme_store_cta();
            ?>
        </nav>

        <button class="gm-mobile-toggle" type="button" aria-controls="gm-mobile-menu" aria-expanded="false">
            <span class="gm-mobile-toggle-icon" aria-hidden="true"></span>
            <span><?php esc_html_e('Menu', 'gadgets-mela-theme'); ?></span>
        </button>
    </div>

    <div class="gm-mobile-panel" id="gm-mobile-menu">
        <div class="gm-mobile-panel-inner">
            <?php gm_theme_store_cta(); ?>
            <nav aria-label="<?php esc_attr_e('Mobile header navigation', 'gadgets-mela-theme'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'gm-mobile-menu',
                    'fallback_cb' => 'gm_theme_fallback_menu',
                    'depth' => 1,
                ]);
                ?>
            </nav>
        </div>
    </div>
</header>
<main class="gm-main" id="content">
