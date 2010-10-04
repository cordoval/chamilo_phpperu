<?php
/**
 * $Id: forumtable.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum.inc
 */
require_once dirname(__FILE__) . '/forumtabledataprovider.class.php';
require_once dirname(__FILE__) . '/forumtablecolumnmodel.class.php';
require_once dirname(__FILE__) . '/forumtablecellrenderer.class.php';
require_once dirname(__FILE__) . '/../../../content_object_table/content_object_table.class.php';

class ForumTable extends ContentObjectTable
{

    function ForumTable($forum, $url_format)
    {
        $name = 'forumtable' . $forum->get_id();
        $data_provider = new ForumTableDataProvider($forum);
        $column_model = new ForumTableColumnModel();
        $cell_renderer = new ForumTableCellRenderer($url_format);
        parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
    }
}
?>