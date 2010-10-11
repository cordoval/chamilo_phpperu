<?php
/**
 * $Id: forumtablecolumnmodel.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum.inc
 */
require_once dirname(__FILE__) . '/../../../content_object_table/content_object_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../content_object_table/content_object_table_column.class.php';

class ForumTableColumnModel extends ContentObjectTableColumnModel
{

    function ForumTableColumnModel()
    {
        parent :: __construct(self :: get_columns(), 3, SORT_DESC);
    }

    function get_columns()
    {
        $columns = array();
        $columns[] = new ContentObjectTableColumn(ContentObject :: PROPERTY_TYPE, true);
        $columns[] = new ContentObjectTableColumn(ContentObject :: PROPERTY_TITLE, true);
        $columns[] = new ContentObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true);
        $columns[] = new ContentObjectTableColumn(ContentObject :: PROPERTY_CREATION_DATE, true);
        $columns[] = new ContentObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE, true);
        return $columns;
    }
}
?>