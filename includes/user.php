<?php

function bids_register_form() {
    global $bids_error_register;
    if (isset($_POST['register_button'])) {
        if (!empty($_POST['fullname']) && !empty($_POST['phone']) && !empty($_POST['username']) && !empty($_POST['phone']) && is_email($_POST['email']) && bids_check_passwords($_POST['password'], $_POST['re_password'])) {
            $args = array(
                'fullname' => $_POST['fullname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            );
            bids_add_new_member($args);
            get_template_part('bids/register', 'success');
        } else {
            $bids_error_register = 'Your information is not accepted in Vuonco Network. Please check again!';
            get_template_part('bids/register', 'form');
        }
    } else {
        get_template_part('bids/register', 'form');
    }
}

add_shortcode('bids_register_form', 'bids_register_form');

add_shortcode('bids_login_form', 'wp_login_form');

add_action('wp_logout', create_function('', 'wp_redirect(home_url());exit();'));

/**
 * Checking the password and repassword are match or not.
 * 
 * @param string $pass
 * @param string $repass
 * 
 * @author Nguyen Dat
 * @since 1.0.1 
 * @return int Result of checking 1: good, 2: password too short, 3: password and re-password not match
 */
function bids_check_passwords($pass, $repass) {
    if (strlen($pass) < 6) {
        return 2;
    }
    if ($pass != $repass) {
        return 3;
    }
    return 1;
}

/**
 * Add a user to database if username and email not exist in database
 * 
 * @param array $args parameter information for new user 'fullname', 'email', 'username', 'phone', 'password'
 * 
 * @author Nguyen Dat
 * @since 1.0.1
 * @return boolean Result add new user
 */
function bids_add_new_member($args) {
    $defaults = array(
        'fullname' => '',
        'email' => '',
        'username' => '',
        'phone' => '',
        'password' => '',
    );
    $r = wp_parse_args($args, $defaults);
    $user_id = username_exists($r['username']);
    if (!$user_id && email_exists($r['username']) == false) {
        $user_id = wp_create_user($r['username'], $r['password'], $r['email']);
        wp_update_user(array('ID' => $user_id, 'display_name' => $r['fullname']));
        add_user_meta($user_id, 'phone', $r['phone']);
        return TRUE;
    }
    return FALSE;
}

function bids_add_new_bid_form() {
    global $bids_error_add_bid;
    if (is_user_logged_in()) {
        if (isset($_POST['bids_add_a_bid_submit'])) {
            $args = array(
                'bids_category' => $_POST['bids_category'],
                'bids_start_date' => $_POST['bids_start_date'],
                'bids_expired_date' => $_POST['bids_expired_date'],
                'bid_subject' => $_POST['bid_subject'],
                'bid_content' => $_POST['bid_content'],
            );
            if (!empty($_POST['bids_category']) && !empty($_POST['bid_subject']) && !empty($_POST['bid_content'])) {
                //add new bid to database
                bids_add_new_bid($args);
                get_template_part('bids/new_bid', 'success');
            } else {
                $bids_error_add_bid = 1;
                get_template_part('bids/new_bid', 'form');
            }
        } else {
            get_template_part('bids/new_bid', 'form');
        }
    } else {
        get_template_part('bids/login', 'area');
    }
}

add_shortcode('bids_add_new_bid_form', 'bids_add_new_bid_form');

function bids_add_new_bid($args) {
    $defaults = array(
        'bids_category' => '',
        'bids_start_date' => '',
        'bids_expired_date' => '',
        'bid_subject' => '',
        'bid_content' => '',
    );
    $r = wp_parse_args($args, $defaults);
    if (empty($r['bid_subject']) || empty($r['bid_content'])) {
        return false;
    }
    $post = array(
        'post_content' => $r['bid_content'],
        'post_title' => sanitize_text_field($r['bid_subject']),
        'post_status' => 'publish',
        'post_type' => FREELANCER_POST_TYPE,
    );
    $post_id = wp_insert_post($post);
    if ($post_id) {
        wp_set_post_terms($post_id, $r['bids_category'], 'bid_category');
        update_post_meta($post_id, 'bids_start_date', sanitize_text_field($r['bids_start_date']));
        update_post_meta($post_id, 'bids_expired_date', sanitize_text_field($r['bids_expired_date']));
        update_post_meta($post_id, 'bid_status', '1');
        return TRUE;
    }
    return false;
}
