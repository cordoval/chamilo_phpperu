<?php
namespace repository\content_object\rss_feed;
/**
 * $Id: rss_feed_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class RssFeedContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>