<?php
/**
 * $Id: learning_path_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class LearningPathInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>