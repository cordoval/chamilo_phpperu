<?php
/**
 * $Id: course_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_admin_path() . 'settings/settings_admin_connector.class.php';
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

class CourseForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
   	const UNLIMITED_MEMBERS = 'unlimited_members';

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
        $this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/course_form.js'));
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
    	$tabs = Array(new FormValidatorTab('build_general_settings_form','General'),
		new FormValidatorTab('build_rights_form', 'Rights'),
		new FormValidatorTab('build_layout_form', 'Layout'));
		$selected_tab = 0;
		$this->add_tabs($tabs, $selected_tab);
    }

    function build_general_settings_form()
    {
        $user_options = array();
            
        $udm = UserDataManager :: get_instance();
        $wdm = WeblcmsDataManager :: get_instance();
         
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
        
        $this->addElement('category', Translation :: get('CourseSettings'));

       	$this->addElement('static', 'course_type', Translation :: get('CourseType'), $this->course->get_course_type()->get_name());
        
        $this->addElement('text', Course :: PROPERTY_NAME, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(Course :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
                
       	$this->addElement('select', Course :: PROPERTY_TITULAR, Translation :: get('Teacher'), $user_options);
        $this->addRule(Course :: PROPERTY_TITULAR, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', Course :: PROPERTY_VISUAL, Translation :: get('VisualCode'), array("size" => "50"));
        $this->addRule(Course :: PROPERTY_VISUAL, Translation :: get('ThisFieldIsRequired'), 'required');
		
        $this->get_categories(0);
        $this->addElement('select', Course :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->categories);
        
        $this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, Translation :: get('Extlink_name'), array("size" => "50"));
        $this->addElement('text', Course :: PROPERTY_EXTLINK_URL, Translation :: get('Extlink_url'), array("size" => "50"));
		
        $adm = AdminDataManager :: get_instance();
		$lang = $adm->retrieve_language_from_english_name($this->course->get_course_type()->get_settings()->get_language())->get_original_name();

		$adm = AdminDataManager :: get_instance();
		$lang_options = $adm->get_languages();
		
		$language_disabled = $this->course->get_language_fixed();
		if($language_disabled)
		{
			$this->addElement('static', 'static_language', Translation :: get('CourseTypeLanguage'), $lang);
			$this->addElement('hidden', CourseSettings :: PROPERTY_LANGUAGE);
		}
		else
			$this->addElement('select', CourseSettings :: PROPERTY_LANGUAGE, Translation :: get('CourseTypeLanguage'), $lang_options);
		
		$visibility_disabled = $this->course->get_visibility_fixed();
		$attr_array = array();
		if($visibility_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseSettings :: PROPERTY_VISIBILITY, Translation :: get('CourseTypeVisibility'), '', $attr_array);
		
		$access_disabled = $this->course->get_access_fixed();
		$attr_array = array();
		if($access_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseSettings :: PROPERTY_ACCESS, Translation :: get('CourseTypeAccess'), '', $attr_array);
		
		$members_disabled = $this->course->get_max_number_of_members_fixed();
		$max = "Unlimited";
		if($this->course->get_course_type()->get_settings()->get_max_number_of_members()>0) $max = $this->course->get_course_type()->get_settings()->get_max_number_of_members();
		if($members_disabled)
		{
			$this->addElement('static', 'static_member', Translation :: get('MaximumNumberOfMembers'), $max);
			$this->addElement('hidden', CourseSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS );
		}
		else
		{
	        $choices = array();
	        $choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Unlimited'), 1, array('onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_MEMBERS . '_window\')', 'id' => self :: UNLIMITED_MEMBERS));
	        $choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Limited'), 0, array('onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_MEMBERS . '_window\')'));
	        $this->addGroup($choices, null, Translation :: get('MaximumNumberOfMembers'), '<br />', false);
	        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_MEMBERS . '_window">');
	        $this->add_textfield(CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS, null, false);
	        $this->addElement('html', '</div>');
		}		
		$this->addElement('category');
		
		$this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var " . self :: UNLIMITED_MEMBERS . " = document.getElementById('" . self :: UNLIMITED_MEMBERS . "');
					if (" . self :: UNLIMITED_MEMBERS . ".checked)
					{
						window_hide('" . self :: UNLIMITED_MEMBERS . "_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }

	function build_layout_form()
	{
		$this->addElement('category', Translation :: get('Layout'));
		$this->addElement('select', CourseLayout :: PROPERTY_LAYOUT, Translation :: get('Layout'), CourseLayout :: get_layouts());
		$this->addElement('select', CourseLayout :: PROPERTY_TOOL_SHORTCUT, Translation :: get('ToolShortcut'), CourseLayout :: get_tool_shortcut_options());
		$this->addElement('select', CourseLayout :: PROPERTY_MENU, Translation :: get('Menu'), CourseLayout :: get_menu_options());
		$this->addElement('select', CourseLayout :: PROPERTY_BREADCRUMB, Translation :: get('Breadcrumb'), CourseLayout :: get_breadcrumb_options());
		$this->addElement('category');
		
		$this->addElement('category', Translation :: get('Functionality'));
		if (PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME))
		{
			$feedback_disabled = $this->course->get_feedback_fixed();
			$attr_array = array();
			if($feedback_disabled)
				$attr_array = array('disabled' => 'disabled');
			$this->addElement('checkbox', CourseLayout :: PROPERTY_FEEDBACK, Translation :: get('Feedback'), '', $attr_array);
		}
		
		$intro_tex_disabled = $this->course->get_intro_text_fixed();
		$attr_array = array();
		if($intro_tex_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_INTRO_TEXT, Translation :: get('IntroductionToolTitle'), '', $attr_array);

		$student_view_disabled = $this->course->get_student_view_fixed();
		$attr_array = array();
		if($student_view_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_STUDENT_VIEW, Translation :: get('StudentView'), '', $attr_array);
			
		$course_code_visible_disabled = $this->course->get_course_code_visible_fixed();
		$attr_array = array();
		if($course_code_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE, Translation :: get('CourseCodeTitleVisible'), '', $attr_array);

		$course_manager_name_visible_disabled = $this->course->get_course_manager_name_visible_fixed();
		$attr_array = array();
		if($course_manager_name_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE, Translation :: get('CourseManagerNameTitleVisible'), '', $attr_array);

		$course_languages_visible_disabled = $this->course->get_course_languages_visible_fixed();
		$attr_array = array();
		if($course_languages_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE, Translation :: get('CourseLanguageVisible'), '', $attr_array);
		$this->addElement('category');
		//$this->addElement('html', '<div style="clear: both;"></div>');
			
		//$this->addElement('html', '</div>')
			
		//$this->addElement('html', '<div style="clear: both;"></div>');
		//$this->addElement('html', '</div>');
	}
    
    function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', Course :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function save_course()
	{
		switch($this->form_type)
		{
			case self::TYPE_CREATE: return $this->create_course();
									break;
			case self::TYPE_EDIT: return $this->update_course();
								  break;
		}
	}
	
    function update_course()
    {
        $course = $this->fill_course_general_settings();
        
    	if(!$course->update())
		{
			return false;
		}

		$course_settings = $this->fill_course_settings();
		
		if (!$course_settings->update())
		{
			return false;
		}
		
		$course_layout = $this->fill_course_layout();
		if(!$course_layout->update())
			return false;
		
		return true;
    }

    function create_course()
    {
        $course = $this->fill_course_general_settings();
        
    	if(!$course->create())
			return false;

		$course_settings = $this->fill_course_settings();
		
		if (!$course_settings->create())
			return false;
		
		$course_layout = $this->fill_course_layout();
		
		if(!$course_layout->create())
			return false;
			
        $wdm = WeblcmsDataManager :: get_instance();
        if (! $this->user->is_platform_admin())
            $user_id = $this->user->get_id();
        else
            $user_id = $course->get_titular();
            
        if ($wdm->subscribe_user_to_course($course, '1', '1', $user_id))
            return true;
        else
            return false;
    }

    function fill_course_general_settings()
    {
    	$course = $this->course;
		$values = $this->exportValues();
    	//$course->set_id($values[Course :: PROPERTY_ID]);
        $course->set_visual($values[Course :: PROPERTY_VISUAL]);
        $course->set_name($values[Course :: PROPERTY_NAME]);
        $course->set_category($values[Course :: PROPERTY_CATEGORY]);
        $course->set_titular($values[Course :: PROPERTY_TITULAR]);
        $course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
        $course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
        return $course;
    }
    
	function fill_course_settings()
	{
		$course = $this->course;
		$values = $this->exportValues();
		$course_settings = $course->get_settings();
		$course_settings->set_course_id($course->get_id());
		$course_settings->set_language($values[CourseTypeSettings :: PROPERTY_LANGUAGE]);
		$course_settings->set_visibility($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY]));
		$course_settings->set_access($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS]));
		if($values[self::UNLIMITED_MEMBERS])
			$members = 0;
		else
			$members = $values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS];
		$course_settings->set_max_number_of_members($members);
		return $course_settings;
	}
    
	function fill_course_layout()
	{
		$course = $this->course;
		$values = $this->exportValues();
		$course_layout = $course->get_layout_settings();
		$course_layout->set_course_id($this->course->get_id());	
		$course_layout->set_intro_text($values[CourseLayout :: PROPERTY_INTRO_TEXT]);
		$course_layout->set_student_view($values[CourseLayout :: PROPERTY_STUDENT_VIEW]);
		$course_layout->set_layout($values[CourseLayout :: PROPERTY_LAYOUT]);
		$course_layout->set_tool_shortcut($values[CourseLayout :: PROPERTY_TOOL_SHORTCUT]);
		$course_layout->set_menu($values[CourseLayout :: PROPERTY_MENU]);
		$course_layout->set_breadcrumb($values[CourseLayout :: PROPERTY_BREADCRUMB]);
		$course_layout->set_feedback($values[CourseLayout :: PROPERTY_FEEDBACK]);
		$course_layout->set_course_code_visible($values[CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE]);
		$course_layout->set_course_manager_name_visible($values[CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE]);
		$course_layout->set_course_languages_visible($values[CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE]);
		return $course_layout;		
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
        
        $course_settings = $course;
        if(is_null($course->get_id())) $course_settings = $course->get_course_type()->get_settings();
        $defaults[CourseSettings :: PROPERTY_LANGUAGE] = $course_settings->get_language();
		$defaults[CourseSettings :: PROPERTY_VISIBILITY] = $course_settings->get_visibility();
		$defaults[CourseSettings :: PROPERTY_ACCESS] = $course_settings->get_access();
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $course_settings->get_max_number_of_members();
		$defaults[self :: UNLIMITED_MEMBERS] = ($course_settings->get_max_number_of_members() == 0)? 1:0;
		
		$course_layout = $course;
        if(is_null($course->get_id())) $course_layout = $course->get_course_type()->get_layout_settings();
		$defaults[CourseLayout :: PROPERTY_STUDENT_VIEW] = $course_layout->get_student_view();
		$defaults[CourseLayout :: PROPERTY_LAYOUT] = $course_layout->get_layout();
		$defaults[CourseLayout :: PROPERTY_TOOL_SHORTCUT] = $course_layout->get_tool_shortcut();
		$defaults[CourseLayout :: PROPERTY_MENU] = $course_layout->get_menu();
		$defaults[CourseLayout :: PROPERTY_BREADCRUMB] = $course_layout->get_breadcrumb();
		$defaults[CourseLayout :: PROPERTY_FEEDBACK] = $course_layout->get_feedback();
		$defaults[CourseLayout :: PROPERTY_INTRO_TEXT] = $course_layout->get_intro_text();
		$defaults[CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE] = $course_layout->get_course_code_visible();
		$defaults[CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE] = $course_layout->get_course_manager_name_visible();
		$defaults[CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE] = $course_layout->get_course_languages_visible();
        
        parent :: setDefaults($defaults);
    }
    
	/**
	 * Function add_row_elements_required adds a row of small elements e.g. checkbox, text for a small number
	 * @param array $arrayelements
	 */

	function add_row_elements_required($arrayelements)
	{
		$renderer = $this->defaultRenderer();

		$element_template = array();
		$element_template[] = '<div class="row">';
		$element_template[] = '<div class="label" style="width: 64%;">';
		$element_template[] = '{label}<!-- BEGIN required --><span class="form_required"><img src="' . Theme :: get_common_image_path() . 'action_required.png" alt="*" title ="*"/></span> <!-- END required -->';
		$element_template[] = '</div>';
		$element_template[] = '<div class="formw" style="width: 30%;">';
		$element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
		$element_template[] = '<div class="form_feedback"></div></div>';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '</div>';
		$element_template = implode("\n", $element_template);

		foreach($arrayelements as $value)
		{
			$renderer->setElementTemplate($element_template, $value->getName());
		}

		foreach($arrayelements as $index => $value)
		{
			if($index == 0)
				$this->addElement('html', '<div class="row"><div style="width: 28.5%; float: left;">');
			else
				$this->addElement('html', '<div style="width: 20%; float: left;">');
			$this->addElement($value);
			if($value->getType() != 'checkbox' && $value->getName() != CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS)
				$this->addRule($value->getName(), Translation :: get('ThisFieldIsRequired'), 'required');
			$this->addElement('html', '</div>');
				
		}
		$this->addElement('html', '<div class="clear">&nbsp;</div></div>');
	}
	
	function get_form_type()
	{
		return $this->form_type;
	}
}
?>