<?php
/**
 * $Id: photo_gallery_data_manager.class.php
 * @package application.lib.photo_gallery
 */

class PhotoGalleryDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function PhotoGalleryDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PhotoGalleryDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_photo_gallery_data_manager.class.php';
            $class = $type . 'PhotoGalleryDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>