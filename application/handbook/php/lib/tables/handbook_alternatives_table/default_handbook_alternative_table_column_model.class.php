<?php


namespace application\handbook;
use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;
use repository\content_object\handbook\Handbook;
use application\context_linker\ContextLink;
use repository\ContentObject;





class DefaultHandbookAlternativeTableColumnModel extends ObjectTableColumnModel
{

     const COLUMN_METADATA_PROPERTY_TYPE = 'metadata_property_type';
    const COLUMN_METADATA_PROPERTY_VALUE = 'metadata_property_value';
  
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new StaticTableColumn('', false);
        $columns[] = new ObjectTableColumn(ContentObject::PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(ContentObject::PROPERTY_DESCRIPTION, true);
        $columns[] = new ObjectTableColumn(ContentObject::PROPERTY_TYPE, true);
        $columns[] = new ObjectTableColumn(self:: COLUMN_METADATA_PROPERTY_TYPE, true);
        $columns[] = new ObjectTableColumn(self :: COLUMN_METADATA_PROPERTY_VALUE, true);
//        $columns[] = self :: get_action_column();
        return $columns;
    }

    static function get_action_column()
    {
        if (! isset(self :: $action_column))
        {
            self :: $action_column = new StaticTableColumn(Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES));
        }
        return self :: $action_column;
    }
}
?>