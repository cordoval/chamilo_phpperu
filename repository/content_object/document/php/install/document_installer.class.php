<?php
namespace repository\content_object\document;

use repository\ContentObjectInstaller;

/**
 * $Id: document_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class DocumentContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>