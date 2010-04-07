<?php
/**
 * $Id: content_object_table_column_model.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/content_object_table/content_object_table_column_model.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class CompetenceContentObjectTableColumnModel extends ContentObjectTableColumnModel
{ 
    /**
     * Constructor.
     */
    function CompetenceContentObjectTableColumnModel()
    {
        ObjectTableColumnModel :: __construct(self :: get_columns(), 1, SORT_ASC);
    }

    /**
     * Gets the columns of this table.
     * @return array An array of all columns in this table.
     * @see ContentObjectTableColumn
     */
    function get_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn('children', false);
        $columns[] = parent :: get_action_column();
        return $columns;
    }
}
?>