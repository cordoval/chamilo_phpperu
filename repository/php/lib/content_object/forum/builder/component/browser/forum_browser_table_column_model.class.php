<?php
/**
 * $Id: forum_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class ForumBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{

    /**
     * Constructor
     */
    function ForumBrowserTableColumnModel($browser)
    {
        $columns[] = new StaticTableColumn(Translation :: get('AddDate'));
        parent :: __construct($browser, $columns);
    }
}
?>