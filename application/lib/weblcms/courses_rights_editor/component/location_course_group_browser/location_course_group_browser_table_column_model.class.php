<?php
/**
 * $Id: location_course_group_browser_table_column_model.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_course_group_bowser
 */
//require_once Path :: get_course_group_path() . 'lib/course_group_table/default_course_group_table_column_model.class.php';
//require_once dirname(__FILE__) . '/../../../tool/course_group/component/course_group_table/default_course_group_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class LocationCourseGroupBrowserTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;
    private static $rights_columns;
    private $browser;

    /**
     * Constructor
     */
    function LocationCourseGroupBrowserTableColumnModel($browser)
    {
        $this->browser = $browser;
        parent :: __construct($this->get_columns());
        $this->add_rights_columns();
        $this->set_default_order_column(1);
    }
    
    function get_columns()
    {
    	$columns = array();
        $columns[] = new ObjectTableColumn(CourseGroup :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(CourseGroup :: PROPERTY_DESCRIPTION, true);
        $columns[] = new StaticTableColumn(Translation :: get('Users'));
        $columns[] = new StaticTableColumn(Translation :: get('Subgroups'));
        return $columns;
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

    static function is_rights_column($column)
    {
        return in_array($column, self :: $rights_columns);
    }

    function add_rights_columns()
    {
        $rights = $this->browser->get_available_rights();
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Utilities :: underscores_to_camelcase(strtolower($right_name));
            $column = new StaticTableColumn(Translation :: get($column_name));
            $this->add_column($column);
            self :: $rights_columns[] = $column;
        }
    }
}
?>