<?php
namespace repository\content_object\rdpublication;
/**
 * $Id: rdpublication_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class RdpublicationContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>