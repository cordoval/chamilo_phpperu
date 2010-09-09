<?php
/**
 * $Id: photo_gallery_installer.class.php
 * @package application.lib.photo_gallery.install
 */

require_once dirname(__FILE__) . '/../photo_gallery_data_manager.class.php';

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