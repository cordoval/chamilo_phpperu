<?php
require_once dirname(__FILE__) . '/../../photo_gallery.class.php';

class DefaultPhotoGalleryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultPhotoGalleryTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return PhotoGalleryTableColumn[]
     */
    private static function get_default_columns()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $content_object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
        return $columns;
    }
}
?>