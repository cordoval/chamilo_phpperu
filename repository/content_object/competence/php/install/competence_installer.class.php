<?php
namespace repository\content_object\competence;

use repository\ContentObjectInstaller;

/**
 * $Id: competence_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class CompetenceContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>