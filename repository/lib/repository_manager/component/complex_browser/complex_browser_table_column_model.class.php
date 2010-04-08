<?php
/**
 * $Id: complex_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.complex_browser
 */
/**
 * Table column model for the repository browser table
 */
class ComplexBrowserTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function ComplexBrowserTableColumnModel($show_subitems_column, $additional_columns = array())
    {
        parent :: __construct(self :: get_default_columns($show_subitems_column, $additional_columns), 1);
        $this->set_default_order_column(0);
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

    private static function get_default_columns($show_subitems_column = true, $additional_columns = array())
    {
        $columns = array();
        $columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TYPE)));
        $columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)));
        $columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_DESCRIPTION)));
        //$columns[] = new ObjectTableColumn(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER);
        

        if ($show_subitems_column)
        {
            $columns[] = new StaticTableColumn(Translation :: get('Subitems'));
        }
        
        foreach ($additional_columns as $additional_column)
        {
            $columns[] = $additional_column;
        }
        
        //$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
        $columns[] = self :: get_modification_column();
        return $columns;
    }
}
?>