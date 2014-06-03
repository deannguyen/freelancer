<?php

/*
  Plugin Name: Freelancer Plugin
  Plugin URI: http://webtretho.com/
  Description: This plugin's build for Freelancer box
  Version: 1.0.0
  Author: Nguyen Dat
  Author URI: http://webtretho.com/
 */

class Freelancer {

    public function __construct() {

        global $freelancer;

        $freelancer = new stdClass;

        /* Set the constants needed by the plugin. */
        add_action('plugins_loaded', array($this, 'constants'), 1);

        /* Internationalize the text strings used. */
        add_action('plugins_loaded', array($this, 'i18n'), 2);

        /* Load the plugin files. */
        add_action('plugins_loaded', array($this, 'includes'), 3);

        /* Register activation hook. */
        //register_activation_hook( __FILE__, array( $this, 'activation' ) );

        /* Register deactivation hook. */
        //register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
                
    }

    public function constants() {

        /* Set the version number of the plugin. */
        define('FREELANCER_VERSION', '1.0.1');

        /* Set the database version number of the plugin. */
        define('FREELANCER_DB_VERSION', 1);

        /* Set constant path to the plugin directory. */
        define('FREELANCER_DIR', trailingslashit(plugin_dir_path(__FILE__)));

        /* Set constant path to the plugin URI. */
        define('FREELANCER_URI', trailingslashit(plugin_dir_url(__FILE__)));

        /* Set constant basename for the plugin. */
        define('FREELANCER_BASE', basename(plugin_dir_path(__FILE__)));
        
        define('FREELANCER_POST_TYPE', 'bids');
        define('FREELANCER_VOTE_POST_TYPE', 'votes');

        /* The status default option for a bid on site
         * load it for the default of plugins with empty value
         */
        if (!get_option('bids_post_status')) {
            $args_status = array(
                '1' => 'Chờ thanh toán',
                '2' => 'Đang hoạt động',
                '3' => 'Đã đóng',
                '4' => 'Hũy do thanh toán',
                '5' => 'Hũy do trái quy định',
            );
            add_option('bids_post_status', $args_status);
        }
    }

    public function i18n() {
        load_plugin_textdomain('freelancer', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function includes() {

        /* Include frontend files. */
        //require_once( FREELANCER_DIR . 'includes/core.php' );
        //require_once( FREELANCER_DIR . 'includes/settings.php' );
        //require_once( FREELANCER_DIR . 'includes/post-types.php' );
        //require_once( FREELANCER_DIR . 'includes/taxonomies.php' );
        //require_once( FREELANCER_DIR . 'includes/user-meta.php' );
        //require_once( FREELANCER_DIR . 'includes/post-meta.php' );
        //require_once( FREELANCER_DIR . 'includes/template.php' );
        require_once( FREELANCER_DIR . 'includes/misc.php' );

        /* Include admin files. */
        if (is_admin()) {
            // include admin files
            require_once( FREELANCER_DIR . 'admin/admin.php' );
            require_once( FREELANCER_DIR . 'admin/settings.php' );
            //require_once( FREELANCER_DIR . 'admin/users.php' );
        }
    }

}

/* Init main class. */
new Freelancer();
