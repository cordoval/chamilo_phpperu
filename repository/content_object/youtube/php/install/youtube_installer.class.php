<?php
namespace repository\content_object\youtube;

use repository\ContentObjectInstaller;

/**
 * $Id: youtube_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class YoutubeContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>