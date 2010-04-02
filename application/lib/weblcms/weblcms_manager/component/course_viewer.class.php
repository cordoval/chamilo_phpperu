<?php
/**
 * $Id: course_viewer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
/**
 * Weblcms component which provides the course page
 */
class WeblcmsManagerCourseViewerComponent extends WeblcmsManagerComponent
{
	private $rights;

	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->load_rights();
		
		if ($this->is_teacher())
		{
			$studentview = Request :: get('studentview');
			if (is_null($studentview))
			{
				$studentview = Session :: retrieve('studentview');
			}

			if ($studentview == 1)
			{
				Session :: register('studentview', 1);
				$this->set_rights_for_student();
			}
			else
			{
				Session :: unregister('studentview');
				$this->set_rights_for_teacher();
			}
		}
		
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses general');

		if (! $this->is_course())
		{
			$this->display_header($trail, false, true, false);
			Display :: error_message(Translation :: get("NotACourse"));
			$this->display_footer();
			exit();
		}
		
		if($studentview && $this->get_course()->get_student_view() != 1)
		{
			if($this->is_teacher())
				$this->redirect(Translation :: get('StudentViewNotAvailable'), true, array('studentview'=>0));
			$this->display_header($trail, false, false, false);
			Display :: error_message(Translation :: get("StudentViewNotAvailable"));
			$this->display_footer();
			exit();
		}

		$this->load_course_theme();
		$this->load_course_language();

		/**
		 * Here we set the rights depending on the user status in the course.
		 * This completely ignores the roles-rights library.
		 * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
		 */

		$user = $this->get_user();
		$course = $this->get_course();
		if ($user != null && $course != null)
		$relation = $this->retrieve_course_user_relation($course->get_id(), $user->get_id());

		/*if(!$user->is_platform_admin() && (!$relation || ($relation->get_status() != 5 && $relation->get_status() != 1)))
		 //TODO: Roles & Rights
		 //if(!$this->is_allowed(VIEW_RIGHT) && !$this->get_user()->is_platform_admin())
		 {
			$this->display_header($trail, false, true);
			Display :: not_allowed();
			$this->display_footer();
			exit;
			}*/

		$course = $this->get_parameter(WeblcmsManager :: PARAM_COURSE);
		$tool = $this->get_parameter(WeblcmsManager :: PARAM_TOOL);
		$action = $this->get_parameter(Application :: PARAM_ACTION);
		$component_action = $this->get_parameter(WeblcmsManager :: PARAM_COMPONENT_ACTION);
		$category = $this->get_parameter(WeblcmsManager :: PARAM_CATEGORY);

		if (is_null($category))
		{
			$category = 0;
		}

		if ($course)
		{
			if ($component_action)
			{
				$wdm = WeblcmsDataManager :: get_instance();
				switch ($component_action)
				{
					case 'make_visible' :
						$wdm->set_module_visible($this->get_course_id(), $tool, 1);
						$this->load_tools();
						break;
					case 'make_invisible' :
						$wdm->set_module_visible($this->get_course_id(), $tool, 0);
						$this->load_tools();
						break;
					case 'make_publication_invisible' :
						$publication = $wdm->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
						$publication->set_hidden(1);
						$publication->update();
						break;
					case 'make_publication_visible' :
						$publication = $wdm->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
						$publication->set_hidden(0);
						$publication->update();
						break;
					case 'delete_publication' :
						$publication = $wdm->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
						$publication->set_show_on_homepage(0);
						$publication->update();
						break;
				}
				$this->set_parameter(WeblcmsManager :: PARAM_TOOL, null);
			}
			if ($tool && ! $component_action)
			{
				if ($tool != 'course_group')
				{
					$this->set_parameter('course_group', null);
				}

				$wdm = WeblcmsDataManager :: get_instance();
				$class = Tool :: type_to_class($tool);
				/*$toolObj = new $class($this);*/
				
				$toolObj = Tool :: factory($tool, $this);
				
				$this->set_tool_class($class);
				$toolObj->run();
				$wdm->log_course_module_access($this->get_course_id(), $this->get_user_id(), $tool, $category);
			}
			else
			{
				$trail = new BreadcrumbTrail();
				$this->set_parameter(Tool :: PARAM_PUBLICATION_ID, null);
				$this->set_parameter('tool_action', null);
				$this->set_parameter('course_group', null);

				$title = CourseLayout :: get_title($this->get_course());

				if (Request :: get('previous') == 'admin')
				{
					$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
					$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
				}
				else
				{
					$trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
				}
				$trail->add(new Breadcrumb($this->get_url(), $title));
				$trail->add_help('courses general');

				$wdm = WeblcmsDataManager :: get_instance();

				$this->display_header($trail, false, true);

				/*$tb_data = array();
				 $tb_data[] = array(
					'href' => $this->get_course()->get_extlink_url(),
					'label' => $this->get_course()->get_extlink_name(),
					'icon' => Theme :: get_common_image_path().'action_home.png',
					'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);*/
				//dump($tb_data);
				//echo Utilities :: build_toolbar($tb_data);

				//Display menu
				$menu_style = $this->get_course()->get_menu();
				if ($menu_style != CourseLayout :: MENU_OFF)
				{
					$renderer = ToolListRenderer :: factory('Menu', $this);
					$renderer->display();
					echo '<div id="tool_browser_' . ($renderer->display_menu_icons() && ! $renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() . '">';
				}
				else
				{
					echo '<div id="tool_browser">';
				}
				if ($this->get_course()->get_intro_text())
				{
					echo $this->display_introduction_text();
					echo '<div class="clear"></div>';
				}

				$renderer = ToolListRenderer :: factory('FixedLocation', $this);
				$renderer->display();
				echo '</div>';
				$this->display_footer();
				$wdm->log_course_module_access($this->get_course_id(), $this->get_user_id(), 'course_home');
			}
		}
		else
		{
			Display :: header(Translation :: get('MyCourses'), 'Mycourses');
			$this->display_footer();
		}
	}

