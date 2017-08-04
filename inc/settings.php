<?php
add_action( 'admin_menu', 'mind_toggl_add_admin_menu' );
add_action( 'admin_init', 'mind_toggl_settings_init' );


function mind_toggl_add_admin_menu(  ) {

    add_options_page( 'Toggl Settings', 'Toggle Settings', 'manage_options', 'toggl_settings', 'mind_toggl_options_page' );

}


function mind_toggl_settings_init(  ) {

    register_setting( 'mind_toggl_settings', 'mind_toggl_settings' );

    add_settings_section(
        'mind_toggl_mind_toggl_settings_section',
        __( '', 'wordpress' ),
        'mind_toggl_settings_section_callback',
        'mind_toggl_settings'
    );

    add_settings_field(
        'mind_toggl_api_key',
        __( 'Toggl API Key', 'wordpress' ),
        'mind_toggl_api_key_render',
        'mind_toggl_settings',
        'mind_toggl_mind_toggl_settings_section'
    );

    add_settings_field(
        'mind_toggl_client_id',
        __( 'Client Name', 'wordpress' ),
        'mind_toggl_client_id_render',
        'mind_toggl_settings',
        'mind_toggl_mind_toggl_settings_section'
    );



}


function mind_toggl_api_key_render(  ) {

    $options = get_option( 'mind_toggl_settings' );
    ?>
    <input type='text' name='mind_toggl_settings[mind_toggl_api_key]' value='<?php echo $options['mind_toggl_api_key']; ?>'>
    <?php

}




function mind_toggl_client_id_render()
{

    $options = get_option('mind_toggl_settings');
    $toggle = new mindToggl($options['mind_toggl_api_key']);
    $clients = $toggle->mind_get_clients();
    if (!$options['mind_toggl_api_key']) :
        echo 'Please enter your API Key to see a list of clients';
    else :
        ?>
        <select name='mind_toggl_settings[mind_toggl_client_id]'>
            <?php
            foreach ($clients as $client) :

                $selected = $options['mind_toggl_client_id'];
                if($selected == '') :
                    $selected = 1;
                endif;
                ?>
                <option value='<?php echo $client->id; ?>' <?php selected($selected, $client->id); ?>>
                    <?php echo $client->name; ?>
                </option>
                <?php
            endforeach;
            ?>
        </select>
        <?php
    endif;

}



function mind_toggl_settings_section_callback(  ) {
}


function mind_toggl_options_page(  ) {

    ?>
    <form action='options.php' method='post'>

        <h2>Toggl Settings</h2>

        <?php
        settings_fields( 'mind_toggl_settings' );
        do_settings_sections( 'mind_toggl_settings' );
        submit_button();
        ?>

    </form>
    <?php

}

?>