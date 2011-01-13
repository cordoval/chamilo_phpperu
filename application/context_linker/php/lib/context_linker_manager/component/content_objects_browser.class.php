<?php
namespace application\context_linker;
use common\libraries\Application;
use common\libraries\EqualityCondition;
use repository\ContentObject;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\PatternMatchCondition;
use common\libraries\AndCondition;
require_once dirname(__FILE__) . '/content_object_browser/content_object_browser_table.class.php';
/**
 * context_linker component which allows the user to browse his context_links
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContentObjectsBrowserComponent extends ContextLinkerManager
{
    private $action_bar;


    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $this->display_header();


        $html = array();
        $html[] = $this->action_bar->as_html();

        $html[] = $this->get_table();

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        
        $table = new ContentObjectBrowserTable($this, array(Application :: PARAM_APPLICATION => 'context_linker', Application :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTENT_OBJECTS),$this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

    function get_condition()
    {

        $conditions = array();
        $search = $this->action_bar->get_query();

        if (isset($search) && $search != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $search . '*');
        }

        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        $condition = new AndCondition($conditions);
        return $condition;
    }
}
?>