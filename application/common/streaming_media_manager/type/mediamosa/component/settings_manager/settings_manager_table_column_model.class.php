<?php
/**
 * $Id: repository_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class SettingsManagerTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SettingsManagerTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
        $this->set_default_order_column(0);
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

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(StreamingMediaServerObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(StreamingMediaServerObject :: PROPERTY_URL);
        $columns[] = new ObjectTableColumn(StreamingMediaServerObject:: PROPERTY_LOGIN);
        $columns[] = new ObjectTableColumn(StreamingMediaServerObject:: PROPERTY_PASSWORD);
        $columns[] = new ObjectTableColumn(StreamingMediaServerObject:: PROPERTY_IS_UPLOAD_POSSIBLE);
        /*$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
		$columns[] = new StaticTableColumn(Translation :: get('Versions'));*/
        return $columns;
    }

    function get_display_order_column_property()
    {
        return ContentObject :: PROPERTY_DISPLAY_ORDER_INDEX;
    }
}
?>