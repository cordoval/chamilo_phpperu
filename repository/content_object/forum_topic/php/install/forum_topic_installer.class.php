<?php
namespace repository\content_object\forum_topic;
/**
 * $Id: forum_topic_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class ForumTopicContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>