<?php
require_once( ABSPATH . 'wp-admin/includes/template.php' );

function bids_add_theme_style() {
    wp_enqueue_style('bids-style-css', FREELANCER_URI . 'templates/css/bids-style.css', '', '1.0.1');
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
        'hide_if_empty' => 0
    );
    wp_dropdown_categories($args);
}

