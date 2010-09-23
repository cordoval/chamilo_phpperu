<?php

require_once dirname(__FILE__) . '/../../tables/photo_gallery_table/default_photo_gallery_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class PhotoGalleryBrowserTableColumnModel extends DefaultPhotoGalleryTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function PhotoGalleryBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return PhotoGalleryTableColumn
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