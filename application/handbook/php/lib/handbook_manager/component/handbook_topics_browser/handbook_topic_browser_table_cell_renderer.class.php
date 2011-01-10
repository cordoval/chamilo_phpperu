<?php
namespace application\handbook;
use common\libraries\Toolbar;
use user\User;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\EqualityCondition;
use common\libraries\Application;
use common\libraries\Request;
require_once   dirname(__FILE__) .  '/handbook_topic_browser_table_column_model.class.php';
require_once   dirname(__FILE__) . '/../../../tables/handbook_topic_table/default_handbook_topic_table_cell_renderer.class.php';

/**
 * Cell renderer for the handbook_topic object browser table
 */
class HandbookTopicBrowserTableCellRenderer extends DefaultHandbookTopicTableCellRenderer
{
    /**
     * The handbook_topic browser component
     */
    public $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

 
    function render_cell($column, $handbook)
    {
        if ($column === HandbookTopicBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($handbook);
        }        
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $handbook->get_id();
        }
        return parent :: render_cell($column, $handbook);
    }

    /**
     * Gets the action links to display
     * @param $handbook The handbook for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($handbook)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('View'),
        		Theme :: get_common_image_path() . 'action_browser.png',
        		 $this->browser->get_url(array(
                        Application::PARAM_APPLICATION => HandbookManager::APPLICATION_NAME,
                        HandbookManager :: PARAM_ACTION => HandbookManager:: ACTION_VIEW_HANDBOOK,
                        HandbookManager :: PARAM_TOP_HANDBOOK_ID => Request::get(HandbookManager :: PARAM_TOP_HANDBOOK_ID),
//                        HandbookManager::PARAM_HANDBOOK_ID => Request::get(HandbookManager :: PARAM_TOP_HANDBOOK_ID),
                         HandbookManager::PARAM_HANDBOOK_SELECTION_ID => $handbook->get_id(),
                        HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID => Request::get(HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID)))));


        return $toolbar->as_html();
    }
}
?>