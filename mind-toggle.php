<?php

/*
 * Plugin Name: Mindshare Toggle Reports
 * Description: A Wordpress Plugin for displaying client information from Toggle on the dashboard.
 * Version: 1.2.0
 * Author: Mindshare Labs, Inc
 * Author URI: https://mind.sh/are
 * License: MIT License
 * Text Domain: WordPress
*/



require dirname(__FILE__) . '/vendor/autoload.php';

require_once dirname(__FILE__) . '/inc/dashboard.php';
require_once dirname(__FILE__) . '/inc/settings.php';
require_once dirname(__FILE__) .  '/inc/updater.php';

if ( is_admin() ) {
    new BFIGitHubPluginUpdater( __FILE__, 'thejameswilliam', 'toggle-dashboard' );
}

class mindToggl
{

    private $key = '';

    public function __construct($key)
    {
        $this->key = $key;
    }

    private function sec2hm($sec)
    {

        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $sec = $sec / 1000;
        $hours = intval(intval($sec) / 3600);

        $hm = $hours . ":";


        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60);

        // add minutes to $hms (with a leading 0 if needed)
        $hm .= str_pad($minutes, 2, "0", STR_PAD_LEFT);


        // done!
        return $hm;

    }


    public function mind_monthly_toggl_hours($client_ID)
    {
        $toggl = new MorningTrain\TogglApi\TogglReportsApi($this->key);


        $today = new DateTime();
        $today_format = $today->format('Y-m-d');
        $first_day_of_month = $today->modify('first day of this month');
        $date_format = $first_day_of_month->format('Y-m-d');
        $query = 'client_ids=' . $client_ID . '&until=' . $today_format . '&since=' . $date_format;
        $logged_times = $toggl->getMonthlyTime($query);


        $seconds_logged = array();
        foreach ($logged_times as $logged_time) :
            $seconds_logged[] = $logged_time->dur;

        endforeach;
        $total_seconds = array_sum($seconds_logged);

        return $this->sec2hm($total_seconds);

    }


    public function mind_get_client_projects($client_ID)
    {

        $toggl = new MorningTrain\TogglApi\TogglApi($this->key);
        $client_projects = $toggl->getActiveClientProjects($client_ID);
        return $client_projects;


    }

    public function mind_get_workspaces()
    {
        $toggl = new MorningTrain\TogglApi\TogglApi($this->key);
        $client_time = $toggl->getWorkspaces();
        return $client_time;
    }

    public function mind_get_clients()
    {

        $toggl = new MorningTrain\TogglApi\TogglApi($this->key);


        $clients = $toggl->getClients();
        return $clients;
    }

    public function mind_get_client($client_id)
    {

        $toggl = new MorningTrain\TogglApi\TogglApi($this->key);


        $client = $toggl->getClientById($client_id);
        return $client;
    }

}
