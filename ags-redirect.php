<?php

/**
 * Plugin Name:       WooCommerce Password Reset Redirect
 * Plugin URI:        https://lizardhost.gr/
 * Description:       redirects the user after password reset to the Defined path & Ads a link to my account menu to the same location.
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Angelos Sykamiotis
 * Author URI:        https://lizardhost.gr/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ags-redirect-plugin
 * Domain Path:       /languages
 */

//define constants of the plugin_name
define('WPBP_PLUGIN_PATH', plugin_dir_url( __FILE__ ));

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require plugin_dir_path( __FILE__ ).'/includes/database.php';
require plugin_dir_path( __FILE__ ).'/includes/settings.php';

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    global $wpdb;
    $table_name = $wpdb->prefix . 'AGs_redirect_settings';
    $query      ="SELECT * FROM $table_name WHERE name='redirect_link'";
    $settings   = $wpdb->get_row( $wpdb->prepare( $query  ), ARRAY_A );
    $redirect_link = $settings['value'];


    function load_admin_styles() {
        wp_enqueue_style( 'admin_css_lineicons','https://cdn.lineicons.com/3.0/lineicons.css' , false, '1.0.0' );
    }

    add_action( 'admin_enqueue_scripts', 'load_admin_styles' );

    function ags_redir_plugin_setup_menu(){
        add_menu_page( 'Ags Redirect Settings', 'Password Reset Redirect Settings ', 'manage_options', 'ags-redirect-plugin', 'ags_redir_init', plugin_dir_url( __FILE__ ).'/assets/icons/ags_redirect_favicon.ico');
    }
    add_action('admin_menu', 'ags_redir_plugin_setup_menu');

    function ags_redir_init(){
        global $settings;
//        echo "<pre>".print_r($settings,true)."</pre>";
        echo '<div class="wrap"><i class="lni lni-link"></i> Redirect The user After Password Reset to the url provided <br>';
        echo 'Example Path "/home" ';
        echo ags_admin_form($settings);
        echo '</div>';

    }




    // ------------------
    // 1. Register new endpoint (URL) for My Account page
    // Note: Re-save Permalinks or it will give 404 error

    function ags_redirect_to_dashboard()
    {
        add_rewrite_endpoint('admin-dashboard', EP_ROOT | EP_PAGES);
    }

    add_action('init', 'ags_redirect_to_dashboard');

    // ------------------
    // 2. Add new query var

    function ags_premium_support_query_vars($vars)
    {
        $vars[] = 'admin-dashboard';
        return $vars;
    }

    add_filter('query_vars', 'ags_premium_support_query_vars', 0);

    // ------------------
    // 3. Insert the new endpoint into the My Account menu


    function ags_add_admin_dashboard_link_my_account($items)
    {
        global $redirect_link;

        $my_items = array(
            '../'.$redirect_link => __('Clinic Dashboard', 'wc-my-account-menu-item'),
        );

        $my_items = array_slice($items, 0, 1, true) + $my_items + array_slice($items, 1, count($items), true);

        return $my_items;
    }

    add_filter('woocommerce_account_menu_items', 'ags_add_admin_dashboard_link_my_account', 99, 1);

    // ------------------
    // 4. Add content to the new tab

    function ags_admin_dashboard_content()
    {
        //    echo '<h3>Premium WooCommerce Support</h3>';
        //    echo do_shortcode( ' /* your shortcode here */ ' );
    }

    add_action('woocommerce_account_admin-dashboard_endpoint', 'ags_admin_dashboard_content');
    // Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

    function woocommerce_new_pass_redirect($user)
    {
        global $redirect_link;
        wp_redirect(home_url($redirect_link));
        exit;
    }

    add_action('woocommerce_customer_reset_password', 'woocommerce_new_pass_redirect');
}



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/ags-redirect-activator.php';
    Ags_redirect_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/ags-redirect-deactivator.php';
    Ags_redirect_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );

register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );
