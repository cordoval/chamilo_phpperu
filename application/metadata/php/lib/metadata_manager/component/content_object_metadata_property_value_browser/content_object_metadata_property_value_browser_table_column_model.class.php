<?php

namespace application\metadata;
use repository\DefaultContentObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;

/**
 * Table column model for the metadata_property_value browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectMetadataPropertyValueBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->add_column(new ObjectTableColumn(ContentObjectMetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID));
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
        if (!isset(self :: $modification_column))
        {
                self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>