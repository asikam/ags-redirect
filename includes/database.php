<?php

function ags_redir_install() {
    global $wpdb;
    global $ags1_db_version;

    $table_name = $wpdb->prefix . 'AGs_redirect_settings';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		value text NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $sql );

    add_option( 'ags1_db_version', $ags1_db_version );
}


function ags_redir_install_data() {
    global $wpdb;
    $name = 'redirect_link';
    $value = '';
    $table_name = $wpdb->prefix . 'AGs_redirect_settings';
    $wpdb->insert(
        $table_name,
        array(
            'time' => current_time( 'mysql' ),
            'name' => $name,
            'value' => $value,
        )
    );
}


function ags_redir_uninstall() {
    global $wpdb;
    global $ws1_db_version;

    $table_name = $wpdb->prefix . 'AGs_redirect_settings';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "DROP TABLE IF EXISTS $table_name;";

    $wpdb->query( $sql);

}
