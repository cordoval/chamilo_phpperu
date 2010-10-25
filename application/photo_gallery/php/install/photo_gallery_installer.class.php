<?php
namespace application\photo_gallery;
use common\libraries\Installer;
use application\photo_gallery\PhotoGalleryDataManager;
/**
 * $Id: photo_gallery_installer.class.php
 * @package application.lib.photo_gallery.install
 */
class PhotoGalleryInstaller extends Installer
{
    /**
     * Constructor
     */
    function PhotoGalleryInstaller($values)
    {
        parent :: __construct($values, PhotoGalleryDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>