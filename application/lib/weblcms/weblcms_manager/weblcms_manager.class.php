<?php
/**
 * $Id: weblcms_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager
 */
require_once dirname(__FILE__) . '/weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/weblcms_search_form.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php';
require_once dirname(__FILE__) . '/../tool/tool.class.php';
require_once dirname(__FILE__) . '/../tool_list_renderer.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';
require_once dirname(__FILE__) . '/../course/course_settings.class.php';
require_once dirname(__FILE__) . '/../course/course_rights.class.php';
require_once dirname(__FILE__) . '/../course/course_group_subscribe_right.class.php';
require_once dirname(__FILE__) . '/../course/course_group_unsubscribe_right.class.php';
require_once dirname(__FILE__) . '/../course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/../course_group/course_group.class.php';
require_once dirname(__FILE__) . '/component/admin_course_browser/admin_course_browser_table.class.php';
require_once dirname(__FILE__) . '/component/admin_course_type_browser/admin_course_type_browser_table.class.php';
require_once dirname(__FILE__) . '/component/subscribed_user_browser/subscribed_user_browser_table.class.php';
require_once dirname(__FILE__) . '/component/subscribe_group_browser/subscribe_group_browser_table.class.php';
require_once dirname(__FILE__) . '/../weblcms_block.class.php';
require_once dirname(__FILE__) . '/../weblcms_rights.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type_layout.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type_settings.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type_tool.class.php';
require_once dirname(__FILE__) . '/../course_type/course_type_rights.class.php';

/**
 ==============================================================================
 *	This is an application that creates a fully fledged web-based learning
 *	content management system. The Web-LCMS is based on so-called "tools",
 *	which each represent a segment in the application.
 *
 *	@author Tim De Pauw
 ==============================================================================
 */

class WeblcmsManager extends WebApplication
{
	const APPLICATION_NAME = 'weblcms';

	const PARAM_COURSE = 'course';
	const PARAM_TYPE = 'type';
	const PARAM_ACTIVE = 'active';
	const PARAM_COURSE_GROUP = 'course_group';
	const PARAM_COURSE_TYPE = 'course_type';
	const PARAM_USERS = 'users';
	const PARAM_GROUP = 'group';
	const PARAM_TOOL = 'tool';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_CATEGORY = 'pcattree';
	const PARAM_COURSE_USER_CATEGORY_ID = 'category';
	const PARAM_COURSE_CATEGORY_ID = 'category';
	const PARAM_COURSE_USER = 'course';
	const PARAM_DIRECTION = 'direction';
	const PARAM_REMOVE_SELECTED = 'remove_selected';
	const PARAM_REMOVE_SELECTED_COURSE_TYPES ='remove selected coursetypes';
	const PARAM_ACTIVATE_SELECTED_COURSE_TYPES = 'activate selected coursetypes';
	const PARAM_DEACTIVATE_SELECTED_COURSE_TYPES = 'deactivate selected coursetypes';
	const PARAM_MOVE_SELECTED_COURSES = 'move selected courses';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED_AS_STUDENT = 'subscribe_selected_as_student';
	const PARAM_SUBSCRIBE_SELECTED_AS_ADMIN = 'subscribe_selected_as_admin';
	const PARAM_SUBSCRIBE_SELECTED_GROUP = 'subscribe_selected_group_admin';
	const PARAM_TOOL_ACTION = 'tool_action';
	const PARAM_STATUS = 'user_status';
	const PARAM_EXTRA = 'extra';
	const PARAM_PUBLICATION = 'publication';

	const ACTION_SUBSCRIBE = 'subscribe';
	const ACTION_MOVE_COURSE = 'coursemover';
	const ACTION_SUBSCRIBE_GROUP = 'subscribe_group';
	const ACTION_UNSUBSCRIBE_GROUP = 'unsubscribe_group';
	const ACTION_SUBSCRIBE_GROUP_USERS = 'subscribe_group_users';
	const ACTION_UNSUBSCRIBE = 'unsubscribe';
	const ACTION_VIEW_WEBLCMS_HOME = 'home';
	const ACTION_VIEW_COURSE = 'courseviewer';
	const ACTION_VIEW_COURSE_TYPE ='coursetypeviewer';
	const ACTION_CREATE_COURSE = 'coursecreator';
	const ACTION_IMPORT_COURSES = 'courseimporter';
	const ACTION_IMPORT_COURSE_USERS = 'courseuserimporter';
	const ACTION_MANAGER_SORT = 'sort';
	const ACTION_MANAGER_SUBSCRIBE = 'subscribe';
	const ACTION_MANAGER_UNSUBSCRIBE = 'unsubscribe';
	const ACTION_COURSE_CATEGORY_MANAGER = 'catmanager';
	const ACTION_ADMIN_COURSE_BROWSER = 'adminbrowser';
	const ACTION_ADMIN_COURSE_TYPE_CREATOR = 'admincoursetypecreator';
	const ACTION_ADMIN_COURSE_TYPE_BROWSER = 'admincoursetypebrowser';
	const ACTION_SELECT_COURSE_TYPE = 'selectcoursetype';
	const ACTION_DELETE_COURSE = 'coursedeleter';
	const ACTION_DELETE_COURSES_BY_COURSE_TYPE = 'coursetypecoursesdeleter';
	const ACTION_DELETE_COURSE_TYPE = 'coursetypedeleter';
	const ACTION_PUBLISH_INTRODUCTION = 'introduction_publisher';
	const ACTION_DELETE_INTRODUCTION = 'delete_introduction';
	const ACTION_EDIT_INTRODUCTION = 'edit_introduction';
	const ACTION_REPORTING = 'reporting';
	const ACTION_CHANGE_ACTIVATION = 'activitychanger';
	const ACTION_CHANGE_ACTIVE = 'activechanger';

	const ACTION_RENDER_BLOCK = 'block';

	/**
	 * The tools that this course offers.
	 */
	private $tools;
	/**
	 * The sections that this application offers.
	 */
	private $sections;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * The course object of the course currently active in this application
	 */
	private $course;

	/**
	 * The course_type object of the course currently active in this application
	 */
	private $course_type;

