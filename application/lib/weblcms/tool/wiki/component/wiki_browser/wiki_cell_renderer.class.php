<?php
/**
 * $Id: wiki_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component.wiki_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class WikiCellRenderer extends ObjectPublicationTableCellRenderer
{

    function WikiCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
             return parent :: get_actions($publication)->as_html();
        }
        
        return parent :: render_cell($column, $publication);
    }

}
?>