	// TODO: New Roles & Rights system
	//	function is_allowed($right)
	//	{
	//		$user_id = $this->get_user_id();
	//		$course_id = $this->get_course_id();
	//		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
	//		$location_id = RolesRights::get_course_location_id($course_id, TOOL_COURSE_HOMEPAGE);
	//
	//		$result = RolesRights::is_allowed_which_rights($role_id, $location_id);
	//		return $result[$right];
	//	}


	function is_course()
	{
		return ($this->get_course()->get_id() != null ? true : false);
	}

	function load_course_theme()
	{
		$course_can_have_theme = $this->get_platform_setting('allow_course_theme_selection');
		$course = $this->get_course();

		if ($course_can_have_theme && $course->has_theme())
		{
			Theme :: set_theme($course->get_theme());
		}
	}

	function load_course_language()
	{
		$course = $this->get_course();
		Translation :: set_language($course->get_language());
	}

	function display_introduction_text()
	{
		$html = array();

		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'introduction');
		$condition = new AndCondition($conditions);

		$publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
		$introduction_text = $publications->next_result();

		if ($introduction_text)
		{
			if ($this->is_allowed(EDIT_RIGHT))
			{
				$tb_data[] = array('href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_EDIT_INTRODUCTION)), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);
			}

			if ($this->is_allowed(DELETE_RIGHT))
			{
				$tb_data[] = array('href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_DELETE_INTRODUCTION)), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);
			}

			$html = array();

			$html[] = '<div class="block" id="block_introduction" style="background-image: url(' . Theme :: get_image_path('home') . 'block_home.png);">';
			$html[] = '<div class="title"><div style="float:left;">' . $introduction_text->get_content_object()->get_title() . '</div>';
			$html[] = '<a href="#" class="closeEl"><img class="visible" src="' . Theme :: get_common_image_path() . 'action_visible.png"/><img class="invisible" style="display: none;") src="' . Theme :: get_common_image_path() . 'action_invisible.png" /></a>';
			$html[] = '<div style="clear: both;"></div></div>';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_content_object()->get_description();
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		else
		{
			if ($this->is_allowed(EDIT_RIGHT))
			{
				$tb_data[] = array('href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_PUBLISH_INTRODUCTION)), 'label' => Translation :: get('PublishIntroductionText'), 'img' => Theme :: get_common_image_path() . 'action_introduce.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			}

			$html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
		}

		return implode("\n", $html);
	}

	/**
	 * Displays the header of this application
	 * @param array $breadcrumbs The breadcrumbs which should be displayed
	 */
	function display_header($breadcrumbtrail, $display_search = false, $display_title = true, $display_tools = true)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}

		$tool_class = $this->get_parameter(WeblcmsManager :: PARAM_TOOL);
		$course = $this->get_parameter(WeblcmsManager :: PARAM_COURSE);
		$action = $this->get_parameter(WeblcmsManager :: PARAM_ACTION);

		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50) . '&hellip;';
		}

		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('course_admin');
		$admin_tool = false;
		foreach($tools as $tool)
		{
			if($tool_class == $tool)
				$admin_tool = true;
		}
		
