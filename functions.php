<?php
add_action('template_redirect', 'storefront_child_counter');
// Counter of  reviews
function storefront_child_counter()
{
    global $post;

    if ('product' === get_post_type($post) && !isset($_COOKIE['product_' . $post->ID])) {
        setcookie('product_' . $post->ID, 1);
        $count = absint(get_post_meta($post->ID, 'counter', 1));
        if (empty($count)) {
            $count = 0;
        }

        $count++;
        update_post_meta($post->ID, 'counter', $count);
    }
}

add_action('woocommerce_checkout_order_processed', 'storefront_child_set_last_date');
// Date of last bayer
function storefront_child_set_last_date($order_id)
{
    $order = new WC_Order($order_id);
    if (count($order->get_items())) {
        $date = date('Y-m-d');
        foreach ($order->get_items() as $key => $value) {
            update_post_meta($value->get_product_id(), 'last_date', $date);
        }
    }
}

add_filter('manage_product_posts_columns', 'storefront_child_product_table_columns');
// Add products $columns
function storefront_child_product_table_columns($columns)
{
    $columns['counter'] = __('Reviews', 'storefront-child');
    $columns['last_date'] = __('Last day of buy', 'storefront-child');

    return $columns;
}

add_action('manage_posts_custom_column', 'storefront_child_column_content', 10, 2);
// Print column content
function storefront_child_column_content($column, $post_id)
{
    if ($column === 'counter' || $column === 'last_date') {
        $value = get_post_meta($post_id, $column, 1);
        echo !empty($value) ? esc_html($value) : 0;
    }
}
