<?php 
namespace repository\content_object\survey;

use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\ActionBarSearchForm;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;

require_once dirname(__FILE__) . '/registration_browser/browser_table.class.php';

class SurveyContextManagerRegistrationBrowserComponent extends SurveyContextManager
{
    private $ab;
    
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->ab = $this->get_action_bar();
        
        $output = $this->get_browser_html();
        
        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        $table = new SurveyContextRegistrationBrowserTable($this, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(SurveyContextRegistration :: PROPERTY_NAME, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            $conditions[] = new PatternMatchCondition(SurveyContextRegistration :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            $condition = new OrCondition($conditions);
        
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
              
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_registration_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ViewRoot' ), Theme::get_common_image_path () . 'action_home.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
        //		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ShowAll' ), Theme::get_common_image_path () . 'action_browser.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: RIGHT_VIEW, SurveyContextManagerRights :: LOCATION_CONTEXT_REGISTRATION, SurveyContextManagerRights :: TYPE_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRights', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url(SurveyContextManagerRights :: LOCATION_CONTEXT_REGISTRATION), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }
    
    
    
}
?>