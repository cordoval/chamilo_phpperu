<?php
/**
 * $Id: calendar_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component
 */
require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__) . '/calendar_viewer/calendar_browser.class.php';

class CalendarToolViewerComponent extends CalendarToolComponent
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'calendar');
        
        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();
        $this->action_bar = $this->get_action_bar();
        
        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $view = Request :: get('view') ? Request :: get('view') : 'month';
        $this->set_parameter('time', $time);
        $this->set_parameter('view', $view);
        $browser = new CalendarBrowser($this);
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses calendar tool');
        
        if (Request :: get('view') != null)
        {
            if (Request :: get('today') == 1)
            {
                $title = 'Today';
                $trail->add(new Breadcrumb($this->get_url(array('today' => 1)), $title));
            }
            else
            {
                $title = Translation :: get(ucfirst(Request :: get('view')) . 'View');
                $trail->add(new Breadcrumb($this->get_url(), $title));
            }
        }
        
        if (Request :: get('pid') != null)
        {
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'))->get_content_object()->get_title()));
        }
        
        $this->display_header($trail, true);
        echo '<br /><a name="top"></a>';
        if (! Request :: get('pid'))
        {
            if (PlatformSetting :: get('enable_introduction', 'weblcms'))
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        $html = $browser->as_html();
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $html;
        echo '</div>';
        
        $this->display_footer();
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if (! Request :: get('pid'))
        {
            $action_bar->set_search_url((Request :: get('view') == 'list') ? $this->get_url(array('view' => Request :: get('view'))) : null);
            
            if ($this->is_allowed(ADD_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        if (Request :: get('view') == 'list')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('view' => 'list', Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (! $this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (! Request :: get('pid'))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path() . 'tool_calendar_down.png', $this->get_url(array('view' => 'list', 'time' => Request :: get('time'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path() . 'tool_calendar_month.png', $this->get_url(array('view' => 'month', 'time' => Request :: get('time'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path() . 'tool_calendar_week.png', $this->get_url(array('view' => 'week', 'time' => Request :: get('time'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path() . 'tool_calendar_day.png', $this->get_url(array('view' => 'day', 'time' => Request :: get('time'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path() . 'tool_calendar_today.png', $this->get_url(array('view' => (Request :: get('view') ? Request :: get('view') : 'month'), 'time' => time(), 'today' => true)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
        }
        
        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
            $conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
            return new OrCondition($conditions);
        }
        
        return null;
    }
    
/*function display_introduction_text()
	{
		$html = array();

		$introduction_text = $this->introduction_text;

		if($introduction_text)
		{

			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => Utilities :: TOOLBAR_DISPLAY_ICON
			);

			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => Utilities :: TOOLBAR_DISPLAY_ICON
			);

			$html[] = '<div class="content_object">';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_content_object()->get_description();
			$html[] = '</div>';
			$html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}

		return implode("\n",$html);
	}*/
}
?>