<?php
/**
 * Theme footer.
 *
 * @package GadgetsMelaTheme
 */
?>
</main>
<footer class="gm-site-footer">
    <p><?php echo esc_html(sprintf(__('© %1$s %2$s. Premium gadget deals and reviews.', 'gadgets-mela-theme'), gmdate('Y'), get_bloginfo('name'))); ?></p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
