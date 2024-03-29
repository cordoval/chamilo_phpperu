<?php
namespace application\wiki;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use repository\ContentObject;
use repository\content_object\wiki\WikiDisplay;

use application\gradebook\EvaluationManager;
/**
 * $Id: wiki_publication_browser_table_cell_renderer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component.wiki_publication_browser
 */

/**
 * Cell rendere for the learning object browser table
 * @author Sven Vanpoucke & Stefan Billiet
 */

class WikiPublicationBrowserTableCellRenderer extends DefaultWikiPublicationTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $wiki_publication)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case ContentObject :: PROPERTY_TITLE :
                    $url = $this->browser->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
                    return '<a href="' . $url . '">' . htmlspecialchars($wiki_publication->get_content_object()->get_title()) . '</a>';
                case ContentObject :: PROPERTY_DESCRIPTION :
                    return Utilities :: truncate_string($wiki_publication->get_content_object()->get_description(), 2000, false);
            }
        }
        if ($column === WikiPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($wiki_publication);
        }
        return parent :: render_cell($column, $wiki_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($wiki_publication)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_wiki_publication_url($wiki_publication), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_wiki_publication_url($wiki_publication), ToolbarItem :: DISPLAY_ICON, true));

        if (WebApplication :: is_active('gradebook'))
        {
            if (EvaluationManager :: retrieve_internal_item_by_publication(WikiManager :: APPLICATION_NAME, $wiki_publication->get_id()))
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Evaluation', null, 'application\gradebook'), Theme :: get_common_image_path() . 'action_evaluation.png', $this->browser->get_evaluation_publication_url($wiki_publication), ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
?>