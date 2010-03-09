<?php
/**
 * $Id: course_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_admin_path() . 'settings/settings_admin_connector.class.php';
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';
require_once dirname(__FILE__) . '/course_layout.class.php';

class CourseForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    const COURSE_VISIBILITY_OPEN_WORLD = 0;
    const COURSE_VISIBILITY_OPEN_PLATFORM = 1;
    const COURSE_VISIBILITY_REGISTERED = 2;
    const COURSE_VISIBILITY_CLOSED = 3;
    const COURSE_VISIBILITY_MODIFIED = 4;

    private $parent;
    private $course;
    private $user;
    private $form_type;
    
    function CourseForm($form_type, $course, $user, $action)
    {
        parent :: __construct('course_settings', 'post', $action);
        
        $this->course = $course;
        $this->user = $user;
        
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }
    
    private $categories;
    private $level = 1;

    function get_categories($parent_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $categories = $wdm->retrieve_course_categories(new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $parent_id));
        
        while ($category = $categories->next_result())
        {
            $this->categories[$category->get_id()] = str_repeat('--', $this->level) . ' ' . $category->get_name();
            $this->level ++;
            $this->get_categories($category->get_id());
            $this->level --;
        }
    }

    function build_basic_form()
    {
        $user_options = array();
            
        $udm = UserDataManager :: get_instance();
         
        if ($this->form_type == self :: TYPE_CREATE)
        {
 	       $users = $udm->retrieve_users(new EqualityCondition(User :: PROPERTY_STATUS, 1));
           while ($userobject = $users->next_result())
           {
	           $user_options[$userobject->get_id()] = $userobject->get_lastname() . '&nbsp;' . $userobject->get_firstname();
           }
        }
        else
        {
        	$wdm = WeblcmsDataManager :: get_instance();
                
            $user_conditions = array();
            $user_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->course->get_id());
            $user_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
            $user_condition = new AndCondition($user_conditions);
                
            $users = $wdm->retrieve_course_user_relations($user_condition);
                
            while ($user = $users->next_result())
            {
            	$userobject = $udm->retrieve_user($user->get_user());
                $user_options[$userobject->get_id()] = $userobject->get_lastname() . '&nbsp;' . $userobject->get_firstname();
            }
        }
        
        $this->addElement('text', Course :: PROPERTY_NAME, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(Course :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
                
       	$this->addElement('select', Course :: PROPERTY_TITULAR, Translation :: get('Teacher'), $user_options);
        $this->addRule(Course :: PROPERTY_TITULAR, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', Course :: PROPERTY_VISUAL, Translation :: get('VisualCode'), array("size" => "50"));
        $this->addRule(Course :: PROPERTY_VISUAL, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Optional') . '</span>');
        
        $cat_options = array();
        $parent = $this->parent;
        
        $this->get_categories(0);
        
        $this->addElement('select', Course :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->categories);
        
        if (PlatformSetting :: get('allow_course_language_selection', WeblcmsManager :: APPLICATION_NAME))
        {
            $adm = AdminDataManager :: get_instance();
            $lang_options = $adm->get_languages();
            $this->addElement('select', Course :: PROPERTY_LANGUAGE, Translation :: get('Language'), $lang_options);
        }
        
        if (PlatformSetting :: get('allow_course_access_selection', WeblcmsManager :: APPLICATION_NAME))
        {
            $course_access = array();
            $course_access[] = & $this->createElement('radio', null, null, Translation :: get('CourseAccessOpenWorld'), self :: COURSE_VISIBILITY_OPEN_WORLD);
            $course_access[] = & $this->createElement('radio', null, null, Translation :: get('CourseAccessOpenRegistered'), self :: COURSE_VISIBILITY_OPEN_PLATFORM);
            $course_access[] = & $this->createElement('radio', null, null, Translation :: get('CourseAccessPrivate'), self :: COURSE_VISIBILITY_REGISTERED);
            $course_access[] = & $this->createElement('radio', null, null, Translation :: get('CourseAccessClosed'), self :: COURSE_VISIBILITY_CLOSED);
            $course_access[] = & $this->createElement('radio', null, null, Translation :: get('CourseAccessModified'), self :: COURSE_VISIBILITY_MODIFIED);
            $this->addGroup($course_access, Course :: PROPERTY_VISIBILITY, Translation :: get('CourseAccess'), '<br />');
        }
        
        if (PlatformSetting :: get('allow_subscribe_selection', WeblcmsManager :: APPLICATION_NAME))
        {
            $subscribe_allowed = array();
            $subscribe_allowed[] = & $this->createElement('radio', null, null, Translation :: get('SubscribeAllowed'), 1);
            $subscribe_allowed[] = & $this->createElement('radio', null, null, Translation :: get('SubscribeNotAllowed'), 0);
            $this->addGroup($subscribe_allowed, Course :: PROPERTY_SUBSCRIBE_ALLOWED, Translation :: get('Subscribe'), '<br />');
        }
        
        if (PlatformSetting :: get('allow_unsubscribe_selection', WeblcmsManager :: APPLICATION_NAME))
        {
            $unsubscribe_allowed = array();
            $unsubscribe_allowed[] = & $this->createElement('radio', null, null, Translation :: get('UnsubscribeAllowed'), 1);
            $unsubscribe_allowed[] = & $this->createElement('radio', null, null, Translation :: get('UnsubscribeNotAllowed'), 0);
            $this->addGroup($unsubscribe_allowed, Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, Translation :: get('Unsubscribe'), '<br />');
        }
        
        if (PlatformSetting :: get('allow_feedback_selection', WeblcmsManager :: APPLICATION_NAME))
        {
            $feedback_allowed = array();
            $feedback_allowed[] = & $this->createElement('radio', null, null, Translation :: get('Yes'), 1);
            $feedback_allowed[] = & $this->createElement('radio', null, null, Translation :: get('No'), 0);
            $this->addGroup($feedback_allowed, CourseLayout :: PROPERTY_ALLOW_FEEDBACK, Translation :: get('AllowFeedback'), '<br />');
        
        }
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        if ($this->course->is_layout_configurable())
        {
            $this->addElement('html', '<div class="configuration_form">');
            $this->addElement('html', '<span class="category">' . Translation :: get('Layout') . '</span>');
            
            $course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
            
            if ($course_can_have_theme)
            {
                $theme_options = array();
                $theme_options[''] = '-- ' . Translation :: get('PlatformDefault') . ' --';
                $theme_options = array_merge($theme_options, Theme :: get_themes());
                $this->addElement('select', Course :: PROPERTY_THEME, Translation :: get('Theme'), $theme_options);
            }
            
            if (PlatformSetting :: get('allow_course_layout_selection', WeblcmsManager :: APPLICATION_NAME))
            {
                $this->addElement('select', CourseLayout :: PROPERTY_LAYOUT, Translation :: get('Layout'), CourseLayout :: get_layouts());
            }
            
            if (PlatformSetting :: get('allow_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME))
            {
                $this->addElement('select', Course :: PROPERTY_TOOL_SHORTCUT, Translation :: get('ToolShortcut'), Course :: get_tool_shortcut_options());
            }
            
            if (PlatformSetting :: get('allow_course_menu_selection', WeblcmsManager :: APPLICATION_NAME))
            {
                $this->addElement('select', Course :: PROPERTY_MENU, Translation :: get('Menu'), Course :: get_menu_options());
            }
            
            if (PlatformSetting :: get('allow_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME))
            {
                $this->addElement('select', Course :: PROPERTY_BREADCRUMB, Translation :: get('Breadcrumb'), Course :: get_breadcrumb_options());
            }
            
            $this->addElement('html', '<div style="clear: both;"></div>');
            $this->addElement('html', '</div>');
        }
    }

    function build_editing_form()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('General') . '</span>');
        
        $this->build_basic_form();
        
        $this->addElement('hidden', Course :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('General') . '</span>');
        
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_course()
    {
        $course = $this->course;
        $values = $this->exportValues();
        
        $course->set_visual($values[Course :: PROPERTY_VISUAL]);
        $course->set_name($values[Course :: PROPERTY_NAME]);
        $course->set_category($values[Course :: PROPERTY_CATEGORY]);
        
        $course->set_titular($values[Course :: PROPERTY_TITULAR]);
        $course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
        $course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
        
        $course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
        if ($course_can_have_theme)
        {
            $course->set_theme($values[Course :: PROPERTY_THEME]);
        }
        
        $language = $values[Course :: PROPERTY_LANGUAGE];
        $course->set_language($language ? $language : PlatformSetting :: get('platform_language'));
        
        $layout = $values[Course :: PROPERTY_LAYOUT];
        $course->set_layout($layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME));
        
        $tool_shortcut = $values[Course :: PROPERTY_TOOL_SHORTCUT];
        $course->set_tool_shortcut($tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME));
        
        $menu = $values[Course :: PROPERTY_MENU];
        $course->set_menu($menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME));
        
        $breadcrumb = $values[Course :: PROPERTY_BREADCRUMB];
        $course->set_breadcrumb($breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME));
        
        $allow_feedback = $values[Course :: PROPERTY_ALLOW_FEEDBACK];
        $course->set_allow_feedback($allow_feedback ? $allow_feedback : PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME));

        $course->set_visibility($values[Course :: PROPERTY_VISIBILITY]);
        $course->set_subscribe_allowed($values[Course :: PROPERTY_SUBSCRIBE_ALLOWED]);
        $course->set_unsubscribe_allowed($values[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED]);
        
        return $course->update();
    }

    function create_course()
    {
        $course = $this->course;
        $values = $this->exportValues();
        
        $course->set_id($values[Course :: PROPERTY_ID]);
        $course->set_visual($values[Course :: PROPERTY_VISUAL]);
        $course->set_name($values[Course :: PROPERTY_NAME]);
        $course->set_category($values[Course :: PROPERTY_CATEGORY]);
        $course->set_titular($values[Course :: PROPERTY_TITULAR]);
        $course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
        $course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
        
        $course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
        if ($course_can_have_theme)
        {
            $course->set_theme($values[Course :: PROPERTY_THEME]);
        }
        
        $course->set_visibility($values[Course :: PROPERTY_VISIBILITY]);
        $course->set_subscribe_allowed($values[Course :: PROPERTY_SUBSCRIBE_ALLOWED]);
        $course->set_unsubscribe_allowed($values[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED]);
        
        $language = $values[Course :: PROPERTY_LANGUAGE];
        $course->set_language($language ? $language : PlatformSetting :: get('platform_language'));
        
        $layout = $values[Course :: PROPERTY_LAYOUT];
        $course->set_layout($layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME));
        
        $tool_shortcut = $values[Course :: PROPERTY_TOOL_SHORTCUT];
        $course->set_tool_shortcut($tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME));
        
        $menu = $values[Course :: PROPERTY_MENU];
        $course->set_menu($menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME));
        
        $breadcrumb = $values[Course :: PROPERTY_BREADCRUMB];
        $course->set_breadcrumb($breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME));
        
        $allow_feedback = $values[Course :: PROPERTY_ALLOW_FEEDBACK];
        $course->set_allow_feedback($allow_feedback ? $allow_feedback : PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME));
        
        if ($course->create())
        {
            // TODO: Temporary function pending revamped roles&rights system
            //add_course_role_right_location_values($course->get_id());
            

            $wdm = WeblcmsDataManager :: get_instance();
            if (! $this->user->is_platform_admin())
            {
                $user_id = $this->user->get_id();
            }
            else
            {
                $user_id = $values[Course :: PROPERTY_TITULAR];
            }
            
            if ($wdm->subscribe_user_to_course($course, '1', '1', $user_id))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $course = $this->course;
        $defaults[Course :: PROPERTY_VISUAL] = $course->get_visual();
        $defaults[Course :: PROPERTY_TITULAR] = $course->get_titular();
        $defaults[Course :: PROPERTY_NAME] = $course->get_name();
        $defaults[Course :: PROPERTY_CATEGORY] = $course->get_category();
        $defaults[Course :: PROPERTY_EXTLINK_NAME] = $course->get_extlink_name();
        $defaults[Course :: PROPERTY_EXTLINK_URL] = $course->get_extlink_url();
        $defaults[Course :: PROPERTY_LANGUAGE] = $course->get_language();
        $defaults[Course :: PROPERTY_VISIBILITY] = $course->get_visibility();
        $defaults[Course :: PROPERTY_SUBSCRIBE_ALLOWED] = $course->get_subscribe_allowed();
        $defaults[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED] = $course->get_unsubscribe_allowed();
        
        $layout = $course->get_layout();
        $defaults[Course :: PROPERTY_LAYOUT] = $layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME);
        
        $tool_shortcut = $course->get_tool_shortcut();
        $defaults[Course :: PROPERTY_TOOL_SHORTCUT] = $tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME);
        
        $menu = $course->get_menu();
        $defaults[Course :: PROPERTY_MENU] = $menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME);
        
        $breadcrumb = $course->get_breadcrumb();
        $defaults[Course :: PROPERTY_BREADCRUMB] = $breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME);
        
        $feedback = $course->get_allow_feedback();
        $defaults[Course :: PROPERTY_ALLOW_FEEDBACK] = $feedback ? $feedback : PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME);
        
        $course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
        
        if ($course_can_have_theme)
        {
            $defaults[Course :: PROPERTY_THEME] = $course->get_theme();
        }
        
        parent :: setDefaults($defaults);
    }
}
?>