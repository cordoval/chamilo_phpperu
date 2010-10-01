<?php
/**
 * $Id: photo_gallery_publication_browser_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.photo_gallery.photo_gallery_manager.component.photo_gallery_publication_browser
 */

require_once dirname(__FILE__) . '/../../../tables/photo_gallery_publication_table/default_photo_gallery_publication_table_column_model.class.php';

/**
 * Table column model for the photo_gallery_publication browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class PhotoGalleryPublicationBrowserTableColumnModel extends DefaultPhotoGalleryPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function PhotoGalleryPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>