<?php
namespace repository\content_object\personal_message;
/**
 * $Id: personal_message_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class PersonalMessageContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>