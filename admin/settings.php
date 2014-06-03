<?php

class FreelancerSettingsPage {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_submenu_page(
                'edit.php?post_type=' . FREELANCER_POST_TYPE, 'Settings Admin', 'Real Estate Settings', 'manage_options', 'freelancer-setting-admin', array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('freelancer_option_name');
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Freelancer Settings</h2>           
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('freelancer_option_group');
                do_settings_sections('freelancer-setting-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        register_setting(
                'freelancer_option_group', // Option group
                'freelancer_option_name', // Option name
                array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
                'setting_section_id', // ID
                'Price for user post and vote on site', // Title
                array($this, 'print_section_info'), // Callback
                'freelancer-setting-admin' // Page
        );

        add_settings_field(
                'price_per_post', // ID
                'Price per post', // Title 
                array($this, 'price_per_post_callback'), // Callback
                'freelancer-setting-admin', // Page
                'setting_section_id' // Section           
        );

        add_settings_field(
                'price_per_vote', 'Price per vote', array($this, 'price_per_vote_callback'), 'freelancer-setting-admin', 'setting_section_id'
        );

        add_settings_section(
                'setting_section_id_2', // ID
                'Price of Package', // Title
                array($this, 'print_package_info'), // Callback
                'freelancer-setting-admin' // Page
        );

        add_settings_field(
                'price_of_package_a', 'Price of Package A', array($this, 'price_of_package_a_callback'), 'freelancer-setting-admin', 'setting_section_id_2'
        );

        add_settings_field(
                'price_of_package_b', 'Price of Package B', array($this, 'price_of_package_b_callback'), 'freelancer-setting-admin', 'setting_section_id_2'
        );

        add_settings_field(
                'price_of_package_c', 'Price of Package C', array($this, 'price_of_package_c_callback'), 'freelancer-setting-admin', 'setting_section_id_2'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input) {
        $new_input = array();
        if (isset($input['price_per_post']))
            $new_input['price_per_post'] = absint($input['price_per_post']);

        if (isset($input['price_per_vote']))
            $new_input['price_per_vote'] = sanitize_text_field($input['price_per_vote']);

        if (isset($input['price_of_package_a']))
            $new_input['price_of_package_a'] = sanitize_text_field($input['price_of_package_a']);

        if (isset($input['price_of_package_b']))
            $new_input['price_of_package_b'] = sanitize_text_field($input['price_of_package_b']);

        if (isset($input['price_of_package_c']))
            $new_input['price_of_package_c'] = sanitize_text_field($input['price_of_package_c']);

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print 'This is the admin setting area. We use to setting the price when people post a articel or when people vote for it:';
    }

    public function print_package_info() {
        print 'Enter the price of package for selling on the site:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function price_per_post_callback() {
        printf(
                '<input type="text" id="price_per_post" name="freelancer_option_name[price_per_post]" value="%s" /> xu', isset($this->options['price_per_post']) ? esc_attr($this->options['price_per_post']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function price_per_vote_callback() {
        printf(
                '<input type="text" id="price_per_vote" name="freelancer_option_name[price_per_vote]" value="%s" /> xu', isset($this->options['price_per_vote']) ? esc_attr($this->options['price_per_vote']) : ''
        );
    }

    public function price_of_package_a_callback() {
        printf(
                '<input type="text" id="price_of_package_a" name="freelancer_option_name[price_of_package_a]" value="%s" /> xu/$10.00', isset($this->options['price_of_package_a']) ? esc_attr($this->options['price_of_package_a']) : ''
        );
    }

    public function price_of_package_b_callback() {
        printf(
                '<input type="text" id="price_of_package_b" name="freelancer_option_name[price_of_package_b]" value="%s" /> xu/$20.00', isset($this->options['price_of_package_b']) ? esc_attr($this->options['price_of_package_b']) : ''
        );
    }

    public function price_of_package_c_callback() {
        printf(
                '<input type="text" id="price_of_package_c" name="freelancer_option_name[price_of_package_c]" value="%s" /> xu/$50.00', isset($this->options['price_of_package_c']) ? esc_attr($this->options['price_of_package_c']) : ''
        );
    }

}

if (is_admin())
    $freelancer_settings_page = new FreelancerSettingsPage();