<?php
namespace repository\content_object\survey;

use common\libraries\OrCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/context_template_table/table.class.php';

class SurveyContextManagerContextTemplateBrowserComponent extends SurveyContextManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $trail = BreadcrumbTrail :: get_instance();

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

        $table = new SurveyContextTemplateBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_PARENT_ID, 1);

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
            $or_condition = new OrCondition($search_conditions);

        }

        if ($or_condition)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = $or_condition;
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_template_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: RIGHT_VIEW, SurveyContextManagerRights :: LOCATION_CONTEXT_REGISTRATION, SurveyContextManagerRights :: TYPE_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRights', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url(SurveyContextManagerRights :: LOCATION_CONTEXT_REGISTRATION), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }
}
?>