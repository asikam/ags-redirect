<?php

function ags_admin_form($data): string
{
    $form_string = '<form action="'.esc_url( admin_url( 'admin-post.php' ) ).'" method="POST">';
    $form_string .= wp_nonce_field( 'update-redirect-path_'.$data['id'] );
    $form_string .='<input type="hidden" name="action" value="ags_redirect_settings_post">
            <table>
                <tr>
                    <th><label for="redirect_link">Redirect Location:</label></th>
                    <td><input type="text" id="redirect_link" name="redirect_link" value="'. $data['value'] .'"></td>
                </tr>
            </table>
           
            <p class="submit">
                <input class="button-primary" type="submit" value="Αποθήκευση">
            </p>
        </form>';

   return $form_string;

}

add_action('admin_post_ags_redirect_settings_post', 'ags_redirect_settings_post');

function ags_redirect_settings_post(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'AGs_redirect_settings';
    $query      ="SELECT * FROM $table_name WHERE name='redirect_link'";
    $settings   = $wpdb->get_row( $wpdb->prepare( $query  ), ARRAY_A );
    $url = sanitize_text_field($_POST['redirect_link']);

    check_admin_referer( 'update-redirect-path_'.$settings['id'] );

    $result = $wpdb->update(
        $table_name,
        array(
            'value' => $url,
            'time' => current_time( 'mysql' )
        ),
        array(
            'name'=>'redirect_link'
        )
    );

    if ($result === FALSE || $result < 1) {
        $wpdb->insert($table_name,
            array(
                'name'=>'redirect_link',
                'value' => $url,
                'time' => current_time( 'mysql' )
            ));
    }

    if ( wp_redirect( $_SERVER["HTTP_REFERER"]) ) {
        exit;
    }
}
