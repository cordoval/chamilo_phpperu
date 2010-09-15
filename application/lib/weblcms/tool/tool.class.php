<?php
/**
 * $Id: tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool
 */

/**
==============================================================================
 * This is the base class for all tools used in applications.
 *
 * @author Tim De Pauw
==============================================================================
 */
require_once dirname(__file__) . '/../browser/content_object_publication_list_renderer.class.php';
require_once dirname(__file__) . '/../browser/object_publication_table/object_publication_table.class.php';
require_once dirname(__file__) . '/../browser/list_renderer/list_content_object_publication_list_renderer.class.php';

abstract class Tool extends SubManager
{
    const PARAM_ACTION = 'tool_action';
    const PARAM_PUBLICATION_ID = 'publication';
    const PARAM_COMPLEX_ID = 'cid';
    const PARAM_MOVE = 'move';
    const PARAM_VISIBILITY = 'visible';
    const PARAM_OBJECT_ID = 'object_id';
    const PARAM_BROWSER_TYPE = 'browser';
    const PARAM_TEMPLATE_NAME = 'template_name';
    
    const ACTION_BROWSE = 'browser';
    const ACTION_VIEW = 'viewer';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_MOVE_DOWN = 'move_down';
    const ACTION_MOVE_UP = 'move_up';
    const ACTION_MOVE_TO_CATEGORY = 'category_mover';
    const ACTION_PUBLISH_INTRODUCTION = 'introduction_publisher';
    const ACTION_MANAGE_CATEGORIES = 'category_manager';
    const ACTION_VIEW_REPORTING_TEMPLATE = 'reporting_viewer';
    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'complex_builder';
    const ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT = 'complex_display';
    const ACTION_SHOW_PUBLICATION = 'show_publication';
    const ACTION_HIDE_PUBLICATION = 'hide_publication';
    const ACTION_EVALUATE_TOOL_PUBLICATION = 'tool_evaluate';
    const ACTION_EDIT_RIGHTS = 'rights_editor';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
    
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
     * with.
     */
    function Tool($parent)
    {
        parent :: __construct($parent);
        $this->properties = $parent->get_tool_properties($this->get_tool_id());
        
        $this->handle_table_action();
        
        $this->set_action(Request :: get(self :: PARAM_ACTION));
        $this->set_parameter(self :: PARAM_ACTION, $this->get_action());
        
        $this->set_optional_parameters();
    }

    function set_optional_parameters()
    {
        $this->set_parameter(Tool :: PARAM_BROWSER_TYPE, $this->get_browser_type());
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

    function get_browser_type()
    {
        $browser_type = Request :: get(Tool :: PARAM_BROWSER_TYPE);
        
        if ($browser_type && in_array($browser_type, $this->get_available_browser_types()))
        {
            return $browser_type;
        }
        else
        {
            $available_browser_types = $this->get_available_browser_types();
            return $available_browser_types[0];
        }
    }

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        return $browser_types;
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
        return $this->get_parent()->get_tool_id();
    }

    function display_header($visible_tools = null, $show_introduction_text = false)
    {
        if (! $visible_tools)
        {
            $visible_tools = $this->get_visible_tools();
        }
        
        parent :: display_header();
        $this->display_course_menus($visible_tools, $show_introduction_text);
    }

    function display_footer()
    {
        echo '</div>';
        parent :: display_footer();
    }

    function get_visible_tools()
    {
        $tools = array();
        
        foreach ($this->get_parent()->get_registered_tools() as $tool)
        {
            $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition(CourseSection :: PROPERTY_ID, $tool->section));
            $section = $sections->next_result();
            
            if (($tool->visible && $section->get_type() != CourseSection :: TYPE_ADMIN) || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $tools[] = $tool;
            }
        }
        
