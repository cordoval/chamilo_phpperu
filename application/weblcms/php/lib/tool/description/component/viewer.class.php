<?php

class DescriptionToolViewerComponent extends DescriptionTool
{

function run()
	{
		$component = ToolComponent::factory(ToolComponent::ACTION_VIEW, $this);
		$component->run();
	}
    
    
//    function run()
//    {
//        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
//        {
//            Display :: not_allowed();
//            return;
//        }
//
//        $conditions = array();
//        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
//        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'description');
//
//        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
//        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
//        $condition = new AndCondition($conditions);
//
//        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
//        $this->introduction_text = $publications->next_result();
//
//        $this->action_bar = $this->get_action_bar();
//
//        $browser = new DescriptionBrowser($this);
//        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add_help('courses description tool');
//
//        if (Request :: get(Tool :: PARAM_PUBLICATION_ID) != null && Request :: get('tool_action') == 'view')
//            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
//
//        $html = $browser->as_html();    
//            
//        $this->display_header();
//
//        //echo '<br /><a name="top"></a>';
//        //echo $this->perform_requested_actions();
//        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
//        {
//            if ($this->get_course()->get_intro_text())
//            {
//                echo $this->display_introduction_text($this->introduction_text);
//            }
//        }
//        else
//        {
//
//        }
//        echo $this->action_bar->as_html() . '<br />';
//        echo '<div style="width:100%; float:right;">';
//        echo $html;
//        echo '</div>';
//
//        $this->display_footer();
//    }

//    function add_actionbar_item($item)
//    {
//        $this->action_bar->add_tool_action($item);
//    }
//
//    function get_action_bar()
//    {
//        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
//
//        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
//        {
//            $action_bar->set_search_url($this->get_url());
//            if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
//            {
//                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(DescriptionTool :: PARAM_ACTION => DescriptionTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//            }
//        }
//
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//
//        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
//        {
//            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//        }
//
//        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
//        {
//            $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
//        }
//
//        return $action_bar;
//    }
//
//    function get_condition()
//    {
//        $query = $this->action_bar->get_query();
//        if (isset($query) && $query != '')
//        {
//            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
//            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
//            return new OrCondition($conditions);
//        }
//
//        return null;
//    }

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DescriptionToolBrowserComponent')));
    }
    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>