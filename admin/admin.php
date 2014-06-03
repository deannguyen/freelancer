<?php
add_action('init', 'portfolio_register');
add_action('admin_enqueue_scripts', 'enqueue_date_picker');

function enqueue_date_picker() {
    wp_enqueue_script(
            'field-date-js', 'Field_Date.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), time(), true
    );

    wp_enqueue_style('jquery-ui-datepicker');
}

function portfolio_register() {

    $labels = array(
        'name' => _x('Real Estate Bids', 'post type general name'),
        'singular_name' => _x('Bid Item', 'post type singular name'),
        'add_new' => _x('Add New', 'portfolio item'),
        'add_new_item' => __('Add New Bid Item'),
        'edit_item' => __('Edit Bid Item'),
        'new_item' => __('New Bid Item'),
        'view_item' => __('View Bid Item'),
        'search_items' => __('Search Bids'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => plugins_url('../images/bid-house-icon-small.png', __FILE__),
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type(FREELANCER_POST_TYPE, $args);

    register_taxonomy("bid_category", array(FREELANCER_POST_TYPE), array("hierarchical" => true, "label" => "Bid category", "singular_label" => "Bid category", "rewrite" => true));

    register_taxonomy("bid_area", array(FREELANCER_POST_TYPE), array("hierarchical" => true, "label" => "Bid area", "singular_label" => "Bid area", "rewrite" => true));

    $labels = array(
        'name' => _x('Real Estate Votes', 'post type general name'),
        'singular_name' => _x('Vote Item', 'post type singular name'),
        'add_new' => _x('Add New', 'portfolio item'),
        'add_new_item' => __('Add New Vote Item'),
        'edit_item' => __('Edit Vote Item'),
        'new_item' => __('New Vote Item'),
        'view_item' => __('View Vote Item'),
        'search_items' => __('Search Votes'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => plugins_url('../images/bid-house-icon-small.png', __FILE__),
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_menu' => 'edit.php?post_type=' . FREELANCER_POST_TYPE,
    );
    register_post_type(FREELANCER_VOTE_POST_TYPE, $args);
}

add_action("admin_init", "bids_admin_init");

function bids_admin_init() {
    add_meta_box("bid_status", "Bid information", "bid_admin_layout_bid_status", "bids", "normal", "low");
}

/**
 * Show the status of a bid for admin can edit it in admin dashboard
 */
function bid_admin_layout_bid_status() {
    global $post;
    $custom = get_post_custom($post->ID);
    $bid_status = $custom["bid_status"][0];
    $bids_min_price = $custom["bids_min_price"][0];
    $bids_max_price = $custom["bids_max_price"][0];
    $bids_start_date = $custom["bids_start_date"][0];
    $bids_expired_date = $custom["bids_expired_date"][0];

    $arg_bid_status = get_option('bids_post_status');
    ?>
    <label style="width: 100px;display: inline-block;">Price range:</label>
    <input name="bids_min_price" placeholder="min" value="<?php echo $bids_min_price; ?>"> to 
    <input name="bids_max_price" placeholder="max" value="<?php echo $bids_max_price; ?>"><br /> 
    <label style="width: 100px;display: inline-block;">Time looking for:</label>
    <input type="date" name="bids_start_date" placeholder="Start date" value="<?php echo $bids_start_date; ?>"> to 
    <input type="date" name="bids_expired_date" placeholder="Expired date" value="<?php echo $bids_expired_date; ?>"><br />     
    <label style="width: 100px;display: inline-block;">Bid status: </label>
    <select name="bid_status">
        <?php foreach ($arg_bid_status as $key => $value): ?>
            <option value="<?php echo $key; ?>" <?php selected($key, $bid_status); ?>><?php echo $value; ?></option>
        <?php endforeach; ?>
    </select>    
    <?php
}

function bid_update_meta_values_in_save_post($post_id) {
    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */

    // If this isn't a 'book' post, don't update it.
    if (FREELANCER_POST_TYPE != $_POST['post_type']) {
        return;
    }

    // - Update the post's metadata.
    if (isset($_REQUEST['bids_min_price'])) {
        update_post_meta($post_id, 'bids_min_price', sanitize_text_field($_REQUEST['bids_min_price']));
    }

    if (isset($_REQUEST['bids_max_price'])) {
        update_post_meta($post_id, 'bids_max_price', sanitize_text_field($_REQUEST['bids_max_price']));
    }

    if (isset($_REQUEST['bids_start_date'])) {
        update_post_meta($post_id, 'bids_start_date', sanitize_text_field($_REQUEST['bids_start_date']));
    }

    if (isset($_REQUEST['bids_expired_date'])) {
        update_post_meta($post_id, 'bids_expired_date', sanitize_text_field($_REQUEST['bids_expired_date']));
    }
    if (isset($_REQUEST['bid_status'])) {
        update_post_meta($post_id, 'bid_status', sanitize_text_field($_REQUEST['bid_status']));
    }
}

add_action('save_post', 'bid_update_meta_values_in_save_post');


/**
 * Custom the colum on the bids admin page
 */
add_action("manage_posts_custom_column", "bids_custom_columns");
add_filter("manage_edit-bids_columns", "bids_edit_columns");

function bids_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Bid Title",
        "bid_category" => "Categories",
        "bid_area" => "Areas",
        "bid_status" => "Status",
        "date" => "Date",
    );

    return $columns;
}

function bids_custom_columns($column) {
    global $post;
    switch ($column) {
        case "bid_category":
            $bid_categories = get_the_terms($post->ID, 'bid_category');
            if ($bid_categories && !is_wp_error($bid_categories)) :
                foreach ($bid_categories as $category) {
                    echo '<li>' . $category->name . '</li>';
                }
            endif;
            break;
        case "bid_area":
            $bid_areas = get_the_terms($post->ID, 'bid_area');
            if ($bid_areas && !is_wp_error($bid_areas)) :
                foreach ($bid_areas as $area) {
                    echo '<li>' . $area->name . '</li>';
                }
            endif;
            break;
        case "bid_status":
            $custom = get_post_custom();
            echo bids_get_bid_status($custom["bid_status"][0]);
            break;
    }
}

add_action('restrict_manage_posts', 'bids_admin_posts_filter_restrict_manage_posts');

/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 * 
 * @author Ohad Raz
 * 
 * @return void
 */
function bids_admin_posts_filter_restrict_manage_posts() {
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if (FREELANCER_POST_TYPE == $type) {
        //change this to the list of values you want to show
        $taxonomies = get_terms('bid_category', array('hide_empty' => false));
        ?>
        <select name="ADMIN_FILTER_FIELD_VALUE">
            <option value=""><?php _e('Filter By Category', 'wose45436'); ?></option>
            <?php
            $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE']) ? $_GET['ADMIN_FILTER_FIELD_VALUE'] : '';
            foreach ($taxonomies as $taxonomy) {
                printf(
                        '<option value="%s"%s>%s</option>', $taxonomy->slug, selected($taxonomy->slug, $current_v, false), $taxonomy->name
                );
            }
            ?>
        </select>
        <?php
    }
}

add_filter('parse_query', 'bids_posts_filter');

/**
 * if submitted filter by post meta
 * 
 * make sure to change META_KEY to the actual meta key
 * and POST_TYPE to the name of your custom post type
 * @author Ohad Raz
 * @param  (wp_query object) $query
 * 
 * @return Void
 */
function bids_posts_filter($query) {
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if (FREELANCER_POST_TYPE == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['taxonomy'] = 'bid_category';
        $query->query_vars['term'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}
?>