        return $tools;
    }

    function display_course_menus($tools, $show_introduction_text = false)
    {
        $menu_style = $this->get_course()->get_menu();
        if ($menu_style != CourseLayout :: MENU_OFF && count($tools) > 0)
        {
            $renderer = ToolListRenderer :: factory(ToolListRenderer :: TYPE_MENU, $this, $tools);
            echo $renderer->display();
            echo '<div id="tool_browser_' . ($renderer->display_menu_icons() && ! $renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() . '">';
        }
        else
        {
            echo '<div id="tool_browser">';
        }
        
        $tool_shortcut = $this->get_course()->get_tool_shortcut();
        
        if (($this->get_tool_id() == 'home' && $this->get_course()->get_intro_text() && ! $this->get_introduction_text()) || ($tool_shortcut == CourseLayout :: TOOL_SHORTCUT_ON && count($tools) > 0))
        {
            echo '<div style="border-bottom: 1px dotted #D3D3D3; margin-bottom: 1em; padding-bottom: 2em;">';
            $shortcuts_visible = true;
        }
        
        if ($show_introduction_text)
        {
            $introduction_text = $this->get_introduction_text();
            if (! $introduction_text)
            {
                if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
                {
                    $toolbar = new Toolbar();
                    $toolbar->add_item(new ToolbarItem(Translation :: get('PublishIntroductionText'), null, $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_LABEL));
                    echo '<div style="float: left;">';
                    echo $toolbar->as_html();
                    echo '</div>';
                }
            }
        }
        
        if ($tool_shortcut == CourseLayout :: TOOL_SHORTCUT_ON && count($tools) > 0)
        {
            $renderer = ToolListRenderer :: factory(ToolListRenderer :: TYPE_SHORTCUT, $this, $tools);
            echo '<div style="float:right;">';
            $renderer->display();
            echo '</div>';
        }
        
        if ($shortcuts_visible)
        {
            echo '</div>';
        }
        
        echo '<div class="clear"></div>';
    }

    function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, $succes_message_multiple)
    {
        return $this->get_parent()->get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, $succes_message_multiple);
    }

    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    /**
     * @see WebApplication :: get_course_id()
     */
    function get_course()
    {
        return $this->get_parent()->get_course();
    }

    /**
     * @see WebApplication :: get_course_id()
     */
    function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    /**
     * @see WebApplication :: get_course_groups()
     */
    function get_course_groups()
    {
        return $this->get_parent()->get_course_groups();
    }

    function get_course_group()
    {
        return $this->get_parent()->get_course_group();
    }

    /**
     * Check if the current user has a given right in this tool
     * @param int $right
     * @return boolean True if the current user has the right
     */
    function is_allowed($right, $publication_id = null)
    {
        $studentview = Session :: retrieve('studentview');
        if ($studentview == 1)
        {
            return ($right == WeblcmsRights :: VIEW_RIGHT);
        }
        
        if ($this->get_parent()->is_teacher())
        {
            return true;
        }
        
        if ($publication_id)
        {
            return WeblcmsRights :: is_allowed_in_courses_subtree($right, $publication_id, WeblcmsRights :: TYPE_PUBLICATION, $this->get_course_id());
        }
        else
        {
            $category_id = Request :: get(WeblcmsManager :: PARAM_CATEGORY);
            if ($category_id)
            {
                return WeblcmsRights :: is_allowed_in_courses_subtree($right, $category_id, WeblcmsRights :: TYPE_COURSE_CATEGORY, $this->get_course_id());
            }
            
            if ($this->get_tool_id() == 'home')
            {
                return WeblcmsRights :: is_allowed_in_courses_subtree($right, 0, RightsUtilities :: TYPE_ROOT, $this->get_course_id());
            }
            
            $module_id = WeblcmsDataManager :: get_instance()->retrieve_course_module_by_name($this->get_course_id(), $this->get_tool_id());
            return WeblcmsRights :: is_allowed_in_courses_subtree($right, $module_id->get_id(), WeblcmsRights :: TYPE_COURSE_MODULE, $this->get_course_id());
        }
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
        return $this->get_parent()->get_last_visit_date();
    }

    //    function get_path($path_type)
    //    {
    //        return $this->get_parent()->get_path($path_type);
    //    }
    

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
            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $toolbar = new Toolbar();
                
                $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_UPDATE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), ToolbarItem :: DISPLAY_ICON));
                $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), ToolbarItem :: DISPLAY_ICON, true));
            }
            
            $html[] = '<div class="announcements level_1" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/introduction.png);">';
            $html[] = '<div class="title" style="border-bottom: 1px dotted #D3D3D3; width:100%;">';
            $html[] = $introduction_text->get_content_object()->get_title();
            $html[] = '</div><div class="clear">&nbsp;</div>';
            $html[] = '<div class="description">';
            $html[] = $introduction_text->get_content_object()->get_description();
            $html[] = '</div>';
            if ($toolbar)
            {
                $html[] = $toolbar->as_html() . '<div class="clear"></div>';
            }
            $html[] = '</div>';
            $html[] = '<br />';
        }
        
        return implode("\n", $html);
    }

    function get_introduction_text()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $this->get_tool_id());
        
        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        return $publications->next_result();
    }

    static function get_allowed_types()
    {
        return array();
    }

    function get_access_details_toolbar_item($parent)
    {
        if (Request :: get(self :: PARAM_PUBLICATION_ID))
        {
            $url = $this->get_parent()->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, Tool :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID), ReportingManager :: PARAM_TEMPLATE_NAME => 'publication_detail_reporting_template'));
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

    function get_complex_display_url($pid)
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $pid));
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
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ToolDoesNotExist', array('toolname' => $tool_name)));
        }
        
        require_once $file;
        
        $class = self :: type_to_class($tool_name);
        
        return new $class($parent);
    }

    /**
     * @param string $type
     * @param Application $application
     */
    static function launch($type, $application)
    {
        $file = dirname(__FILE__) . '/' . $type . '/' . $type . '_tool.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ToolTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = self :: type_to_class($type);
        
        parent :: launch($class, $application);
    }

    function convert_content_object_publication_to_calendar_event($publication)
    {
        return $publication;
    }

    function tool_has_new_publications($tool_name, $course)
    {
        return $this->get_parent()->tool_has_new_publications($tool_name, $course);
    }
}
?>