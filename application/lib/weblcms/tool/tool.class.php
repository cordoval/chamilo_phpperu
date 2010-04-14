<?php
/**
 * $Id: tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool
 */
require_once Path :: get_library_path() . 'utilities.class.php';

/**
==============================================================================
 *	This is the base class for all tools used in applications.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class Tool
{
    const PARAM_ACTION = 'tool_action';
    const PARAM_PUBLICATION_ID = 'publication';
    const PARAM_COMPLEX_ID = 'cid';
    const PARAM_MOVE = 'move';
    const PARAM_VISIBILITY = 'visible';
    const PARAM_OBJECT_ID = 'object_id';

    const ACTION_PUBLISH = 'publish';
    const ACTION_EDIT = 'edit';
    const ACTION_EDIT_CLOI = 'edit_cloi';
    const ACTION_EDIT_FEEDBACK = 'edit_feedback';
    const ACTION_CREATE_CLOI = 'create_cloi';
    const ACTION_MOVE_UP = 'move_up';
    const ACTION_MOVE_DOWN = 'move_down';
    const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
    const ACTION_MOVE_SELECTED_TO_CATEGORY = 'move_selected_to_category';
    const ACTION_MOVE = 'move';
    const ACTION_DELETE = 'delete';
    const ACTION_DELETE_CLOI = 'delete_cloi';
    const ACTION_DELETE_FEEDBACK = 'delete_feedback';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_SHOW = 'show';
    const ACTION_HIDE = 'hide';
    const ACTION_PUBLISH_INTRODUCTION = 'publish_introduction';
    const ACTION_PUBLISH_FEEDBACK = 'publish_feedback';
    const ACTION_MANAGE_CATEGORIES = 'managecategories';
    const ACTION_VIEW_ATTACHMENT = 'view_attachment';
    const ACTION_FEEDBACK_CLOI = 'feedback_cloi';
    const ACTION_VIEW_REPORTING_TEMPLATE = 'view_reporting_template';
    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'builder';
    const ACTION_VIEW = 'view';
    const ACTION_EVALUATE_TOOL_PUBLICATION = 'evaluate_tool_publication';

    /**
     * The action of the tool
     */
    private $action;

    /**
     * The application that the tool is associated with.
     * @var WeblcmsManager
     */
    private $parent;

    /**
     * The rights of the current user in this tool
     */
    private $rights;

    /**
     * Constructor.
     * @param Application $parent The application that the tool is associated
     *                            with.
     */
    function Tool($parent)
    {
        $this->parent = $parent;
        $this->properties = $parent->get_tool_properties($this->get_tool_id());
        $this->set_action(isset($_POST[self :: PARAM_ACTION]) ? $_POST[self :: PARAM_ACTION] : Request :: get(self :: PARAM_ACTION));
        $this->set_parameter(self :: PARAM_ACTION, $this->get_action());
        $this->parse_input_from_table();
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']) || isset($_POST['tool_action']))
        {
            $ids = $_POST['pubtbl_id'];

            if (empty($ids))
            {
                $ids = $_POST['publication_table_id'];
                if (empty($ids))
                    $ids = array();
            }
            elseif (! is_array($ids))
            {
                $ids = array($ids);
            }

            $action = ($_POST['tool_action']) ? $_POST['tool_action'] : $_POST['action'];
            switch ($action)
            {
                case self :: ACTION_MOVE_SELECTED_TO_CATEGORY :
                    $this->set_action(self :: ACTION_MOVE_SELECTED_TO_CATEGORY);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, $ids);
                    break;

                case self :: ACTION_DELETE :
                    $this->set_action(self :: ACTION_DELETE);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, $ids);
                    break;

                case self :: ACTION_DELETE_CLOI :
                    $this->set_action(self :: ACTION_DELETE_CLOI);
                    Request :: set_get(self :: PARAM_COMPLEX_ID, $_POST['page_table_id']);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, Request :: get(self :: PARAM_PUBLICATION_ID));
                    break;

                case self :: ACTION_HIDE :
                    $this->set_action(self :: ACTION_HIDE);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, $ids);
                    break;

                case self :: ACTION_SHOW :
                    $this->set_action(self :: ACTION_SHOW);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, $ids);
                    break;
            }
        }
    }

    function set_action($action)
    {
        $this->action = $action;
    }

    function get_action()
    {
        return $this->action;
    }

    /**
     * Runs the tool, performing whatever actions are necessary.
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;

        switch ($action)
        {
            case self :: ACTION_EVALUATE_TOOL_PUBLICATION :
        		$component = ToolComponent :: factory('', 'ToolEvaluate', $this);
        		break;
            case self :: ACTION_EDIT :
                $component = ToolComponent :: factory('', 'Edit', $this);
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = ToolComponent :: factory('', 'IntroductionPublisher', $this);
                break;
            case self :: ACTION_PUBLISH_FEEDBACK :
                $component = ToolComponent :: factory('', 'FeedbackPublisher', $this);
                break;
            case self :: ACTION_FEEDBACK_CLOI :
                $component = ToolComponent :: factory('', 'ComplexFeedback', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = ToolComponent :: factory('', 'CategoryManager', $this);
                break;
            case self :: ACTION_MOVE_UP :
                Request :: set_get(self :: PARAM_MOVE, -1);
                $component = ToolComponent :: factory('', 'Move', $this);
                break;
            case self :: ACTION_MOVE_DOWN :
                Request :: set_get(self :: PARAM_MOVE, 1);
                $component = ToolComponent :: factory('', 'Move', $this);
                break;
            case self :: ACTION_MOVE :
                $component = ToolComponent :: factory('', 'Move', $this);
                break;
            case self :: ACTION_MOVE_TO_CATEGORY :
                $component = ToolComponent :: factory('', 'MoveSelectedToCategory', $this);
                break;
            case self :: ACTION_MOVE_SELECTED_TO_CATEGORY :
                $component = ToolComponent :: factory('', 'MoveSelectedToCategory', $this);
                break;
            case self :: ACTION_DELETE :
                $component = ToolComponent :: factory('', 'Delete', $this);
                break;
            case self :: ACTION_DELETE_CLOI :
                $component = ToolComponent :: factory('', 'ComplexDeleter', $this);
                break;
            case self :: ACTION_DELETE_FEEDBACK :
                $component = ToolComponent :: factory('', 'FeedbackDeleter', $this);
                break;
            case self :: ACTION_EDIT_CLOI :
                $component = ToolComponent :: factory('', 'ComplexEdit', $this);
                break;
            case self :: ACTION_EDIT_FEEDBACK :
                $component = ToolComponent :: factory('', 'FeedbackEdit', $this);
                break;
            case self :: ACTION_CREATE_CLOI :
                $component = ToolComponent :: factory('', 'ComplexCreator', $this);
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = ToolComponent :: factory('', 'ToggleVisibility', $this);
                break;
            case self :: ACTION_SHOW :
                Request :: set_get(PARAM_VISIBILITY, 0);
                $component = ToolComponent :: factory('', 'ToggleVisibility', $this);
                break;
            case self :: ACTION_HIDE :
                Request :: set_get(PARAM_VISIBILITY, 1);
                $component = ToolComponent :: factory('', 'ToggleVisibility', $this);
                break;
            case self :: ACTION_VIEW_ATTACHMENT :
                $component = ToolComponent :: factory('', 'AttachmentViewer', $this);
                break;
            case self :: ACTION_VIEW_REPORTING_TEMPLATE :
                $component = ToolComponent :: factory('', 'AccessDetailsViewer', $this);
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT :
                $component = ToolComponent :: factory('', 'ComplexBuilder', $this);
                break;
        }
        if ($component)
        {
            $component->run();
        }

        return $component;
    }

    /**
     * Returns the application that this tool is associated with.
     * @return Application The application.
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * Returns the properties of this tool within the specified course.
     * @return Tool The tool.
     */
    function get_properties()
    {
        return $this->properties;
    }

    /**
     * @see Application :: get_tool_id()
     */
    function get_tool_id()
    {
        return $this->parent->get_tool_id();
    }

    /**
     * @see Application :: display_header()
     */
    function display_header($breadcrumbtrail, $display_title)
    {
        $trail = new BreadcrumbTrail();
        $trail->set_help_items($breadcrumbtrail->get_help_items());
        switch ($this->parent->get_course()->get_breadcrumb())
        {
            case CourseLayout :: BREADCRUMB_TITLE :
                $title = $this->parent->get_course()->get_name();
                break;
            case CourseLayout :: BREADCRUMB_CODE :
                $title = $this->parent->get_course()->get_visual();
                break;
            case CourseLayout :: BREADCRUMB_COURSE_HOME :
                $title = Translation :: get('CourseHome');
                break;
            default :
                $title = $this->parent->get_course()->get_visual();
                break;
        }

        $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'tool' => null, 'course' => null, self :: PARAM_PUBLICATION_ID => null)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(array('tool' => null, 'tool_action' => null, self :: PARAM_PUBLICATION_ID => null)), $title));

        // TODO: do this by overriding display_header in the course_group tool


        if (! is_null($this->parent->get_course_group()))
        {
            $course_group = $this->parent->get_course_group();
            $trail->add(new Breadcrumb($this->get_url(array('tool_action' => null, WeblcmsManager :: PARAM_COURSE_GROUP => null)), Translation :: get('CourseGroups')));
            //if(Request :: get('tool_action') != null)
        //$trail->add(new Breadcrumb($this->get_url(array('tool_action' => 'course_group_unsubscribe')), $course_group->get_name()));
        }
        elseif ($this->get_tool_id() == 'course_group')
        {
            $trail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), Translation :: get(Tool :: type_to_class($this->parent->get_tool_id()) . 'Title')));
        }
        // TODO: make this the default
        if ($this->get_tool_id() != 'course_group')
        {
            $trail->add(new Breadcrumb($this->get_url(array('tool_action' => null, 'pcattree' => null, 'view' => null, 'time' => null, self :: PARAM_PUBLICATION_ID => null)), Translation :: get(Tool :: type_to_class($this->parent->get_tool_id()) . 'Title')));
        }

        $breadcrumbs = $breadcrumbtrail->get_breadcrumbs();

        if (count($breadcrumbs))
        {
            foreach ($breadcrumbs as $i => $breadcrumb)
            {
                if ($i != 0)
                    $trail->add($breadcrumb);
            }
        }
        $this->parent->display_header($trail, false, $display_title);
        //echo '<div class="clear"></div>';


        if ($this->parent->get_course()->get_tool_shortcut() == CourseLayout :: TOOL_SHORTCUT_ON)
        {
            $renderer = ToolListRenderer :: factory('Shortcut', $this->parent);
            echo '<div style="width: 100%; text-align: right;">';
            $renderer->display();
            echo '</div>';
        }

        echo '<div class="clear"></div>';

        if ($msg = Request :: get(Application :: PARAM_MESSAGE))
        {
            $this->parent->display_message($msg);
        }
        if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
        {
            $this->parent->display_error_message($msg);
        }

        $menu_style = $this->parent->get_course()->get_menu();
        if ($menu_style != CourseLayout :: MENU_OFF)
        {
            $renderer = ToolListRenderer :: factory('Menu', $this->parent);
            $renderer->display();
            echo '<div id="tool_browser_' . ($renderer->display_menu_icons() && ! $renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() . '">';
        }
        else
        {
            echo '<div id="tool_browser">';
        }

    }

    /**
     * @see Application :: display_footer()
     */
    function display_footer()
    {
        echo '</div>';
        $this->parent->display_footer();
    }

    function display_error_message($message)
    {
        $this->parent->display_error_message($message);
    }

    /**
     * Informs the user that access to the page was denied.
     */
    function disallow()
    {
        Display :: not_allowed();
    }

    /**
     * @see WebApplication :: get_user()
     */
    function get_user()
    {
        return $this->parent->get_user();
    }

    /**
     * @see WebApplication :: get_user_id()
     */
    function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user_info($user_id)
    {
        return $this->parent->get_user_info($user_id);
    }

    /**
     * @see WebApplication :: get_course_id()
     */
    function get_course()
    {
        return $this->parent->get_course();
    }

    /**
     * @see WebApplication :: get_course_id()
     */
    function get_course_id()
    {
        return $this->parent->get_course_id();
    }

    /**
     * @see WebApplication :: get_course_groups()
     */
    function get_course_groups()
    {
        return $this->parent->get_course_groups();
    }

    function get_course_group()
    {
        return $this->parent->get_course_group();
    }

    /**
     * @see WebApplication :: get_parameters()
     */
    function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    /**
     * @see WebApplication :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->parent->get_parameter($name);
    }

    /**
     * @see WebApplication :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        $this->parent->set_parameter($name, $value);
    }

    /**
     * @see WebApplication :: get_url()
     */

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->parent->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @see WebApplication :: redirect()
     */
    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
    {
        return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities, $type);
    }
    
    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_APPLICATION)
    {
        return $this->get_parent()->simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }
    /**
     * Check if the current user has a given right in this tool
     * @param int $right
     * @return boolean True if the current user has the right
     */
    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    /**
     * Converts a tool name to the corresponding class name.
     * @param string $tool The tool name.
     * @return string The class name.
     */
    static function type_to_class($tool)
    {
        return Utilities :: underscores_to_camelcase($tool) . 'Tool';
    }

    /**
     * Converts a tool class name to the corresponding tool name.
     * @param string $class The class name.
     * @return string The tool name.
     */
    static function class_to_type($class)
    {
        return str_replace('/Tool$/', '', Utilities :: camelcase_to_underscores($class));
    }

    /**
     * @see WeblcmsManager :: get_last_visit_date()
     */
    function get_last_visit_date()
    {
        return $this->parent->get_last_visit_date();
    }

    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /** Dummy functions so we can use the same component class for both tool and repositorytool **/
    function perform_requested_action()
    {
    }

    //	function get_categories($list = false)
    //	{
    //		return $this->get_parent()->get_categories($list);
    //	}


    /**
     * @see Application :: get_category()
     */
    function get_category($id)
    {
        return $this->get_parent()->get_category($id);
    }

    private function build_move_to_category_form($action)
    {
        $form = new FormValidator($action, 'get', $this->get_url());
        $categories = $this->get_categories(true);
        $form->addElement('select', ContentObjectPublication :: PROPERTY_CATEGORY_ID, Translation :: get('Category'), $categories);
        //$form->addElement('submit', 'submit', Translation :: get('Ok'));
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));
        $buttons[] = $form->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $parameters = $this->get_parameters();
        $parameters['pcattree'] = Request :: get('pcattree');
        $parameters[self :: PARAM_ACTION] = $action;
        foreach ($parameters as $key => $value)
        {
            $form->addElement('hidden', $key, $value);
        }
        return $form;
    }

    function display_introduction_text($introduction_text)
    {
        $html = array();

        if ($introduction_text)
        {
            if ($this->is_allowed(EDIT_RIGHT))
            {
                $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

                $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);
            }

            $html[] = '<div class="announcements level_1">';
            $html[] = '<div class="title">';
            $html[] = $introduction_text->get_content_object()->get_title();
            $html[] = '</div><div class="clear">&nbsp;</div>';
            $html[] = '<div class="description">';
            $html[] = $introduction_text->get_content_object()->get_description();
            $html[] = '</div>';
            $html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        return implode("\n", $html);
    }

    static function get_allowed_types()
    {
        return array();
    }

    function get_access_details_toolbar_item($parent)
    {
        if (Request :: get(self :: PARAM_PUBLICATION_ID))
        {
            //Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE,
            $url = $this->parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, Tool :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID), ReportingManager :: PARAM_TEMPLATE_NAME => 'PublicationDetailReportingTemplate'));
            return new ToolbarItem(Translation :: get('AccessDetails'), Theme :: get_common_image_path() . 'action_reporting.png', $url);
        }
        else
        {
            return new ToolbarItem('');
        }
    }

    function get_complex_builder_url($pid)
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $pid));
    }

    static function get_pcattree_parents($pcattree)
    {
        $parent = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($pcattree);
        $parents[] = $parent;

        while ($parent && $parent->get_parent() != 0)
        {
            $parent = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($parent->get_parent());
            $parents[] = $parent;
        }
        $parents = array_reverse($parents);

        return $parents;
    }
    
    static function factory($tool_name, $parent)
    {
    	$file = dirname(__FILE__) . '/' . $tool_name . '/' . $tool_name . '_tool.class.php';
    	if(!file_exists($file))
    	{
    		throw new Exception(Translation :: get('ToolDoesNotExist', array('toolname' => $tool_name)));
    	}
    	
    	require_once $file;
    	
    	$class = self :: type_to_class($tool_name);
    	
    	return new $class($parent);
    }
 	function retrieve_evaluation_ids_by_publication($id)
    {
    	require_once dirname (__FILE__) . '/../../gradebook/evaluation_manager/evaluation_manager.class.php';
    	return EvaluationManager :: retrieve_evaluation_ids_by_publication(self :: APPLICATION_NAME, $id);
    }

    function move_internal_to_external($publication)
    {
    	if(WebApplication :: is_active('gradebook'))
        {
	    	//require_once dirname (__FILE__) . '/../../gradebook/evaluation_manager/evaluation_manager.class.php';
	    	return EvaluationManager :: move_internal_to_external(self :: APPLICATION_NAME, $publication);
        }
    }
 	function get_evaluation_publication_url($tool_publication)
    {
        require_once dirname (__FILE__) . '/../../gradebook/evaluation_manager/evaluation_manager.class.php';
        $parameters[EvaluationManager :: PARAM_PUBLICATION_ID] = $tool_publication->get_id();
        $parameter_string = base64_encode(serialize($parameters));
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EVALUATE_WIKI_PUBLICATION, EvaluationManager :: PARAM_PARAMETERS => $parameter_string));
    }
}
?>