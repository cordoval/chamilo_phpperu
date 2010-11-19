<?php
namespace repository\content_object\blog;

use repository\ContentObjectInstaller;

/**
 * $Id: blog_item_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class BlogContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>