<?php
require_once( ABSPATH . 'wp-admin/includes/template.php' );

function bids_add_theme_style() {
    wp_enqueue_style('bids-style-css', FREELANCER_URI . 'templates/css/bids-style.css', '', '1.0.1');
    wp_enqueue_script('do-freelancer-jobs', FREELANCER_URI . 'templates/js/freelancer.js', array('jquery'), null, true);
    wp_enqueue_script('jquery-validate', FREELANCER_URI . 'templates/js/jquery.validate.min.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'bids_add_theme_style');

function bids_area_checklist() {
    ?>
    <ul id='bid_area'>
        <?php
        $args = array(
            'taxonomy' => 'bid_area',
        );
        wp_terms_checklist('', $args);
        ?>
    </ul>
    <?php
}

function bids_city_selectbox() {
    $args = array(
        'show_option_all' => 'Please choose a city',
        'taxonomy' => 'bid_area',
        'hide_empty' => 0,
        'depth' => 1,
        'hierarchical' => 1,
        'hide_if_empty' => 0,
        'id' => 'bids_area_city',
        'name' => 'bids_area_city',
        'class' => 'required'
    );
    wp_dropdown_categories($args);
}

function bids_show_bid_listings() {
    get_template_part('bids/bids');
}

add_shortcode('bids_show_bid_listings', 'bids_show_bid_listings');

function bids_show_min_price() {
    global $post;
    echo get_post_meta($post->ID, 'bids_min_price', true);
}

function bids_show_max_price() {
    global $post;
    echo get_post_meta($post->ID, 'bids_max_price', true);
}

function bids_get_vote_form() {
    ob_start();
    get_template_part('bids/vote', 'form');
    $content = ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $content;
}

function bids_add_vote_form($content) {
    if (is_singular(FREELANCER_POST_TYPE)) {
        $content .= bids_get_vote_form();
    }
    return $content;
}

add_filter('the_content', 'bids_add_vote_form');