	/**
	 * The course_group object of the course_group currently active in this application
	 */
	private $course_group;

	private $search_form;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function WeblcmsManager($user)
	{
		parent :: __construct($user);
		$this->set_parameter(self :: PARAM_ACTION, Request :: get(self :: PARAM_ACTION));
		$this->set_parameter(self :: PARAM_COMPONENT_ACTION, Request :: get(self :: PARAM_COMPONENT_ACTION));
		$this->set_parameter(self :: PARAM_CATEGORY, Request :: get(self :: PARAM_CATEGORY));
		$this->set_parameter(self :: PARAM_COURSE, Request :: get(self :: PARAM_COURSE));
		$this->set_parameter(self :: PARAM_COURSE_GROUP, Request :: get(self :: PARAM_COURSE_GROUP));
		//$this->set_parameter(self :: PARAM_COURSE_TYPE, Request :: get(self :: PARAM_COURSE_TYPE));
		$this->set_parameter(self :: PARAM_TOOL, Request :: get(self :: PARAM_TOOL));

		$this->parse_input_from_table();

		$this->course_type = $this->load_course_type();
		$this->tools = array();
		$this->course = new Course();
		$this->load_course();
		$this->course_group = null;
		$this->load_course_group();
		$this->sections = array();
		$this->load_sections();
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();

		switch ($action)
		{
			case self :: ACTION_VIEW_COURSE :
				$component = WeblcmsManagerComponent :: factory('CourseViewer', $this);
				break;
			case self :: ACTION_CREATE_COURSE :
				$component = WeblcmsManagerComponent :: factory('CourseCreator', $this);
				break;
			case self :: ACTION_IMPORT_COURSES :
				$component = WeblcmsManagerComponent :: factory('CourseImporter', $this);
				break;
			case self :: ACTION_IMPORT_COURSE_USERS :
				$component = WeblcmsManagerComponent :: factory('CourseUserImporter', $this);
				break;
			case self :: ACTION_MANAGER_SUBSCRIBE :
				$component = WeblcmsManagerComponent :: factory('Subscribe', $this);
				break;
			case self :: ACTION_MANAGER_UNSUBSCRIBE :
				$component = WeblcmsManagerComponent :: factory('Unsubscribe', $this);
				break;
			case self :: ACTION_SUBSCRIBE_GROUP :
				$component = WeblcmsManagerComponent :: factory('GroupSubscribe', $this);
				break;
			case self :: ACTION_MOVE_COURSE :
                $component = WeblcmsManagerComponent :: factory('CourseMove', $this);
                break;
			case self :: ACTION_UNSUBSCRIBE_GROUP :
				$component = WeblcmsManagerComponent :: factory('GroupUnsubscribe', $this);
				break;
			case self :: ACTION_SUBSCRIBE_GROUP_USERS :
				$component = WeblcmsManagerComponent :: factory('GroupUsersSubscribe', $this);
				break;
			case self :: ACTION_MANAGER_SORT :
				$component = WeblcmsManagerComponent :: factory('Sorter', $this);
				break;
			case self :: ACTION_COURSE_CATEGORY_MANAGER :
				$component = WeblcmsManagerComponent :: factory('CourseCategoryManager', $this);
				break;
			case self :: ACTION_ADMIN_COURSE_BROWSER :
				$component = WeblcmsManagerComponent :: factory('AdminCourseBrowser', $this);
				break;
			case self :: ACTION_ADMIN_COURSE_TYPE_BROWSER :
                $component = WeblcmsManagerComponent :: factory('AdminCourseTypeBrowser', $this);
                break;
			case self :: ACTION_ADMIN_COURSE_TYPE_CREATOR :
				$component = WeblcmsManagerComponent :: factory('AdminCourseTypeCreator', $this);
				break;
		    case self :: ACTION_SELECT_COURSE_TYPE :
				$component = WeblcmsManagerComponent :: factory('CourseTypeSelector', $this);
				break;
			case self :: ACTION_DELETE_COURSE :
				$component = WeblcmsManagerComponent :: factory('CourseDeleter', $this);
				break;
			case self :: ACTION_DELETE_COURSES_BY_COURSE_TYPE :
				$component = WeblcmsManagerComponent :: factory('CourseTypeCoursesDeleter', $this);
				break;
			case self :: ACTION_DELETE_COURSE_TYPE :
				$component = WeblcmsManagerComponent :: factory('CourseTypeDeleter', $this);
				break;
			case self :: ACTION_VIEW_COURSE_TYPE :
				$component = WeblcmsManagerComponent :: factory('CourseTypeViewer', $this);
				break;
			case self :: ACTION_PUBLISH_INTRODUCTION :
				$component = WeblcmsManagerComponent :: factory('IntroductionPublisher', $this);
				break;
			case self :: ACTION_DELETE_INTRODUCTION :
				$component = WeblcmsManagerComponent :: factory('IntroductionDeleter', $this);
				break;
			case self :: ACTION_EDIT_INTRODUCTION :
				$component = WeblcmsManagerComponent :: factory('IntroductionEditor', $this);
				break;
			case self :: ACTION_CHANGE_ACTIVATION :
                $component = WeblcmsManagerComponent :: factory('ActiveChanger', $this);
                break;
			case self :: ACTION_REPORTING :
				$component = WeblcmsManagerComponent :: factory('Reporting', $this);
				break;
			case self :: ACTION_CHANGE_ACTIVE :
				$component = WeblcmsManagerComponent :: factory('ActivityChanger', $this);
				break;
			default :
				$this->set_action(self :: ACTION_VIEW_WEBLCMS_HOME);
				$component = WeblcmsManagerComponent :: factory('Home', $this);
		}
		$component->run();
	}

	/**
	 * Renders the weblcms block and returns it.
	 */
	function render_block($block)
	{
		$weblcms_block = WeblcmsBlock :: factory($this, $block);
		return $weblcms_block->run();
	}

	function set_tool_class($class)
	{
		return $this->tool_class = $class;
	}

	/**
	 * Gets the identifier of the current tool
	 * @return string The identifier of current tool
	 */
	function get_tool_id()
	{
		return $this->get_parameter(self :: PARAM_TOOL);
	}

