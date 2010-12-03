<?php
namespace repository\content_object\forum;

use common\extensions\repo_viewer\ContentObjectTableColumnModel;

use repository\ContentObject;
/**
 * $Id: forumtablecolumnmodel.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum.inc
 */

class ForumTableColumnModel extends ContentObjectTableColumnModel
{

    function __construct()
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