<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.1.6
 * @package    Ags_Redirect
 * @subpackage Ags_Redirect_Plugin/includes
 * @author     Angelos Sykamiotis <gasikamio@gmail.com>
 */
class Ags_redirect_Plugin_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.1.5
     */
    public static function activate() {
        ags_redir_install();

        ags_redir_install_data();
    }

}