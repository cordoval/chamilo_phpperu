<?php
namespace repository\content_object\hotpotatoes;

use repository\ContentObjectInstaller;

/**
 * $Id: hotpotatoes_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class HotpotatoesContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>