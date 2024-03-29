<?php
namespace application\photo_gallery;

use common\libraries\GalleryObjectTablePropertyModel;
use common\libraries\GalleryObjectTableProperty;

use repository\ContentObject;
use repository\RepositoryDataManager;

class DefaultPhotoGalleryGalleryTablePropertyModel extends GalleryObjectTablePropertyModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_properties(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_properties()
    {
        $content_object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());
        
        $properties = array();
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_TITLE, $content_object_alias);
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_DESCRIPTION, $content_object_alias);
        return $properties;
    }
}
?>