 	/**
     * Retrieves the change active url
     * @return the change active component url
     */
    function get_change_active_url($type, $course_type_id)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_ACTIVE;
        $parameters[self :: PARAM_TYPE] = $type;
        $parameters[self :: PARAM_COURSE_TYPE] = $course_type_id;

        return $this->get_url($parameters);
    }

	/**
	 * Gets the user object for a given user
	 * @param int $user_id
	 * @return User
	 */
	function get_user_info($user_id)
	{
		return UserDataManager :: get_instance()->retrieve_user($user_id);
	}

	/**
	 * Returns the course that is being used.
	 * @return string The course.
	 */
	function get_course()
	{
		return $this->course;
	}

	/**
	 * Sets the course
	 * @param Course $course
	 */
	function set_course($course)
	{
		$this->course = $course;
	}

	function set_course_type($course_type)
	{
		$this->course_type = $course_type;
	}

	//function set_course_type($course_type)
	//{
	//	$this->course_type = $course_type;
	//}

	/**
	 * Returns the identifier of the course that is being used.
	 * @return string The course identifier.
	 */
	function get_course_id()
	{
		if ($this->course == null)
		return 0;

		return $this->course->get_id();
	}
	/*
	function get_course_type_id()
	{
		if($this->course_type == null)
		return 0;

		return $this->course_type->get_id();
	}
	*/

	/**
	 * Returns the course_group that is being used.
	 * @return string The course_group.
	 */
	function get_course_group()
	{
		return $this->course_group;
	}

	function get_course_type_deleting_all_courses_url($course_type)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_COURSES_BY_COURSE_TYPE, self :: PARAM_COURSE_TYPE =>$course_type->get_id()));
	}
	/**
	 * Returns the course_group that is being used.
	 * @return string The course_group.
	 */
	function get_course_type()
	{
		return $this->course_type;
	}

	function get_move_course_url($course)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_COURSE, self :: PARAM_COURSE => $course->get_id()));
    }

	function get_course_type_deleting_url($course_type)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_COURSE_TYPE, self :: PARAM_COURSE_TYPE => $course_type->get_id()));
    }

	function get_course_type_editing_url($course_type)
    {
        //return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE_TYPE => $course_type->get_id(), self :: PARAM_TOOL => 'course_type_settings', 'previous' => 'admin'));
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_COURSE_TYPE_CREATOR, self :: PARAM_COURSE_TYPE => $course_type->get_id(), self :: PARAM_TOOL => 'course_type_settings', 'previous' => 'admin'));
    }

	function get_course_type_maintenance_url($course_type)
    {
        //return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id(), self :: PARAM_TOOL => 'maintenance'));
    	return null;
    }

	function get_course_type_viewing_url($course_type)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE_TYPE, self :: PARAM_COURSE_TYPE => $course_type->get_id()));
    }

	/**
	 * Sets the course_group
	 * @param CourseGroup $course_group
	 */
	function set_course_group($course_group)
	{
		$this->course_group = $course_group;
	}

	/**
	 * Gets a list of all course_groups of the current active course in which the
	 * current user is subscribed.
	 */
	function get_course_groups()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$course_groups = $wdm->retrieve_course_groups_from_user($this->get_user(), $this->get_course())->as_array();
		return $course_groups;
	}

	/**
	 * Makes a category tree ready for displaying by adding a prefix to the
	 * category title based on the level of that category in the tree structure.
	 * @param array $tree The category tree
	 * @param array $categories In this array the new category titles (with
	 * prefix) will be stored. The keys in this array are the category ids, the
	 * values are the new titles
	 * @param int $level The current level in the tree structure
	 */
	private static function translate_category_tree($tree, $categories, $level = 0)
	{
		foreach ($tree as $node)
		{
			$obj = $node['obj'];
			$prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level) . '&mdash; ' : '');
			$categories[$obj->get_id()] = $prefix . $obj->get_title();
			$subtree = $node['sub'];
			if (is_array($subtree) && count($subtree))
			{
				self :: translate_category_tree($subtree, $categories, $level + 1);
			}
		}
	}

	/**
	 * Gets a category
	 * @param int $id The id of the requested category
	 * @return LearningPublicationCategory The requested category
	 */
	function get_category($id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_category($id);
	}

	/**
	 * Displays the header of this application
	 * @param array $breadcrumbs The breadcrumbs which should be displayed
	 */
	function display_header($breadcrumbtrail, $display_search = false, $display_title = true)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}

		$tool = $this->get_parameter(self :: PARAM_TOOL);
		$course = $this->get_parameter(self :: PARAM_COURSE);
		$action = $this->get_parameter(self :: PARAM_ACTION);

		if (isset($this->tool_class))
		{
			$tool = str_replace('_tool', '', Tool :: class_to_type($this->tool_class));
			$js_file = dirname(__FILE__) . '/tool/' . $tool . '/' . $tool . '.js';
			if (file_exists($js_file))
			{
				$htmlHeadXtra[] = '<script type="text/javascript" src="application/lib/weblcms/tool/' . $tool . '/' . $tool . '.js"></script>';
			}
		}

		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50) . '&hellip;';
		}
		Display :: header($breadcrumbtrail);

		if (isset($this->tool_class))
		{
			/*echo '<div style="float: right; margin: 0 0 0.5em 0.5em; padding: 0.5em; border: 1px solid #DDD; background: #FAFAFA;">';
			 echo '<form method="get" action="'.$this->get_url().'" style="display: inline;">';
			 echo '<input type="hidden" name="'.self :: PARAM_ACTION.'" value="courseviewer" />';
			 echo '<input type="hidden" name="'.self :: PARAM_COURSE.'" value="'. $this->get_course_id() .'" />';
			 echo '<input type="hidden" name="'.Application :: PARAM_APPLICATION.'" value="'.$this->get_parameter('application').'"/>';
			 echo '<select name="'.self :: PARAM_TOOL.'" onchange="submit();">';
			 $tools = array ();
			 foreach ($this->get_registered_tools() as $t)
			 {
				$tools[$t->name]['title']	= htmlentities(Translation :: get(Tool :: type_to_class($t->name).'Title'));
				$tools[$t->name]['visible']	= $t->visible;
				$tools[$t->name]['section']	= $t->section;
				}
				asort($tools);
				foreach ($tools as $tool => $properties)
				{
				if (($properties['visible'] && $properties['section'] != 'course_admin') || $this->get_course()->is_course_admin($this->get_user()))
				{
				$class = Tool :: type_to_class($tool);
				echo '<option value="'.$tool.'"'. ($class == $this->tool_class ? ' selected="selected"' : '').'>'.htmlentities($properties['title']).'</option>';
				}
				}
				echo '</select></form></div>';*/
			if ($display_title)
			{
				echo '<div style="float: left;">';
				Display :: tool_title(htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($this->tool_class) . 'Title')));
				echo '</div>';
			}

		}
		else
		{
			if ($course && is_object($this->course) && $action == self :: ACTION_VIEW_COURSE)
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

			//echo '<div class="clear">&nbsp;</div>';
		}

		if (! isset($this->tool_class))
		{
			if ($msg = Request :: get(Application :: PARAM_MESSAGE))
			{
				echo '<br />';
				$this->display_message($msg);
			}
			if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
			{
				echo '<br />';
				$this->display_error_message($msg);
			}
		}

		//echo 'Last visit: '.date('r',$this->get_last_visit_date());
	}

	/**
	 * Displays the footer of this application
	 */
	function display_footer()
	{
		Display :: footer();
	}

	/**
	 * Returns the names of the tools known to this application.
	 * @return array The tools.
	 */
	function get_registered_tools()
	{
		return $this->tools;
	}

	/**
	 * Returns the names of the sections known to this application.
	 * @return array The tools.
	 */
	function get_registered_sections()
	{
		return $this->sections;
	}

	function get_tool_properties($tool)
	{
		return $this->tools[$tool];
	}

	/**
	 * Loads the tools available to the course.
	 */
	function load_tools()
	{
		if (! is_null($this->get_course_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->tools = $wdm->get_course_modules($this->get_course_id());

			foreach ($this->tools as $index => $tool)
			{
				require_once dirname(__FILE__) . '/../tool/' . $tool->name . '/' . $tool->name . '_tool.class.php';
			}
		}
	}

	/**
	 * Loads the sections installed on the system.
	 */
	function load_sections()
	{
		if (! is_null($this->get_course_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$condition = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $this->get_course_id());
			$sections = $wdm->retrieve_course_sections($condition);
			while ($section = $sections->next_result())
			{
				$this->sections[$section->type][] = $section;
			}
		}
	}

	/**
	 * Loads the current course into the system.
	 */
	public function load_course($id = null)
	{
		if($id == null)
			$id = $this->get_parameter(self :: PARAM_COURSE);
		$wdm = WeblcmsDataManager :: get_instance();
		if (! is_null($id) && !is_array($id))
		{
			$this->course = $wdm->retrieve_course($id);
			if(!$this->course)
				$this->redirect(Translation :: get('CourseCorrupt'), true, array('go' => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME),array(),false,Redirect::TYPE_LINK);
		}
		else
		{
			$this->course = $wdm->retrieve_empty_course();
			$this->course->set_course_type($this->course_type);
		}
		$this->load_tools();
		$this->course->set_tools($this->tools);
	}

	/**
	 * Loads the current course_group into the system.
	 */
	private function load_course_group()
	{
		if (! is_null($this->get_parameter(self :: PARAM_COURSE_GROUP)) && strlen($this->get_parameter(self :: PARAM_COURSE_GROUP) > 0))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->course_group = $wdm->retrieve_course_group($this->get_parameter(self :: PARAM_COURSE_GROUP));
		}
	}

	/**
	 * Loads the current course_type into the system.
	 */
	private function load_course_type($id = null)
	{
		$course_type = null;
		if(is_null($id))
			$id = Request :: get(self :: PARAM_COURSE_TYPE);
		$wdm = WeblcmsDataManager :: get_instance();
		if (! is_null($id) && strlen($id) > 0)
		{
			$course_type = $wdm->retrieve_course_type($id);
		}
		else
		{
			$course_type = $wdm->retrieve_empty_course_type();
		}
		return $course_type;
	}

	/**
	 * Determines whether or not the given name is a valid tool name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid tool name, false otherwise.
	 */
	static function is_tool_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}

	/*
	 * Inherited
	 */
	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_max_sort_value($table, $column, $condition);
	}

	/*
	 * Inherited
	 */
	function content_object_is_published($object_id)
	{
		return WeblcmsDataManager :: get_instance()->content_object_is_published($object_id);
	}

	/*
	 * Inherited
	 */
	function any_content_object_is_published($object_ids)
	{
		return WeblcmsDataManager :: get_instance()->any_content_object_is_published($object_ids);
	}

	/*
	 * Inherited
	 */
	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
		return WeblcmsDataManager :: get_instance()->get_content_object_publication_attributes($this->get_user(), $object_id, $type, $offset, $count, $order_property);
	}

	/*
	 * Inherited
	 */
	function get_content_object_publication_attribute($publication_id)
	{
		return WeblcmsDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
	}

	/*
	 * Inherited
	 */
	function delete_content_object_publications($object_id)
	{
		return WeblcmsDataManager :: get_instance()->delete_content_object_publications($object_id);
	}

	function delete_content_object_publication($publication_id)
	{
		return WeblcmsDataManager :: get_instance()->delete_content_object_publication($publication_id);
	}

	/*
	 * Inherited
	 */
	function update_content_object_publication_id($publication_attr)
	{
		return WeblcmsDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
	}

	/**
	 * Inherited
	 */
	function count_publication_attributes($user = null, $object_id = null, $condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
	}

	/**
	 * Inherited
	 */
	function get_content_object_publication_locations($content_object, $user = null)
	{
		$locations = array();

		$type = $content_object->get_type();

		//$courses = $this->retrieve_courses($user->get_id());
		$courses = $this->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user->get_id(), CourseUserRelation :: get_table_name()));
		while ($course = $courses->next_result())
		$c[] = $course;

		$directory = dirname(__FILE__) . '/../tool/';
		$tools = Filesystem :: get_directory_content($directory, Filesystem :: LIST_DIRECTORIES, false);
		foreach ($tools as $tool)
		{
			$path = $directory . $tool . '/' . $tool . '_tool.class.php';

			if (! file_exists($path))
			continue;

			require_once $path;
			$class = Utilities :: underscores_to_camelcase($tool) . 'Tool';
			$obj = new $class($this);
			$types[$tool] = $obj->get_allowed_types();
		}

		foreach ($types as $tool => $allowed_types)
		{
			if (in_array($type, $allowed_types))
			{
				$user = Session :: get_user_id();

				foreach ($c as $course)
				$locations[$course->get_id() . '-' . $tool] = 'Course: ' . $course->get_name() . ' - Tool: ' . $tool;
			}
		}

		return $locations;
	}

	function publish_content_object($content_object, $location, $attributes)
	{
		$location_split = split('-', $location);
		$course = $location_split[0];
		$tool = $location_split[1]; //echo $location;
		$dm = WeblcmsDataManager :: get_instance();
		$do = $dm->get_next_content_object_publication_display_order_index($course, $tool, 0);

		$pub = new ContentObjectPublication();
		$pub->set_content_object_id($content_object->get_id());
		$pub->set_course_id($course);
		$pub->set_tool($tool);
		$pub->set_publisher_id(Session :: get_user_id());
		$pub->set_display_order_index($do);
		$pub->set_publication_date(time());
		$pub->set_modified_date(time());

		$pub->set_hidden($attributes[ContentObjectPublication :: PROPERTY_HIDDEN]);
		if (is_null($pub->is_hidden()))
		$pub->set_hidden(0);

		if ($attributes['forever'] == 0)
		{
			$pub->set_from_date(Utilities :: time_from_datepicker($attributes['from_date']));
			$pub->set_to_date(Utilities :: time_from_datepicker($attributes['to_date']));
		}

		$pub->create();

		$course = $dm->retrieve_course($course);

		return Translation :: get('PublicationCreated') . ': <b>' . Translation :: get('Course') . '</b>: ' . $course->get_name() . ' - <b>' . Translation :: get('Tool') . '</b>: ' . $tool;
	}

	function add_publication_attributes_elements($form)
	{
		$form->addElement('category', Translation :: get('PublicationDetails'));
		$form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . ContentObjectPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		$form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');
		$form->addElement('category');
		$form->addElement('html', '<br />');

		$defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
		$form->setDefaults($defaults);
	}

	/**
	 * Count the number of courses
	 * @param Condition $condition
	 * @return int
	 */
	function count_courses($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_courses($condition);
	}

	function count_course_types($condition = null)
    {
        return WeblcmsDataManager :: get_instance()->count_course_types($condition);
    }

	/**
	 * Count the number of course categories
	 * @param Condition $condition
	 * @return int
	 */
	function count_course_categories($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_course_categories($condition);
	}

	/**
	 * Count the number of courses th user is subscribed to
	 * @param Condition $condition
	 * @return int
	 */
	function count_user_courses($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_user_courses($condition);
	}

	/**
	 * Count the number of course user categories
	 * @param Condition $condition
	 * @return int
	 */
	function count_course_user_categories($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_course_user_categories($condition);
	}

	/**
	 * Retrieves the course categories that match the criteria from persistent storage.
	 * @param string $parent The parent of the course category.
	 * @return DatabaseCourseCategoryResultSet The resultset of course category.
	 */
	function retrieve_course_categories($conditions = null, $offset = null, $count = null, $order_by = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_categories($conditions, $offset, $count, $order_by);
	}

	/**
	 * Retrieves the personal course categories for a given user.
	 * @return DatabaseUserCourseCategoryResultSet The resultset of course categories.
	 */
	function retrieve_course_user_categories($conditions = null, $offset = null, $count = null, $order_property = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_categories($conditions, $offset, $count, $order_property);
	}

	/**
	 * Retrieves a personal course category for the user.
	 * @return CourseUserCategory The course user category.
	 */
	function retrieve_course_user_category($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_category($condition);
	}

	/**
	 * Retrieves a personal course category for the user according to
	 * @param int $user_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserCategory The course user category.
	 */
	function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_category_at_sort($user_id, $sort, $direction);
	}

	/**
	 * Retrieves a single course from persistent storage.
	 * @param string $course_code The alphanumerical identifier of the course.
	 * @return Course The course.
	 */
	function retrieve_course($course_code)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course($course_code);
	}

	function retrieve_course_type($course_type_id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_type($course_type_id);
	}

    function retrieve_course_types($condition = null, $offset = null, $count = null, $order_property = null)
    {
    		return WeblcmsDataManager :: get_instance()->retrieve_course_types($condition, $offset, $count, $order_property);
    }

    function retrieve_active_course_types()
    {
		return WeblcmsDataManager :: get_instance()->retrieve_active_course_types();
    }

    function count_active_course_types()
    {
    	return WeblcmsDataManager :: get_instance()->count_active_course_type();
    }
	/**
	 * Retrieves a single course category from persistent storage.
	 * @param string $category_code The alphanumerical identifier of the course category.
	 * @return CourseCategory The course category.
	 */
	function retrieve_course_category($course_category)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_category($course_category);
	}

	/**
	 * Retrieves a single course user relation from persistent storage.
	 * @param string $course_code
	 * @param int $user_id
	 * @return CourseCategory The course category.
	 */
	function retrieve_course_user_relation($course_code, $user_id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relation($course_code, $user_id);
	}

	/**
	 * Retrieves the next course user relation according to.
	 * @param int $user_id
	 * @param int $category_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserRelationResultSet
	 */
	function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);
	}

	/**
	 * Retrieves a set of course user relations
	 * @param int $user_id
	 * @param string $course_user_category
	 */
	function retrieve_course_user_relations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relations($condition, $offset, $count, $order_property);
	}

	/**
	 * Retrieve a series of courses
	 * @param User $user
	 * @param string $category
	 * @param Condition $condition
	 * @param array $order_by
	 * @param int $offset
	 * @param int $max_objects
	 * @return CourseResultSet
	 */
	function retrieve_courses($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_courses($condition, $offset, $count, $order_property);
	}

	/**
	 * Retrieve a series of courses for a specific user + the relation
	 * @param Condition $condition
	 * @param array $order_by
	 * @param int $offset
	 * @param int $max_objects
	 * @return CourseResultSet
	 */
	function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition, $offset, $count, $order_property);
	}

	/**
	 * Gets the date of the last visit of current user to the current location
	 * @param string $tool If $tool equals null, current active tool will be
	 * taken into account. If no tool is given or no tool is active the date of
	 * last visit to the course homepage will be returned.
	 * @param int $category_id The category in the given tool of which the last
	 * visit date is requested. If $category_id equals null, the current active
	 * category will be used.
	 * @return int
	 */
	function get_last_visit_date($tool = null, $category_id = null)
	{
		if (is_null($tool))
		{
			$tool = $this->get_parameter(self :: PARAM_TOOL);
		}
		if (is_null($category_id))
		{
			$category_id = $this->get_parameter(self :: PARAM_CATEGORY);
			if (is_null($category_id))
			{
				$category_id = 0;
			}
		}
		$wdm = WeblcmsDataManager :: get_instance();
		$date = $wdm->get_last_visit_date($this->get_course_id(), $this->get_user_id(), $tool, $category_id);
		return $date;
	}

	/**
	 * Determines if a tool has new publications  since the last time the
	 * current user visited the tool.
	 * @todo This function now uses the count_content_object_publications
	 * function and for each tool a query is executed. All information can be
	 * retrieved using a single query. WeblcmsDataManager should implement this
	 * functionality.
	 * @todo This function currently doesn't take the course_group information into
	 * account. So it's possible this function returns true even if there's no
	 * new publication for the current user
	 * @param string $tool
	 */
	function tool_has_new_publications($tool)
	{
		$class = Tool :: type_to_class($tool);
		$tool_object = new $class($this);
		if (is_subclass_of($tool_object, 'Tool'))
		{
			$last_visit_date = $this->get_last_visit_date($tool);
			$wdm = WeblcmsDataManager :: get_instance();
			$course_groups = $wdm->retrieve_course_groups_from_user($this->get_user(), $this->get_course())->as_array();

			$conditions = array();
			$conditions[] = new EqualityCondition('tool', $tool);
			$conditions[] = new EqualityCondition('course_id', $this->get_course_id());
			$conditions[] = new InequalityCondition('modified', InequalityCondition :: GREATER_THAN, $last_visit_date);

			if ((! $this->get_course()->is_course_admin($this->get_user()) && ! $this->get_user()->is_platform_admin()))
			{
				// Only select visible publications
				$conditions[] = new EqualityCondition('hidden', 0);
				// Only select publications which are published forever OR
				// of which the current time is in the publication period and the last visit date is before the from_date.
				$conditions_publication_period = array();
				$conditions_publication_period[] = new InequalityCondition('from_date', InequalityCondition :: LESS_THAN_OR_EQUAL, time());
				$conditions_publication_period[] = new InequalityCondition('to_date', InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
				$conditions_publication_period[] = new InequalityCondition('from_date', InequalityCondition :: GREATER_THAN_OR_EQUAL, $last_visit_date);
				$condition_publication_period = new AndCondition($conditions_publication_period);
				$condition_publication_forever = new EqualityCondition('from_date', 0);
				$conditions[] = new OrCondition($condition_publication_forever, $condition_publication_period);
			}

			$user_id = $this->get_user_id();

			$access = array();
			$access[] = new InCondition('user_id', $user_id, $wdm->get_database()->get_alias('content_object_publication_user'));
			$access[] = new InCondition('course_group_id', $course_groups, $wdm->get_database()->get_alias('content_object_publication_course_group'));
			if (! empty($user_id) || ! empty($course_groups))
			{
				$access[] = new AndCondition(array(new EqualityCondition('user_id', null, $wdm->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $wdm->get_database()->get_alias('content_object_publication_course_group'))));
			}

			$conditions[] = new OrCondition($access);
			$condition = new AndCondition($conditions);

			$new_items = $wdm->count_content_object_publications_new($condition);

			return $new_items > 0;
		}

		return false;
	}

	/**
	 * Returns the url to the course's page
	 * @param Course $course
	 * @return String
	 */
	function get_course_viewing_url($course)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id()));
	}

	/**
	 * Returns the link to the course's page
	 * @param Course $course
	 * @return String
	 */
	function get_course_viewing_link($course, $encode = false)
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id()), $encode);
	}

	/**
	 * Returns the editing url for the course
	 * @param Course $course
	 * @return String
	 */
	function get_course_editing_url($course)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_COURSE, self :: PARAM_COURSE => $course->get_id(), self :: PARAM_TOOL => 'course_settings', 'previous' => 'admin'));
	}

	/**
	 * Returns the deleting url for the course
	 * @param Course $course
	 * @return String
	 */
	function get_course_deleting_url($course)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_COURSE, self :: PARAM_COURSE => $course->get_id()));
	}

	/**
	 * Returns the maintenance url for the course
	 * @param Course $course
	 * @return String
	 */
	function get_course_maintenance_url($course)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id(), self :: PARAM_TOOL => 'maintenance'));
	}

	/**
	 * Returns the subscription url for the course
	 * @param Course $course
	 * @return String
	 */
	function get_course_subscription_url($course)
	{
		if(is_null($course->get_settings()))
        	$course = $this->load_course($course->get_id());
        $course = $this->get_course();
		if (! $this->course_subscription_allowed($course))
		{
			return null;
		}

		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SUBSCRIBE, self :: PARAM_COURSE => $course->get_id()));
	}

	/**
	 * Returns the unsubscription url for the course
	 * @param Course $course
	 * @return String
	 */
	function get_course_unsubscription_url($course)
	{
		if (! $this->course_unsubscription_allowed($course))
		{
			return null;
		}

		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_UNSUBSCRIBE, self :: PARAM_COURSE => $course->get_id()));
	}

	/**
	 * Returns the editing url for the course user category
	 * @param CourseUsercategory $course_user_category
	 * @return String
	 */
	function get_course_user_category_edit_url($course_user_category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'edit', self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

	/**
	 * Returns the creating url for a course user category
	 * @return String
	 */
	function get_course_user_category_add_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'add'));
	}

	/**
	 * Returns the moving url for the course user category
	 * @param CourseUserCategory $course_user_category
	 * @param string $direction
	 * @return String
	 */
	function get_course_user_category_move_url($course_user_category, $direction)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'movecat', self :: PARAM_DIRECTION => $direction, self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

	/**
	 * Returns the deleting url for the course user category
	 * @param CourseUserCategory $course_user_category
	 * @return String
	 */
	function get_course_user_category_delete_url($course_user_category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'delete', self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

	/**
	 * Returns the editing url for the course category
	 * @param CourseCategory $course_category
	 * @return String
	 */
	function get_course_category_edit_url($coursecategory)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER, self :: PARAM_COMPONENT_ACTION => 'edit', self :: PARAM_COURSE_CATEGORY_ID => $coursecategory->get_code()));
	}

	/**
	 * Returns the creating url for a course category
	 * @return String
	 */
	function get_course_category_add_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER, self :: PARAM_COMPONENT_ACTION => 'add'));
	}

	/**
	 * Returns the deleting url for the course category
	 * @param CourseCategory $course_category
	 * @return String
	 */
	function get_course_category_delete_url($coursecategory)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER, self :: PARAM_COMPONENT_ACTION => 'delete', self :: PARAM_COURSE_CATEGORY_ID => $coursecategory->get_code()));
	}

	/**
	 * Returns the editing url for the course category
	 * @param CourseCategory $course_category
	 * @return String
	 */
	function get_course_user_edit_url($course_user)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'assign', self :: PARAM_COURSE_USER => $course_user->get_id()));
	}

	/**
	 * Returns the moving url for the course user relation
	 * @param CourseUserRelation $course_user
	 * @param string $direction
	 * @return String
	 */
	function get_course_user_move_url($course_user, $direction)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT, self :: PARAM_COMPONENT_ACTION => 'move', self :: PARAM_DIRECTION => $direction, self :: PARAM_COURSE_USER => $course_user->get_id()));
	}

	/**
	 * Checks whether subscription to the course is allowed for the current user
	 * @param Course $course
	 * @return boolean
	 */
	function course_subscription_allowed($course)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->course_subscription_allowed($course, $this->get_user_id());
	}

	/**
	 * Checks whether unsubscription from the course is allowed for the current user
	 * @param Course $course
	 * @return boolean
	 */
	function course_unsubscription_allowed($course)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->course_unsubscription_allowed($course, $this->get_user());
	}

	/**
	 * Checks whether the user is subscribed to the given course
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function is_subscribed($course, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->is_subscribed($course, $user_id);
	}

	/**
	 * Subscribe a user to a course.
	 * @param Course $course
	 * @param int $status
	 * @param int $tutor_id
	 * @param int $user_id
	 * @return boolean
	 */
	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->subscribe_user_to_course($course, $status, $tutor_id, $user_id);
	}

	/**
	 * Unsubscribe a user from a course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function unsubscribe_user_from_course($course, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->unsubscribe_user_from_course($course, $user_id);
	}

	/**
	 * Subscribe a group to a course.
	 * @param Course $course
	 * @param int $group_id
	 * @return boolean
	 */
	function subscribe_group_to_course($course, $group_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->subscribe_group_to_course($course, $group_id);
	}

	/**
	 * Unsubscribe a group from a course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function unsubscribe_group_from_course($course, $group_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->unsubscribe_group_from_course($course, $group_id);
	}

	/**
	 * @todo Clean this up. It's all SortableTable's fault. :-(
	 */
	private function parse_input_from_table()
	{
		$action = $_POST['action'];

		if (isset($action))
		{

			$action = $_POST['action'];

			$selected_course_ids = $_POST[AdminCourseBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty($selected_course_ids))
			{
				$selected_course_ids = array();
			}
			elseif (! is_array($selected_course_ids))
			{
				$selected_course_ids = array($selected_course_ids);
			}

			$selected_user_ids = $_POST[SubscribedUserBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty($selected_user_ids))
			{
				$selected_user_ids = array();
			}
			elseif (! is_array($selected_user_ids))
			{
				$selected_user_ids = array($selected_user_ids);
			}

			$selected_group_ids = $_POST[SubscribeGroupBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty($selected_group_ids))
			{
				$selected_group_ids = array();
			}
			elseif (! is_array($selected_group_ids))
			{
				$selected_group_ids = array($selected_group_ids);
			}

			$selected_course_type_ids = $_POST[AdminCourseTypeBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty($selected_course_type_ids))
			{
				$selected_course_type_ids = array();
			}

			elseif (! is_array($selected_course_type_ids))
			{
				$selected_course_type_ids = array($selected_course_type_ids);
			}

			$selected_course_type_id = $_POST[AdminCourseTypeBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if ($action == 'enable' || $action == 'disable')
            {
                $this->redirect('url', null, null, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CHANGE_ACTIVE, WeblcmsManager :: PARAM_COURSE_TYPE => $selected_course_type_id, WeblcmsManager :: PARAM_TYPE => 'course_type', WeblcmsManager :: PARAM_EXTRA => $action));
            }

			switch ($action)
			{
				case self :: PARAM_REMOVE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_COURSE);
					Request :: set_get(self :: PARAM_COURSE, $selected_course_ids);
					break;

				case self :: PARAM_UNSUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_MANAGER_UNSUBSCRIBE);
					Request :: set_get(self :: PARAM_USERS, $selected_user_ids);
					break;

				case self :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT :
					$this->set_action(self :: ACTION_MANAGER_SUBSCRIBE);
					Request :: set_get(self :: PARAM_USERS, $selected_user_ids);
					Request :: set_get(self :: PARAM_STATUS, 5);
					break;

				case self :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN :
					$this->set_action(self :: ACTION_MANAGER_SUBSCRIBE);
					Request :: set_get(self :: PARAM_USERS, $selected_user_ids);
					Request :: set_get(self :: PARAM_STATUS, 1);
					break;
				case self :: PARAM_SUBSCRIBE_SELECTED_GROUP :
					$this->set_action(self :: ACTION_SUBSCRIBE_GROUP_USERS);
					Request :: set_get(WeblcmsManager :: PARAM_GROUP, $selected_group_ids);
					Request :: set_get(self :: PARAM_STATUS, 1);
					break;
				case self :: PARAM_REMOVE_SELECTED_COURSE_TYPES :
					$this->set_action(self :: ACTION_DELETE_COURSE_TYPE);
					Request :: set_get(self :: PARAM_COURSE_TYPE, $selected_course_type_ids);
					break;
				case self :: PARAM_ACTIVATE_SELECTED_COURSE_TYPES :
					$this->set_action(self :: ACTION_CHANGE_ACTIVATION);
					Request :: set_get(self :: PARAM_COURSE_TYPE, $selected_course_type_ids);
					Request :: set_get(self :: PARAM_ACTIVE, 1);
					break;
				case self :: PARAM_DEACTIVATE_SELECTED_COURSE_TYPES :
					$this->set_action(self :: ACTION_CHANGE_ACTIVATION);
					Request :: set_get(self :: PARAM_COURSE_TYPE, $selected_course_type_ids);
					Request :: set_get(self :: PARAM_ACTIVE, 0);
					break;
				case self :: PARAM_MOVE_SELECTED_COURSES :
					$this->set_action(self :: ACTION_MOVE_COURSE);
					Request :: set_get(self :: PARAM_COURSE, $selected_course_ids);
					break;
			}
		}
	}

	/**
	 * Gets the search form.
	 * @return RepositorySearchForm The search form.
	 */
	private function get_search_form()
	{
		if (! isset($this->search_form))
		{
			$this->search_form = new WeblcmsSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	/**
	 * Gets the search condition
	 * @return Condition
	 */
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}

	/**
	 * Returns whether the search form has validated
	 * @return boolean
	 */
	function get_search_validate()
	{
		return $this->get_search_form()->validate();
	}

	/**
	 * Gets the search parameter
	 * @param string $name
	 * @return string
	 */
	function get_search_parameter($name)
	{
		return $this->search_parameters[$name];
	}

	/**
	 * Displays the search form
	 */
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	/**
	 * Returns a list of actions available to the admin.
	 * @param User $user The current user.
	 * @return Array $info Contains all possible actions.
	 */
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('CourseTypeList'), 'description' => Translation :: get('CourseTypeListDescription'), 'action' => 'list', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER)));
		$links[] = array('name' => Translation :: get('CreateType'), 'description' => Translation :: get('CreateTypeDescription'), 'action' => 'add', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR)));
		$links[] = array('name' => Translation :: get('List'), 'description' => Translation :: get('ListDescription'), 'action' => 'list', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER)));
		$links[] = array('name' => Translation :: get('Create'), 'description' => Translation :: get('CreateDescription'), 'action' => 'add', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)));
		$links[] = array('name' => Translation :: get('Import'), 'description' => Translation :: get('ImportDescription'), 'action' => 'import', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSES)));
		$links[] = array('name' => Translation :: get('CourseCategoryManagement'), 'description' => Translation :: get('CourseCategoryManagementDescription'), 'action' => 'category', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER)));
		$links[] = array('name' => Translation :: get('UserImport'), 'description' => Translation :: get('UserImportDescription'), 'action' => 'import', 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSE_USERS)));

		$info = parent :: get_application_platform_admin_links();
		$info['links'] = $links;
		$info['search'] = $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER));
		return $info;
	}

	/**
	 * Gets the available links to display in the platform admin
	 * @retun array of links and actions
	 */
	public function get_application_platform_import_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('ImportCourses'), 'description' => Translation :: get('ImportCoursesDescription'), 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSES)));
		$links[] = array('name' => Translation :: get('ImportCourseUsers'), 'description' => Translation :: get('ImportCourseUsersDescription'), 'url' => $this->get_link(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSE_USERS)));

		return $links;
	}

	function get_reporting_url($params)
	{
		$array = array(Application::PARAM_APPLICATION => self :: APPLICATION_NAME, self :: PARAM_TOOL => null, self :: PARAM_ACTION => self :: ACTION_REPORTING);
		$array = array_merge($array, $params);
		return $this->get_url($array);
	}

	/**
	 * Helper function for the Application class,
	 * pending access to class constants via variables in PHP 5.3
	 * e.g. $name = $class :: APPLICATION_NAME
	 *
	 * DO NOT USE IN THIS APPLICATION'S CONTEXT
	 * Instead use:
	 * - self :: APPLICATION_NAME in the context of this class
	 * - YourApplicationManager :: APPLICATION_NAME in all other application classes
	 */
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	/**
	 * Returns html of additional user information for the user view
	 *
	 * @param User $user
	 */
	function get_additional_user_information($user)
	{
		$html = array();

		$table = new Html_Table(array('class' => 'data_table'));

		$table->setHeaderContents(0, 0, Translation :: get('Courses'));
		$table->setCellAttributes(0, 0, array('colspan' => 2, 'style' => 'text-align: center;'));

		$table->setHeaderContents(1, 0, Translation :: get('CourseCode'));
		$table->setHeaderContents(1, 1, Translation :: get('CourseName'));

		$courses = $this->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user->get_id(), CourseUserRelation :: get_table_name()));

		if($courses->size() == 0)
		{
			$table->setCellContents(2, 0, Translation :: get('NoCourses'));
			$table->setCellAttributes(2, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
		}

		$i = 2;

		while($course = $courses->next_result())
		{
			$url = '<a href="' . $this->get_course_viewing_link($course) . '">';
			$table->setCellContents($i, 0, $url . $course->get_visual() . '</a>');
			$table->setCellAttributes($i, 0, array('style' => 'width: 150px;'));
			$table->setCellContents($i, 1, $url . $course->get_name() . '</a>');
			$i++;
		}

		$table->altRowAttributes(1, array('class' => 'row_odd'), array('class' => 'row_even'), true);

		$html[] = $table->toHtml();

		return implode("\n", $html);
	}

}
?>