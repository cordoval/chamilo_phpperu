<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Request;

use common\libraries\InCondition;
use repository\ContentObject;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\ActionBarSearchForm;
use common\libraries\Application;
use common\libraries\PatternMatchCondition;
use repository\content_object\handbook_topic\HandbookTopic;
use common\libraries\AndCondition;
use repository\ComplexBrowserTable;
use common\libraries\Toolbar;
use common\extensions\repo_viewer\ContentObjectTable;
use common\libraries\OrCondition;



require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/handbook_topics_browser/handbook_topic_browser_table.class.php';


class HandbookManagerSearchResultsBrowserComponent extends HandbookManager
{
        
        private $action_bar;

    function run()
    {
      
                
        $this->action_bar = $this->get_action_bar();
        $output = $this->get_publications_html();



        $this->display_header();

        echo $this->action_bar->as_html();
     
        echo $output;
        echo '</div>';

        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(self::PARAM_TOP_HANDBOOK_ID => Request::get(self::PARAM_TOP_HANDBOOK_ID))));

        return $action_bar;
    }

    private function get_publications_html()
    {
       
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();;
        $parameters[Application :: PARAM_APPLICATION] = 'handbook';
        $parameters[Application :: PARAM_ACTION] = HandbookManager :: ACTION_SEARCH;
            $parameters[self::PARAM_TOP_HANDBOOK_ID] = Request::get(self::PARAM_TOP_HANDBOOK_ID);

        $actions = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        $table = new HandbookTopicBrowserTable($this, array(
                Application :: PARAM_APPLICATION => 'handbook',
                Application :: PARAM_ACTION => HandbookManager :: ACTION_SEARCH,
                self::PARAM_TOP_HANDBOOK_ID => Request::get(self::PARAM_TOP_HANDBOOK_ID)),
                $this->get_condition());


        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {

        $conditions = array();
        $ids_array = self::get_all_text_items_of_handbook(Request::get(self::PARAM_TOP_HANDBOOK_ID));
        $search = $this->action_bar->get_query();
        $conditions[] = new InCondition(ContentObject::PROPERTY_ID, $ids_array,  ContentObject::get_table_name());

        if (isset($search) && $search != '')
        {
            $search_conditions[] = new PatternMatchCondition(HandbookTopic::PROPERTY_TEXT, '*' . $search . '*', HandbookTopic::get_table_name());
            $search_conditions[] = new PatternMatchCondition(HandbookTopic::PROPERTY_TITLE, '*' . $search . '*', ContentObject::get_table_name());
            $search_conditions[] = new PatternMatchCondition(HandbookTopic::PROPERTY_DESCRIPTION, '*' . $search . '*', ContentObject::get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        }

        $condition = new AndCondition($conditions);
        return $condition;
    }

   function get_maximum_select()
    {
        return 1;
    }

    function is_shared_object_browser()
    {
        return 0;
    }

    function get_excluded_objects()
    {
        return array();
    }

    function get_query()
    {
      
        return $this->get_condition();
    }

}
?>