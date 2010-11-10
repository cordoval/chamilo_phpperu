<?php

namespace application\context_linker;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/context_link_browser/context_link_browser_table.class.php';
/**
 * context_linker component which allows the user to browse his context_links
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinksBrowserComponent extends ContextLinkerManager
{

    function run()
    {
        $this->display_header();

        $html = array();

        $html[]= $this->get_action_bar();
        if(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))
        {
            $html[] = $this->get_table();
        }
        else
        {
            $html[] = '<p>' . Translation :: get('NoContentObjectSelected', null, 'repository') . '</p>';
        }

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID));
        $table = new ContextLinkBrowserTable($this, array(Application :: PARAM_APPLICATION => 'context_linker', Application :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS), $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('CreateObject', array('OBJECT' => Translation :: get('ContextLink')), Utilities :: COMMON_LIBRARIES) . Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar->as_html();
    }

    

    
}
?>