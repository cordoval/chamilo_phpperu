<?php
namespace application\weblcms;

use repository\ContentObject;
use common\libraries\GalleryObjectTablePropertyModel;
use common\libraries\GalleryObjectTableProperty;

/**
 * $Id: object_publication_gallery_table_property_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.object_publication_table
 */
/**
 * This class represents a column model for a publication candidate table
 */
class ObjectPublicationGalleryTablePropertyModel extends GalleryObjectTablePropertyModel
{

    /**
     * Constructor.
     */
    function __construct($properties)
    {
        parent :: __construct(self :: get_properties(), 0, SORT_ASC);
    }

    /**
     * Gets the columns of this table.
     * @return array An array of all columns in this table.
     * @see ContentObjectTableColumn
     */
    function get_properties()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        $properties = array();
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_TITLE, $wdm->get_alias(ContentObject :: get_table_name()));
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_DESCRIPTION, $wdm->get_alias(ContentObject :: get_table_name()));
        return $properties;
    }
}
?>