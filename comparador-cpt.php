<?php
/**
 * Plugin Name: Comparador (CPT) Administrable
 * Description: Crea un CPT "Listas de comparación" para Woodmart y permite elegir productos vía búsqueda AJAX, ordenar y usar por shortcode.
 * Author: intagono
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

/* =========================================================
 * 1) Custom Post Type: compare_list
 * ======================================================= */
add_action('init', function() {
    $labels = [
        'name'               => 'Listas de comparación',
        'singular_name'      => 'Lista de comparación',
        'menu_name'          => 'Comparador',
        'name_admin_bar'     => 'Lista de comparación',
        'add_new'            => 'Añadir nueva',
        'add_new_item'       => 'Añadir nueva lista',
        'new_item'           => 'Nueva lista',
        'edit_item'          => 'Editar lista',
        'view_item'          => 'Ver lista',
        'all_items'          => 'Listas',
        'search_items'       => 'Buscar listas',
        'not_found'          => 'No hay listas',
        'not_found_in_trash' => 'No hay listas en la papelera',
    ];

    register_post_type('compare_list', [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-randomize',
        'supports'           => ['title'],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    ]);
});

/* =========================================================
 * 2) Metabox: Productos de la lista (UI + AJAX)
 * ======================================================= */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'compare_products_box',
        'Productos de la lista',
        'compare_products_box_render',
        'compare_list',
        'normal',
        'high'
    );
});

function compare_products_box_render($post) {
    wp_nonce_field('compare_products_save', 'compare_products_nonce');

    $product_ids = (array) get_post_meta($post->ID, 'compare_products', true);
    $product_ids = array_values(array_filter(array_map('absint', $product_ids)));

    // Enqueue scripts/styles
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('compare-admin-js', plugin_dir_url(__FILE__) . 'js/compare-admin.js', ['jquery', 'jquery-ui-sortable'], '1.0.0', true);
    wp_localize_script('compare-admin-js', 'CompareAdmin', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('compare_ajax_nonce'),
    ]);

    wp_enqueue_style('compare-admin-css', plugin_dir_url(__FILE__) . 'css/compare-admin.css', [], '1.0.0');

    ?>
    <div class="compare-search">
        <input type="text" id="compare-search-input" placeholder="Buscar productos por nombre...">
        <button type="button" class="button" id="compare-search-btn">Buscar</button>
        <div id="compare-search-results"></div>
    </div>

    <h4>Productos seleccionados (arrastra para reordenar)</h4>
    <ul id="compare-selected" class="compare-sortable">
        <?php
        if (!empty($product_ids)) {
            $products = get_posts([
                'post_type' => 'product',
                'post__in'  => $product_ids,
                'orderby'   => 'post__in',
                'numberposts' => -1,
            ]);
            foreach ($products as $p) {
                printf(
                    '<li data-id="%d"><span class="title">%s</span><button type="button" class="button-link compare-remove">&times;</button><input type="hidden" name="compare_products[]" value="%d"></li>',
                    $p->ID,
                    esc_html($p->post_title),
                    $p->ID
                );
            }
        }
        ?>
    </ul>
    <p class="description">Se guardará el orden actual para el comparador.</p>
    <?php
}

add_action('save_post_compare_list', function ($post_id) {
    if (!isset($_POST['compare_products_nonce']) || !wp_verify_nonce($_POST['compare_products_nonce'], 'compare_products_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $ids = isset($_POST['compare_products']) ? (array) $_POST['compare_products'] : [];
    $ids = array_values(array_unique(array_filter(array_map('absint', $ids))));
    update_post_meta($post_id, 'compare_products', $ids);
}, 10, 1);

/* ====== AJAX: búsqueda de productos por título ====== */
add_action('wp_ajax_compare_search_products', function () {
    check_ajax_referer('compare_ajax_nonce', 'nonce');

    if (!current_user_can('edit_posts')) wp_send_json_error(['message' => 'No autorizado'], 403);

    $s = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
    $paged = max(1, absint($_GET['page'] ?? 1));

    $args = [
        'post_type'      => 'product',
        's'              => $s,
        'posts_per_page' => 10,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ];
    $query = new WP_Query($args);

    $items = [];
    foreach ($query->posts as $p) {
        $items[] = ['id' => $p->ID, 'text' => $p->post_title];
    }

    wp_send_json_success([
        'items'      => $items,
        'total'      => (int) $query->found_posts,
        'totalPages' => (int) $query->max_num_pages,
        'page'       => $paged,
    ]);
});

/* =========================================================
 * 3) Lógica: leer lista y setear cookie para Woodmart
 * ======================================================= */

function cpt_set_compare_cookie_from_ids(array $ids) {
    $ids = array_values(array_filter(array_map('absint', $ids)));
    if (empty($ids)) return;

    $json = wp_json_encode($ids);
    // 1 hora de vida (ajusta a gusto)
    setcookie('woodmart_compare_list', $json, time() + 3600, '/');
    $_COOKIE['woodmart_compare_list'] = $json;
}

function cpt_get_compare_ids_by_slug($slug) {
    $post = get_page_by_path(sanitize_title($slug), OBJECT, 'compare_list');
    if (!$post) return [];
    $ids = (array) get_post_meta($post->ID, 'compare_products', true);
    return array_values(array_filter(array_map('absint', $ids)));
}

function cpt_get_compare_ids_by_post($post_id) {
    $ids = (array) get_post_meta(absint($post_id), 'compare_products', true);
    return array_values(array_filter(array_map('absint', $ids)));
}

/* =========================================================
 * 4) Shortcodes
 *    - [comparador tabla="slug"]  ó  [comparador id="123"]
 *    - Alias: [comparador_fijo tabla="slug"]
 * ======================================================= */
function comparador_render_shortcode($atts) {
    $atts = shortcode_atts([
        'tabla' => '', // slug
        'id'    => 0,  // post id del CPT
    ], $atts, 'comparador');

    $ids = [];
    if (!empty($atts['tabla'])) {
        $ids = cpt_get_compare_ids_by_slug($atts['tabla']);
    } elseif (!empty($atts['id'])) {
        $ids = cpt_get_compare_ids_by_post($atts['id']);
    }

    if (empty($ids)) return '<div class="notice notice-warning">No hay productos en esta lista.</div>';

    cpt_set_compare_cookie_from_ids($ids);

    // Renderiza el comparador nativo del theme
    return do_shortcode('[woodmart_compare]');
}
add_shortcode('comparador', 'comparador_render_shortcode');

// Alias de compatibilidad con tu shortcode anterior
add_shortcode('comparador_fijo', function ($atts) {
    return comparador_render_shortcode($atts);
});

/* =========================================================
 * 5) Admin list column (conteo de productos)
 * ======================================================= */
add_filter('manage_compare_list_posts_columns', function ($cols) {
    $cols['compare_count'] = 'Productos';
    return $cols;
});
add_action('manage_compare_list_posts_custom_column', function ($col, $post_id) {
    if ($col === 'compare_count') {
        $ids = (array) get_post_meta($post_id, 'compare_products', true);
        echo '<strong>' . count(array_filter($ids)) . '</strong>';
    }
}, 10, 2);

/* =========================================================
 * 6) Assets del admin (CSS/JS)
 *    Crea los archivos /css/compare-admin.css y /js/compare-admin.js
 * ======================================================= */
