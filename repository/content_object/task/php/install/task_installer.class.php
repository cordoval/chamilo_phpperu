<?php
namespace repository\content_object\task;
/**
 * $Id: task_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class TaskContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>