<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\EqualityCondition;

require_once dirname ( __FILE__ ) . '/context_template_subscribe_page_browser/subscribe_page_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey_page/survey_page.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';


class SurveyBuilderContextTemplateSubscribePageBrowserComponent extends SurveyBuilder
{
    private $template;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template_id = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID);
        $this->template = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($template_id);

        $this->ab = $this->get_action_bar();
        $output = $this->get_html();

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_html()
    {
        $parameters = $this->get_parameters();
        $parameters[SurveyBuilder :: PARAM_TEMPLATE_ID ] = $this->template->get_id();
    	$parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();

        $table = new SurveyContextTemplateSubscribePageBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $root_conditions = array();
    	$root_conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID));
		$root_conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->get_root_content_object_id());
        $condition = new AndCondition($root_conditions);
        $template_rel_pages = SurveyContextDataManager::get_instance()->retrieve_template_rel_pages($condition);


        $template_pages= array();
        while ($template_rel_page = $template_rel_pages->next_result())
        {
            $template_pages[] =  $template_rel_page->get_page_id();
        }

       $survey = RepositoryDataManager::get_instance()->retrieve_content_object($this->get_root_content_object_id());

        $pages = $survey->get_pages();

        $survey_pages = array();
        foreach ($pages as $page) {
        	$survey_pages[] = $page->get_id();
        }

        $not_template_pages = array_diff($survey_pages, $template_pages);

        $conditions = array();

        if(!count($not_template_pages)){
        	$not_template_pages[] = 0;
        }
        $conditions[] = new InCondition(SurveyPage :: PROPERTY_ID,$not_template_pages, SurveyPage::get_table_name());

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0){
        	 return null;
        }
		$condition = new AndCondition($conditions);

        return $condition;
    }

    function get_survey_context_template()
    {
        return $this->template;
    }

    function get_action_bar()
    {
        $template = $this->template;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_template_suscribe_page_browser_url($template));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_template_suscribe_page_browser_url($template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>