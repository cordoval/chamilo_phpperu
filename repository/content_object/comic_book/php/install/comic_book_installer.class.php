<?php
namespace repository\content_object\comic_book;
/**
 * $Id: comic_book_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class ComicBookContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>