<?php
namespace application\photo_gallery;

use repository\ContentObject;
use repository\RepositoryDataManager;

use common\libraries\ObjectTableColumn;
use common\libraries\ObjectTableColumnModel;

class DefaultPhotoGalleryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
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