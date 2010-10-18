<?php
namespace repository\content_object\glossary;

use repository\ContentObjectInstaller;
/**
 * @package repository.install
 */
class GlossaryContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>