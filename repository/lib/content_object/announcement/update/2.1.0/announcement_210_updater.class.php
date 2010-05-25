<?php
/**
 * $Id: announcement_210_updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class Announcement210ContentObjectUpdater extends ContentObjectUpdater
{
    function get_install_path()
    {
        return dirname(__FILE__) . '/../../install/';
    }
    
    function get_path()
    {
    	return dirname(__FILE__) . '/';
    }
}
?>