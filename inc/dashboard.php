<?php
// Function that outputs the contents of the dashboard widget
function mind_toggle_function($post, $callback_args)
{


    $options = get_option('mind_toggl_settings');
    $api_key = $options['mind_toggl_api_key'];
    $client_id = $options['mind_toggl_client_id'];
    if (!$api_key) {
        echo '<a href="/wp-admin/options-general.php?page=toggl_settings">Configure Your Toggl Settings</a>';
    } elseif (!$client_id) {
        echo '<a href="/wp-admin/options-general.php?page=toggl_settings">Enter A Client ID</a>';
    } else {


        $toggle = new mindToggl($api_key);


        $total_hours = $toggle->mind_monthly_toggl_hours($client_id);
        $client = $toggle->mind_get_client($client_id);
        $client_projs = $toggle->mind_get_client_projects($client->id);
        $current_time = new DateTime();
        $current_month = $current_time->format('M');
        echo '<div class="mind-client-name">';
        echo '<div class="mind-client-name">';
        echo $client->name;
        echo '</div>';
        echo '<div class="mind-client-hours">';
        echo 'Hours Logged in ' . $current_month . ': ';
        echo $toggle->mind_monthly_toggl_hours($client->id);
        echo '</div>';

        echo '</div>';

        echo '<div class="mind-client-projects">';
        foreach ($client_projs as $client_proj) :
            echo '<span class="mind-project-name">' . $client_proj->name . '</span>';
            echo '<ul>';
            echo '<li>';
            if (isset($client_proj->estimated_hours)) :
                echo '<strong>Estimated Hours: </strong>' . $client_proj->estimated_hours . ' ';
            endif;
            echo '<strong>Actual Hours: </strong>' . $client_proj->actual_hours . '</li>';
            echo '</ul>';
        endforeach;
        echo '</div>';
        echo '<div class="warning">';
        echo 'The estimates above may not reflect actual billable hours and may not be used for such purposes.';
        echo '<br>';
        echo '</div>';

    }
}

// Function used in the action hook
function mind_add_dashboard_widgets()
{
    wp_add_dashboard_widget('dashboard_widget', 'Toggle Time Log', 'mind_toggle_function');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'mind_add_dashboard_widgets');


// Update CSS within in Admin
function mind_admin_style()
{
    wp_enqueue_style('toggle-mind-admin-styles', plugins_url('../css/admin.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'mind_admin_style');