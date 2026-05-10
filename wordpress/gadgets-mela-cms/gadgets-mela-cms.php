<?php
/**
 * Plugin Name: Gadgets Mela CMS
 * Description: Turns WordPress into the product/deal CMS for the Gadgets Mela Next.js storefront.
 * Version: 1.0.0
 * Author: Gadgets Mela
 * Text Domain: gadgets-mela
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Gadgets_Mela_CMS {
    private const META_PREFIX = '_gm_';
    private const OPTION_KEY = 'gadgets_mela_settings';

    public function __construct() {
        add_action('init', [$this, 'register_product_deal_post_type']);
        add_action('init', [$this, 'register_product_category_taxonomy']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('add_meta_boxes', [$this, 'register_product_fields']);
        add_action('save_post_product_deal', [$this, 'save_product_fields']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('customize_register', [$this, 'register_customizer_settings']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function register_product_deal_post_type(): void {
        $labels = [
            'name' => __('Product Deals', 'gadgets-mela'),
            'singular_name' => __('Product Deal', 'gadgets-mela'),
            'menu_name' => __('Products', 'gadgets-mela'),
            'add_new_item' => __('Add Product', 'gadgets-mela'),
            'edit_item' => __('Edit Product Deal', 'gadgets-mela'),
            'new_item' => __('New Product Deal', 'gadgets-mela'),
            'view_item' => __('View Product Deal', 'gadgets-mela'),
            'search_items' => __('Search Product Deals', 'gadgets-mela'),
        ];

        register_post_type('product_deal', [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'rest_base' => 'product_deal',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive' => false,
            'rewrite' => false,
            'capability_type' => 'post',
        ]);
    }

    public function register_product_category_taxonomy(): void {
        register_taxonomy('product_deal_category', ['product_deal'], [
            'labels' => [
                'name' => __('Categories', 'gadgets-mela'),
                'singular_name' => __('Category', 'gadgets-mela'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
        ]);
    }

    public function register_admin_menu(): void {
        add_menu_page(
            __('Gadgets Mela', 'gadgets-mela'),
            __('Gadgets Mela', 'gadgets-mela'),
            'edit_posts',
            'gadgets-mela',
            [$this, 'render_settings_page'],
            'dashicons-cart',
            26
        );

        add_submenu_page('gadgets-mela', __('Products', 'gadgets-mela'), __('Products', 'gadgets-mela'), 'edit_posts', 'edit.php?post_type=product_deal');
        add_submenu_page('gadgets-mela', __('Add Product', 'gadgets-mela'), __('Add Product', 'gadgets-mela'), 'edit_posts', 'post-new.php?post_type=product_deal');
        add_submenu_page('gadgets-mela', __('Categories', 'gadgets-mela'), __('Categories', 'gadgets-mela'), 'manage_categories', 'edit-tags.php?taxonomy=product_deal_category&post_type=product_deal');
        add_submenu_page('gadgets-mela', __('Settings', 'gadgets-mela'), __('Settings', 'gadgets-mela'), 'manage_options', 'gadgets-mela-settings', [$this, 'render_settings_page']);
    }

    public function register_product_fields(): void {
        add_meta_box(
            'gadgets_mela_product_fields',
            __('Gadgets Mela Product Details', 'gadgets-mela'),
            [$this, 'render_product_fields'],
            'product_deal',
            'normal',
            'high'
        );
    }

    private function field_definitions(): array {
        return [
            'brand' => ['label' => __('Brand name', 'gadgets-mela'), 'type' => 'text'],
            'short_description' => ['label' => __('Short description', 'gadgets-mela'), 'type' => 'textarea'],
            'image' => ['label' => __('Product image URL', 'gadgets-mela'), 'type' => 'url'],
            'amazon_url' => ['label' => __('Amazon affiliate link', 'gadgets-mela'), 'type' => 'url'],
            'regular_price' => ['label' => __('Regular price', 'gadgets-mela'), 'type' => 'number', 'step' => '0.01'],
            'sale_price' => ['label' => __('Sale price', 'gadgets-mela'), 'type' => 'number', 'step' => '0.01'],
            'discount' => ['label' => __('Discount percentage', 'gadgets-mela'), 'type' => 'number', 'step' => '1'],
            'rating' => ['label' => __('Rating', 'gadgets-mela'), 'type' => 'number', 'step' => '0.1', 'min' => '0', 'max' => '5'],
            'reviews' => ['label' => __('Review count', 'gadgets-mela'), 'type' => 'number', 'step' => '1'],
            'is_hot_deal' => ['label' => __('Hot deal', 'gadgets-mela'), 'type' => 'checkbox'],
            'is_featured' => ['label' => __('Featured', 'gadgets-mela'), 'type' => 'checkbox'],
            'trending_score' => ['label' => __('Trending score', 'gadgets-mela'), 'type' => 'number', 'step' => '1'],
            'whatsapp_message' => ['label' => __('WhatsApp message', 'gadgets-mela'), 'type' => 'textarea'],
            'updated_date' => ['label' => __('Updated date', 'gadgets-mela'), 'type' => 'date'],
        ];
    }

    public function render_product_fields(WP_Post $post): void {
        wp_nonce_field('gadgets_mela_save_product', 'gadgets_mela_product_nonce');
        echo '<table class="form-table" role="presentation"><tbody>';
        foreach ($this->field_definitions() as $key => $field) {
            $value = get_post_meta($post->ID, self::META_PREFIX . $key, true);
            $id = 'gm_' . esc_attr($key);
            echo '<tr><th scope="row"><label for="' . esc_attr($id) . '">' . esc_html($field['label']) . '</label></th><td>';
            if ($field['type'] === 'textarea') {
                echo '<textarea class="large-text" rows="3" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '">' . esc_textarea($value) . '</textarea>';
            } elseif ($field['type'] === 'checkbox') {
                echo '<label><input type="checkbox" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="1" ' . checked($value, '1', false) . '> ' . esc_html__('Yes', 'gadgets-mela') . '</label>';
            } else {
                $step = isset($field['step']) ? ' step="' . esc_attr($field['step']) . '"' : '';
                $min = isset($field['min']) ? ' min="' . esc_attr($field['min']) . '"' : '';
                $max = isset($field['max']) ? ' max="' . esc_attr($field['max']) . '"' : '';
                echo '<input class="regular-text" type="' . esc_attr($field['type']) . '" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '"' . $step . $min . $max . '>';
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }

    public function save_product_fields(int $post_id): void {
        if (!isset($_POST['gadgets_mela_product_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gadgets_mela_product_nonce'])), 'gadgets_mela_save_product')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach ($this->field_definitions() as $key => $field) {
            $input_key = 'gm_' . $key;
            if ($field['type'] === 'checkbox') {
                update_post_meta($post_id, self::META_PREFIX . $key, isset($_POST[$input_key]) ? '1' : '0');
                continue;
            }
            $raw_value = isset($_POST[$input_key]) ? wp_unslash($_POST[$input_key]) : '';
            if ($field['type'] === 'url') {
                $value = esc_url_raw($raw_value);
            } elseif ($field['type'] === 'textarea') {
                $value = sanitize_textarea_field($raw_value);
            } elseif ($field['type'] === 'number') {
                $value = is_numeric($raw_value) ? (string) $raw_value : '';
            } else {
                $value = sanitize_text_field($raw_value);
            }
            update_post_meta($post_id, self::META_PREFIX . $key, $value);
        }
    }

    public function register_settings(): void {
        register_setting('gadgets_mela_settings_group', self::OPTION_KEY, [$this, 'sanitize_settings']);
        add_settings_section('gadgets_mela_general', __('Storefront Settings', 'gadgets-mela'), '__return_false', 'gadgets-mela-settings');

        foreach ($this->settings_definitions() as $key => $field) {
            add_settings_field($key, $field['label'], [$this, 'render_setting_field'], 'gadgets-mela-settings', 'gadgets_mela_general', ['key' => $key, 'field' => $field]);
        }
    }

    private function settings_definitions(): array {
        return [
            'logo' => ['label' => __('Logo URL', 'gadgets-mela'), 'type' => 'url'],
            'whatsapp_number' => ['label' => __('WhatsApp number', 'gadgets-mela'), 'type' => 'text'],
            'amazon_affiliate_tag' => ['label' => __('Amazon affiliate tag', 'gadgets-mela'), 'type' => 'text'],
            'store_app_url' => ['label' => __('Store app URL', 'gadgets-mela'), 'type' => 'url'],
            'brand_primary_color' => ['label' => __('Primary brand color', 'gadgets-mela'), 'type' => 'color'],
            'brand_accent_color' => ['label' => __('Accent brand color', 'gadgets-mela'), 'type' => 'color'],
            'cta_text' => ['label' => __('CTA text', 'gadgets-mela'), 'type' => 'text'],
        ];
    }

    public function render_setting_field(array $args): void {
        $settings = get_option(self::OPTION_KEY, []);
        $key = $args['key'];
        $field = $args['field'];
        $value = $settings[$key] ?? '';
        echo '<input class="regular-text" type="' . esc_attr($field['type']) . '" name="' . esc_attr(self::OPTION_KEY . '[' . $key . ']') . '" value="' . esc_attr($value) . '">';
    }

    public function sanitize_settings(array $settings): array {
        $clean = [];
        foreach ($this->settings_definitions() as $key => $field) {
            $value = $settings[$key] ?? '';
            if ($field['type'] === 'url') {
                $clean[$key] = esc_url_raw($value);
            } elseif ($field['type'] === 'color') {
                $clean[$key] = sanitize_hex_color($value) ?: '';
            } else {
                $clean[$key] = sanitize_text_field($value);
            }
        }
        return $clean;
    }

    public function render_settings_page(): void {
        echo '<div class="wrap"><h1>' . esc_html__('Gadgets Mela Settings', 'gadgets-mela') . '</h1><form method="post" action="options.php">';
        settings_fields('gadgets_mela_settings_group');
        do_settings_sections('gadgets-mela-settings');
        submit_button();
        echo '</form></div>';
    }

    public function register_customizer_settings(WP_Customize_Manager $wp_customize): void {
        $wp_customize->add_section('gadgets_mela_storefront', [
            'title' => __('Gadgets Mela Storefront', 'gadgets-mela'),
            'priority' => 35,
        ]);

        foreach ($this->settings_definitions() as $key => $field) {
            $setting_id = self::OPTION_KEY . '[' . $key . ']';
            $wp_customize->add_setting($setting_id, [
                'type' => 'option',
                'sanitize_callback' => $field['type'] === 'url' ? 'esc_url_raw' : 'sanitize_text_field',
            ]);
            $wp_customize->add_control($setting_id, [
                'label' => $field['label'],
                'section' => 'gadgets_mela_storefront',
                'type' => $field['type'] === 'color' ? 'color' : 'text',
            ]);
        }
    }

    public function register_rest_routes(): void {
        register_rest_route('gadgets-mela/v1', '/products', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_products'],
            'permission_callback' => '__return_true',
            'args' => [
                'category' => ['sanitize_callback' => 'sanitize_text_field'],
                'hot' => ['sanitize_callback' => 'rest_sanitize_boolean'],
                'featured' => ['sanitize_callback' => 'rest_sanitize_boolean'],
                'trending' => ['sanitize_callback' => 'rest_sanitize_boolean'],
            ],
        ]);
    }

    public function get_products(WP_REST_Request $request): WP_REST_Response {
        $meta_query = ['relation' => 'AND'];
        if ($request->get_param('hot')) {
            $meta_query[] = ['key' => self::META_PREFIX . 'is_hot_deal', 'value' => '1'];
        }
        if ($request->get_param('featured')) {
            $meta_query[] = ['key' => self::META_PREFIX . 'is_featured', 'value' => '1'];
        }
        if ($request->get_param('trending')) {
            $meta_query[] = ['key' => self::META_PREFIX . 'trending_score', 'value' => 0, 'compare' => '>', 'type' => 'NUMERIC'];
        }

        $query_args = [
            'post_type' => 'product_deal',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => 'modified',
            'order' => 'DESC',
        ];
        if (count($meta_query) > 1) {
            $query_args['meta_query'] = $meta_query;
        }
        if ($request->get_param('category')) {
            $query_args['tax_query'] = [[
                'taxonomy' => 'product_deal_category',
                'field' => 'slug',
                'terms' => $request->get_param('category'),
            ]];
        }

        $products = array_map([$this, 'format_product'], get_posts($query_args));
        return rest_ensure_response($products);
    }

    private function format_product(WP_Post $post): array {
        $terms = get_the_terms($post->ID, 'product_deal_category');
        $category = (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->name : '';
        $image = get_post_meta($post->ID, self::META_PREFIX . 'image', true) ?: get_the_post_thumbnail_url($post->ID, 'large');
        $description = get_post_meta($post->ID, self::META_PREFIX . 'short_description', true) ?: get_the_excerpt($post);
        $updated = get_post_meta($post->ID, self::META_PREFIX . 'updated_date', true) ?: get_the_modified_date(DATE_ATOM, $post);

        return [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'brand' => get_post_meta($post->ID, self::META_PREFIX . 'brand', true),
            'category' => $category,
            'description' => $description,
            'image' => $image ?: '',
            'amazonUrl' => get_post_meta($post->ID, self::META_PREFIX . 'amazon_url', true),
            'regularPrice' => (float) get_post_meta($post->ID, self::META_PREFIX . 'regular_price', true),
            'salePrice' => (float) get_post_meta($post->ID, self::META_PREFIX . 'sale_price', true),
            'discount' => (int) get_post_meta($post->ID, self::META_PREFIX . 'discount', true),
            'rating' => (float) get_post_meta($post->ID, self::META_PREFIX . 'rating', true),
            'reviews' => (int) get_post_meta($post->ID, self::META_PREFIX . 'reviews', true),
            'isHotDeal' => get_post_meta($post->ID, self::META_PREFIX . 'is_hot_deal', true) === '1',
            'isFeatured' => get_post_meta($post->ID, self::META_PREFIX . 'is_featured', true) === '1',
            'trendingScore' => (int) get_post_meta($post->ID, self::META_PREFIX . 'trending_score', true),
            'whatsappMessage' => get_post_meta($post->ID, self::META_PREFIX . 'whatsapp_message', true),
            'updatedAt' => $updated,
        ];
    }
}

new Gadgets_Mela_CMS();
