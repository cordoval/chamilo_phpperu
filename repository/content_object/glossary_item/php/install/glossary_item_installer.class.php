<?php
namespace repository\content_object\glossary_item;
/**
 * $Id: glossary_item_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class GlossaryItemContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>