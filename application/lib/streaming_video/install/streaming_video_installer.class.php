<?php

/**
 * 
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../streaming_video_data_manager.class.php';

//TODO : creates streaming_video_ftp_accounts view in db if not possible with XML
class StreamingVideoInstaller extends Installer
{
    /**
     * Constructor
     */
    function StreamingVideoInstaller($values)
    {
        parent :: __construct($values, StreamingVideoDataManager :: get_instance());

    }

    function install_extra()
    {
         //create default profiles
        //high
        $profile = new TranscodingProfile();
        $profile->set_position(1);
        $profile->set_name('high');
        $profile->set_audio_quality(2);
        $profile->set_video_quality(4);
        $profile->set_width(640);
        $profile->set_height(360);
        $profile->set_channels(2);

        $profile->create();

        //low
        $profile = new TranscodingProfile();
        $profile->set_position(2);
        $profile->set_name('low');
        $profile->set_audio_quality(0);
        $profile->set_video_quality(2);
        $profile->set_width(320);
        $profile->set_height(180);
        $profile->set_channels(2);

        $profile->create();

        return true;

    }

     /**
     * Runs the install-script.
     */
    function get_path()
    {
       return dirname(__FILE__);
    }

}
?>
