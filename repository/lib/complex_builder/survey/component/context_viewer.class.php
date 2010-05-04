<?php

require_once dirname(__FILE__) . '/context_template_rel_page_browser/rel_page_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';

class SurveyBuilderContextViewerComponent extends SurveyBuilderComponent
{
    private $template;
    private $ab;
    private $survey_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        
        $id = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID);
        
        if ($id)
        {
            
            $this->template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($id);
            
            $template = $this->template;
            
            $trail->add(new Breadcrumb($this->get_configure_context_url(), Translation :: get('BrowseContexts')));
            $trail->add(new Breadcrumb($this->get_url(array(SurveyBuilder :: PARAM_TEMPLATE_ID => $id)), $template->get_name()));
            
            $this->display_header($trail);
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
            $parameters[SurveyBuilder :: PARAM_TEMPLATE_ID ] =  $id;
            $parameters[SurveyBuilder :: PARAM_ROOT_LO] =  $this->get_root_lo()->get_id();
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
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, Request :: get(SurveyBuilder :: PARAM_ROOT_LO));
        
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

}
?>