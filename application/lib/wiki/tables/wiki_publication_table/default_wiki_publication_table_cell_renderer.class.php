<?php
/**
 * $Id: default_wiki_publication_table_cell_renderer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.tables.wiki_publication_table
 */

require_once dirname(__FILE__) . '/../../wiki_publication.class.php';
//require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

/**
 * Default cell renderer for the wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class DefaultWikiPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultWikiPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param WikiPublication $wiki_publication - The wiki_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $wiki_publication)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                //return $wiki_publication->get_content_object()->get_title();
                $url = $this->browser->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
                return '<a href="' . $url . '">' . htmlspecialchars($wiki_publication->get_content_object()->get_title()) . '</a>';
            case ContentObject :: PROPERTY_DESCRIPTION :
                return $wiki_publication->get_content_object()->get_description();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>