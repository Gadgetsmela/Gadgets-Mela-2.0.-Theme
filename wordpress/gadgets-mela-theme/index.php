<?php
/**
 * Main theme template.
 *
 * @package GadgetsMelaTheme
 */

get_header();
?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('gm-post-card'); ?>>
            <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>
    <?php the_posts_pagination(); ?>
<?php else : ?>
    <section class="gm-post-card">
        <h1><?php esc_html_e('Fresh gadget stories are coming soon.', 'gadgets-mela-theme'); ?></h1>
        <p><?php esc_html_e('Visit the Gadgets Mela store for the latest curated affiliate deals.', 'gadgets-mela-theme'); ?></p>
        <?php gm_theme_store_cta(); ?>
    </section>
<?php endif; ?>

<?php
get_footer();
