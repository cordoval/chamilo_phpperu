<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\AndCondition;

require_once dirname(__FILE__) . '/context_template_rel_page_browser/rel_page_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';

class SurveyBuilderContextViewerComponent extends SurveyBuilder
{
    private $template;
    private $ab;
    private $survey_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $id = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID);

        if ($id)
        {

            $this->template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($id);

            $template = $this->template;

            $this->display_header();
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_template.png);">';
            echo '<div class="title">' . Translation :: get('SurveyContextTemplateDetails') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $template->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $template->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
            echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_users.png);">';
            echo '<div class="title">' . Translation :: get('SurveyPages') . '</div>';
            $parameters = $this->get_parameters();
            $parameters[SurveyBuilder :: PARAM_TEMPLATE_ID] = $id;
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();

            $table = new SurveyContextTemplateRelPageBrowserTable($this, $parameters, $this->get_condition());
            echo $table->as_html();
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID));
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->get_root_content_object_id());

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_NAME, '*' . $query . '*', SurveyPage :: get_table_name());
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyPage :: get_table_name());
            $condition = new OrCondition($or_conditions);

            $pages = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
            while ($page = $pages->next_result())
            {
                $page_conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $page->get_id());
            }

            if (count($page_conditions))
            {
                $conditions[] = new OrCondition($page_conditions);
            }

            else
            {
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, 0);

            }

        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $template = $this->template;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(SurveyBuilder :: PARAM_TEMPLATE_ID => $template->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_template_viewing_url($template->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddSurveyPages'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_template_suscribe_page_browser_url($template->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $condition = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $template->get_id());
        $pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
        $visible = ($pages->size() > 0);

        if ($visible)
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_template_emptying_url($template->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_TEMPLATE_ID);
    }

}
?>