		if ($this->is_teacher() && $this->get_course()->get_student_view() == 1 && !$admin_tool)
		{
			$studentview = Session :: retrieve('studentview');

			if ($studentview == 1)
			{
				$breadcrumbtrail->add_extra(new ToolbarItem(Translation :: get('TeacherView'), Theme :: get_image_path() . 'action_teacher_view.png', $this->get_url(array('studentview' => '0', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
				//echo '<a href="' . $this->get_url(array('studentview' => '0', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))) . '">' . Translation :: get('TeacherView') . '</a>';
			}
			else
			{
				$breadcrumbtrail->add_extra(new ToolbarItem(Translation :: get('StudentView'), Theme :: get_image_path() . 'action_student_view.png', $this->get_url(array('studentview' => '1', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
				//echo '<a href="' . $this->get_url(array('studentview' => '1', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))) . '">' . Translation :: get('StudentView') . '</a>';
			}
		}

		Display :: header($breadcrumbtrail);

		if (isset($tool_class))
		{
			if ($display_title)
			{
				echo '<div style="float: left;">';
				Display :: tool_title(htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($tool_class) . 'Title')));
				echo '</div>';
			}

		}
		else
		{
			if ($course && is_object($this->get_course()) && $action == WeblcmsManager :: ACTION_VIEW_COURSE)
			{
				
				//echo '<h3 style="float: left;">'.htmlentities($this->course->get_name()).'</h3>';
				echo '<h3 style="float: left;">' . htmlentities($title) . '</h3>';
				// TODO: Add department name and url here somewhere ?
			}
			else
			{
				echo '<h3 style="float: left;">' . htmlentities($title) . '</h3>';
				if ($display_search)
				{
					$this->display_search_form();
				}
			}

			if ($this->get_course()->get_tool_shortcut() == CourseLayout :: TOOL_SHORTCUT_ON && $display_tools)
			{
				$renderer = ToolListRenderer :: factory('Shortcut', $this);
				echo '<div id="tool_shortcuts">';
				$renderer->display();
				echo '</div>';
			}

			echo '<div class="clear">&nbsp;</div>';
		}

		if (! isset($tool_class))
		{
			if ($msg = Request :: get(Application :: PARAM_MESSAGE))
			{
				//                echo '<br />';
				$this->display_message($msg);
			}
			if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
			{
				//                echo '<br />';
				$this->display_error_message($msg);
			}
		}

		//echo 'Last visit: '.date('r',$this->get_last_visit_date());
	}

	function is_allowed($right)
	{
		return $this->rights[$right];
	}

	/**
	 * Load the rights for the current user in this tool
	 */
	private function load_rights()
	{
		$this->rights[VIEW_RIGHT] = true;

		$studentview = Session :: retrieve('studentview');

		if ($this->is_teacher() && $studentview != '1')
		{
			$this->set_rights_for_teacher();
		}
		else
		{
			$this->set_rights_for_student();
		}
	}

	private function set_rights_for_teacher()
	{
		$this->rights[EDIT_RIGHT] = true;
		$this->rights[ADD_RIGHT] = true;
		$this->rights[DELETE_RIGHT] = true;
	}

	private function set_rights_for_student()
	{
		$this->rights[EDIT_RIGHT] = false;
		$this->rights[ADD_RIGHT] = false;
		$this->rights[DELETE_RIGHT] = false;
	}

	private $is_teacher;

	private function is_teacher()
	{
		if (is_null($this->is_teacher))
		{
			$user = $this->get_user();
			$course = $this->get_course();

			if ($user != null && $course != null)
			{
				$relation = $this->retrieve_course_user_relation($course->get_id(), $user->get_id());

				if (($relation && $relation->get_status() == 1) || $user->is_platform_admin())
				{
					$this->is_teacher = true;
					return $this->is_teacher;
				}
			}

			$this->is_teacher = false;
		}

		return $this->is_teacher;
	}
}
?>