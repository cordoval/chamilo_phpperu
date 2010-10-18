<?php
/**
 * $Id: settings_webconferencing_connector.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.settings
 */
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_library_path() . 'filesystem/path.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class SettingsWebconferencingConnector
{

    function get_network_options()
    {
        $network_options = array('L' => Translation :: get('Low'), 'M' => Translation :: get('Medium'), 'H' => Translation :: get('High'));
        return $network_options;
    }

    function get_mikes_options()
    {
        $mikes_options = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);
        return $mikes_options;
    }

    function get_audio_video_options()
    {
        $audio_video_options = array('A' => Translation :: get('Audio'), 'X' => Translation :: get('VideoOnly'), 'V' => Translation :: get('AudioVideoAllowed'), 'D' => Translation :: get('AudioVideoDisabled'));
        return $audio_video_options;
    }
}
?>