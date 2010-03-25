<?php
/**
 * $Id: course_type_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */

class CourseTypeForm extends FormValidator
{

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	const UNLIMITED_MEMBERS = 'unlimited_members';

	private $parent;
	private $course_type;
	private $form_type;

	function CourseTypeForm($form_type, $course_type, $action, $parent)
	{
		parent :: __construct('course_type_settings', 'post', $action);
		$this->form_type = $form_type;
		$this->course_type = $course_type;
		$this->parent = $parent;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}
		$this->setDefaults();
		$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/viewable_checkbox.js'));
		$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/course_type_form.js'));
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

	function build_basic_form()
	{
		$tabs = array();
		$tabs[] = new FormValidatorTab('build_general_settings_form', 'General');
		$tabs[] = new FormValidatorTab('build_layout_form', 'Layout');
		$tabs[] = new FormValidatorTab('build_tools_form', 'Tools');
		$tabs[] = new FormValidatorTab('build_rights_form', 'RightsManagement');

		$this->add_tabs($tabs, 0);
	}

	function build_rights_form()
	{
		$this->addElement('static', '', 'RightsLabel', 'RightsValue');
	}

	function build_tools_form()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('basic');
		$data = array();

		//Tools defaults
		$course_type_tools = $this->course_type->get_tools();
			
		foreach ($tools as $index => $tool)
		{
			$tool_data = array();
				
			$element_name_arr = array('class'=>'iphone '.$tool);
			$element_default_arr = array('class'=>'viewablecheckbox', 'style'=>'width=80%');
				
			if($this->form_type == self::TYPE_CREATE)
			{
				$element_name_arr['checked'] = "checked";
				$element_default_arr['checked'] = "checked";
			}
			else
			{
				foreach($course_type_tools as $course_type_tool)
				{
					if($tool ==  $course_type_tool->get_name())
					{
						$element_name_arr['checked'] = "checked";
						if($course_type_tool->get_visible_default())
						$element_default_arr['checked'] = "checked";
					}
				}
			}
				
			$tool_image_src = Theme :: get_image_path() . 'tool_mini_' . $tool . '.png';
			$tool_image = $tool . "_image";
			$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool) . 'Title'));
			$element_name = $tool . "element";
			$element_default = $tool . "elementdefault";

			$tool_data[] = '<img class="' . $tool_image .'" src="' . $tool_image_src . '" style="vertical-align: middle;" alt="' . $title . '"/>';
			$tool_data[] = $title;
			$tool_data[] = '<div  style="margin: 0 auto; width: 50px;">'.$this->createElement('checkbox', $element_name, $title, '', $element_name_arr)->toHtml().'</div>';
			$tool_data[] = '<div class="'.$element_default.'"/>'.$this->createElement('checkbox', $element_default, Translation :: get('IsVisible'),'', $element_default_arr)->toHtml().'</div>';
			$count ++;

			$data[] = $tool_data;
		}

		$table = new SortableTableFromArray($data);
		$table->set_header(0, '', false);
		$table->set_header(1, Translation :: get('Tool'), false);
		$table->set_header(2, Translation :: get('IsToolAvailable'), false, null, array('style'=>'width: 35%;'));
		$table->set_header(3, Translation :: get('IsToolVisible'), false, null, array('style'=>'width: 20%; text-align: center;'));
		$this->addElement('html', '<div style="width:70%; margin-left: 15%;">'.$table->as_html().'</div>');

		$this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var image_path = '".Theme :: get_image_path()."';
					var common_image_path = '".Theme :: get_common_image_path()."';
					/* ]]> */
					</script>\n");
	}

	function build_layout_form()
	{
		$this->addElement('category', Translation :: get('Layout'));
		$this->addElement('select', CourseTypeLayout :: PROPERTY_LAYOUT, Translation :: get('Layout'), CourseTypeLayout :: get_layouts());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT, Translation :: get('ToolShortcut'), CourseTypeLayout :: get_tool_shortcut_options());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_MENU, Translation :: get('Menu'), CourseTypeLayout :: get_menu_options());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_BREADCRUMB, Translation :: get('Breadcrumb'), CourseTypeLayout :: get_breadcrumb_options());
		$this->addElement('category');

		$this->addElement('category', Translation :: get('Functionality'));
		if (PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME))
		{
			$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK, Translation :: get('Feedback'));
		}
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT, Translation :: get('IntroductionToolTitle'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW, Translation :: get('StudentView'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE, Translation :: get('CourseCodeTitleVisible'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE, Translation :: get('CourseManagerNameTitleVisible'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE, Translation :: get('CourseLanguageVisible'));
		$this->addElement('category');

		$this->addElement('category', Translation :: get('LockedFunctionality'));
		$this->add_information_message('', '', Translation :: get('LockedFunctionalityLayout'), true);
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_LAYOUT_FIXED, Translation :: get('Layout'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED, Translation :: get('ToolShortcut'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_MENU_FIXED, Translation :: get('Menu'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED, Translation :: get('Breadcrumb'));
	  
		$this->add_information_message('', '', Translation :: get('LockedFunctionalityDescription'), true);
		if (PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME))
		{
			$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED, Translation :: get('Feedback'));
		}
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED, Translation :: get('IntroductionToolTitle'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED, Translation :: get('StudentView'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED, Translation :: get('CourseCodeTitleVisible'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED, Translation :: get('CourseManagerNameTitleVisible'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED, Translation :: get('CourseLanguageVisible'));
		$this->addElement('category');
	}

	function build_general_settings_form()
	{
		// Course type settings
		$this->addElement('category', Translation :: get('CourseTypeOnly'));
		$this->add_textfield(CourseType :: PROPERTY_NAME, Translation :: get('CourseTypeName'));
		$this->add_html_editor(CourseType :: PROPERTY_DESCRIPTION, Translation :: get('CourseTypeDescription'), true, array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup'));
		$this->addElement('checkbox', CourseType :: PROPERTY_ACTIVE, Translation :: get('CourseTypeActive'));
		$this->addElement('category');

		// Course settings
		$this->addElement('category', Translation :: get('CourseTypeDefaultProperties'));

		$adm = AdminDataManager :: get_instance();
		$lang_options = $adm->get_languages();
		$this->addElement('select', CourseTypeSettings :: PROPERTY_LANGUAGE, Translation :: get('CourseTypeLanguage'), $lang_options);

		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY, Translation :: get('CourseTypeVisibility'));

		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_ACCESS, Translation :: get('CourseTypeAccess'));

		// Number of members
		$choices = array();
		$choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Unlimited'), 1, array('onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_MEMBERS . '_window\')', 'id' => self :: UNLIMITED_MEMBERS));
		$choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Limited'), 0, array('onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_MEMBERS . '_window\')'));
		$this->addGroup($choices, 'choices', Translation :: get('MaximumNumberOfMembers'), '<br />', false);
		$this->addElement('html', '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_MEMBERS . '_window">');
		$this->add_textfield(CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS, null, false);
		$this->registerRule('max_members', null, 'HTML_QuickForm_Rule_Max_Members', dirname(__FILE__) .'/max_members.rule.class.php');
		$this->addRule(Array('choices',CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS), Translation :: get('IncorrectNumber'), 'max_members');
		$this->addElement('html', '</div>');

		$this->addElement('category');

		$this->addElement('category', Translation :: get('CourseTypeLockedProperties'));
		$this->add_information_message('', '', Translation :: get('LockedFunctionalityDescription'), true);
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED, Translation :: get('CourseTypeLanguage'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED, Translation :: get('CourseTypeVisibility'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_ACCESS_FIXED, Translation :: get('CourseTypeAccess'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED , Translation :: get('CourseTypeMaxNumberOfMembers'));
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

	function save_course_type()
	{
		switch($this->form_type)
		{
			case self::TYPE_CREATE: return $this->create_course_type();
			break;
			case self::TYPE_EDIT: return $this->update_course_type();
			break;
		}
	}

	function update_course_type()
	{
		$course_type = $this->fill_course_type();

		if(!$this->course_type->update())
		{
			return false;
		}

		$course_type_settings = $this->fill_course_type_settings();

		if (!$course_type_settings->update())
		{
			return false;
		}

		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('basic');
		$selected_tools = $this->fill_course_type_tools($tools);
		$default_tools = $this->course_type->get_tools();

		foreach($selected_tools as $tool)
		{
			$sub_validation = false;
			foreach($default_tools as $index => $default_tool)
			{
				if($tool->get_name() == $default_tool->get_name())
				{
					if(!$tool->update())
					return false;
					$sub_validation = true;
					unset($default_tools[$index]);
					break;
				}
			}
			if(!$sub_validation)
			{
				if(!$tool->create())
				return false;
			}
		}

		foreach($default_tools as $tool)
		{
			if(!$tool->delete())
			return false;
		}

		$course_type_layout = $this->fill_course_type_layout();

		if($course_type_layout->update())
		{
			//update all course related to the coursetype
			$condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());
			$courses = WeblcmsDataManager::get_instance()->retrieve_courses($condition);
			while($course = $courses->next_result())
			{
				$this->parent->get_parent()->load_course($course->get_id());
				$course = $this->parent->get_parent()->get_course();
				
				dump($course);
				
				$course_settings = $this->fill_course_settings($course);
				if(!$course_settings->update())
					return false;
				$course_layout = $this->fill_course_layout($course);
				if(!$course_layout->update())
					return false;
				
				$selected_tools = $this->fill_course_type_tools($tools);
				$course_tools = $course->get_tools();
				$course_modules = array();

				foreach($selected_tools as $tool)
				{
					$sub_validation = false;
					foreach($course_tools as $index => $course_tool)
					{
						if($tool->get_name() == $course_tool->name)
						{
							$sub_validation = true;
							unset($course_tools[$index]);
							break;
						}
					}
					if(!$sub_validation)
					{
						$course_module = new CourseModule();
						$course_module->set_course_code($course->get_id());
						$course_module->set_name($tool->get_name());
						$course_module->set_visible($tool->get_visible_default());
						$course_module->set_section("basic");
						$course_modules[] = $course_module;
					}
				}

				foreach($course_tools as $tool)
				{
					if(!$wdm->delete_course_module($tool->course_id, $tool->name))
						return false;
				}
				
				if(!$wdm->create_course_modules($course_modules, $course->get_id()))
					return false;

			}
			// TODO: Temporary function pending revamped roles&rights system
			//add_course_role_right_location_values($course_type->get_id());
			return true;
		}
		else
		{
			return false;
		}
	}

	function create_course_type()
	{
		$course_type = $this->fill_course_type();

		if(!$this->course_type->create())
		{
			return false;
		}

		$course_type_settings = $this->fill_course_type_settings();

		if (!$course_type_settings->create())
		{
			return false;
		}

		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('basic');
		$selected_tools = $this->fill_course_type_tools($tools);
		$validation = true;

		foreach($selected_tools as $tool)
		{
			if(!$tool->create())
			$validation = false;
		}

		if(!$validation)
		{
			return false;
		}

		$course_type_layout = $this->fill_course_type_layout();

		if($course_type_layout->create())
		{
			// TODO: Temporary function pending revamped roles&rights system
			//add_course_role_right_location_values($course_type->get_id());
			return true;
		}
		else
		{
			return false;
		}
	}

	function fill_course_type()
	{
		$course_type = $this->course_type;
		$values = $this->exportValues();
		//$course_type->set_id($values[CourseType :: PROPERTY_ID]);
		$course_type->set_name($values[CourseType :: PROPERTY_NAME]);
		$course_type->set_description($values[CourseType :: PROPERTY_DESCRIPTION]);
		$course_type->set_active($values[CourseType :: PROPERTY_ACTIVE]);
		return $course_type;
	}

	function fill_course_type_settings()
	{
		$course_type = $this->course_type;
		$values = $this->exportValues();
		$course_type_settings = $course_type->get_settings();
		$course_type_settings->set_course_type_id($course_type->get_id());
		$course_type_settings->set_language($values[CourseTypeSettings :: PROPERTY_LANGUAGE]);
		$course_type_settings->set_language_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED]));
		$course_type_settings->set_visibility($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY]));
		$course_type_settings->set_visibility_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED]));
		$course_type_settings->set_access($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS]));
		$course_type_settings->set_access_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS_FIXED]));
		if($values[self::UNLIMITED_MEMBERS])
		$members = 0;
		else
		$members = $values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS];
		$course_type_settings->set_max_number_of_members($members);
		$course_type_settings->set_max_number_of_members_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED]));
		return $course_type_settings;
	}

	function fill_course_type_layout()
	{
		$course_type = $this->course_type;
		$values = $this->exportValues();
		$course_type_layout = $course_type->get_layout_settings();
		$course_type_layout->set_course_type_id($this->course_type->get_id());
		$course_type_layout->set_intro_text($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT]));
		$course_type_layout->set_intro_text_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED]));
		$course_type_layout->set_student_view($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW]));
		$course_type_layout->set_student_view_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED]));
		$course_type_layout->set_layout($values[CourseTypeLayout :: PROPERTY_LAYOUT]);
		$course_type_layout->set_layout_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_LAYOUT_FIXED]));
		$course_type_layout->set_tool_shortcut($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT]);
		$course_type_layout->set_tool_shortcut_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED]));
		$course_type_layout->set_menu($values[CourseTypeLayout :: PROPERTY_MENU]);
		$course_type_layout->set_menu_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_MENU_FIXED]));
		$course_type_layout->set_breadcrumb($values[CourseTypeLayout :: PROPERTY_BREADCRUMB]);
		$course_type_layout->set_breadcrumb_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED]));
		$course_type_layout->set_feedback($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_FEEDBACK]));
		$course_type_layout->set_feedback_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED]));
		$course_type_layout->set_course_code_visible($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE]));
		$course_type_layout->set_course_code_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED]));
		$course_type_layout->set_course_manager_name_visible($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE]));
		$course_type_layout->set_course_manager_name_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED]));
		$course_type_layout->set_course_languages_visible($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE]));
		$course_type_layout->set_course_languages_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED]));

		return $course_type_layout;
	}

	function fill_course_type_tools($tools)
	{
		$tools_array = array();
		foreach($tools as $tool)
		{
			$element_name = $tool . "element";
			$element_default = $tool . "elementdefault";

			if($this->parse_checkbox_value($this->getSubmitValue($element_name)))
			{
				$course_type_tool = new CourseTypeTool();
				$course_type_tool->set_course_type_id($this->course_type->get_id());
				$course_type_tool->set_name($tool);
				$course_type_tool->set_visible_default($this->parse_checkbox_value($this->getSubmitValue($element_default)));
				$tools_array[] = $course_type_tool;
			}
		}
		return $tools_array;
	}

	function fill_course_settings($course)
	{
		$values = $this->exportValues();
		$course->get_settings()->set_course_id($course->get_id());
		if($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED]))
			$course->set_language($values[CourseTypeSettings :: PROPERTY_LANGUAGE]);
		if($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED]))
			$course->set_visibility($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY]));
		if($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS_FIXED]))
			$course->set_access($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS]));
		if($values[self::UNLIMITED_MEMBERS])
			$members = 0;
		else
			$members = $values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS];
		if($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED]))
			$course->set_max_number_of_members($members);
		return $course->get_settings();
	}

	function fill_course_layout($course)
	{
		$values = $this->exportValues();
		$course->get_layout_settings()->set_course_id($course->get_id());
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED]))
			$course->set_intro_text($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_INTRO_TEXT]));
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED]))
			$course->set_student_view($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_STUDENT_VIEW]));
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_LAYOUT_FIXED]))
			$course->set_layout($values[CourseLayout :: PROPERTY_LAYOUT]);
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED]))
			$course->set_tool_shortcut($values[CourseLayout :: PROPERTY_TOOL_SHORTCUT]);
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_MENU_FIXED]))
			$course->set_menu($values[CourseLayout :: PROPERTY_MENU]);
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED]))
			$course->set_breadcrumb($values[CourseLayout :: PROPERTY_BREADCRUMB]);
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED]))
			$course->set_feedback($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_FEEDBACK]));
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED]))
			$course->set_course_code_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE]));
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED]))
			$course->set_course_manager_name_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE]));
		if($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED]))
			$course->set_course_languages_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE]));
		return $course->get_layout_settings();
	}
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$course_type = $this->course_type;
		$defaults[CourseType :: PROPERTY_NAME] = $course_type->get_name();
		$defaults[CourseType :: PROPERTY_DESCRIPTION] = $course_type->get_description();
		$defaults[CourseType :: PROPERTY_ACTIVE] = $course_type->get_active();

		$course_type_id = $course_type->get_settings()->get_course_type_id();
		$course_type_settings = $course_type->get_settings();
		$defaults[CourseTypeSettings :: PROPERTY_LANGUAGE] = $course_type_settings->get_language();
		$defaults[CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED] = $course_type_settings->get_language_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_VISIBILITY] = $course_type_id?$course_type_settings->get_visibility():1;
		$defaults[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED] = $course_type_settings->get_visibility_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_ACCESS] = $course_type_id?$course_type_settings->get_access():1;
		$defaults[CourseTypeSettings :: PROPERTY_ACCESS_FIXED] = $course_type_settings->get_access_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $course_type_settings->get_max_number_of_members();
		$defaults[self :: UNLIMITED_MEMBERS] = ($course_type_settings->get_max_number_of_members() == 0)? 1:0;
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED] = $course_type_settings->get_max_number_of_members_fixed();

		//Layout defaults.
		$course_type_id = $course_type->get_layout_settings()->get_course_type_id();
		$student_view = $course_type->get_layout_settings()->get_student_view();
		$defaults[CourseTypeLayout :: PROPERTY_STUDENT_VIEW] = $course_type_id?$student_view:1;

		$student_view_fixed = $course_type->get_layout_settings()->get_student_view_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED] = $student_view_fixed;

		$layout = $course_type->get_layout_settings()->get_layout();
		$defaults[CourseTypeLayout :: PROPERTY_LAYOUT] = $layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME);

		$layout_fixed = $course_type->get_layout_settings()->get_layout_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_LAYOUT] = $layout_fixed;

		$tool_shortcut = $course_type->get_layout_settings()->get_tool_shortcut();
		$defaults[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT] = $tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME);

		$tool_shortcut_fixed = $course_type->get_layout_settings()->get_tool_shortcut_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT] = $tool_shortcut_fixed;

		$menu = $course_type->get_layout_settings()->get_menu();
		$defaults[CourseTypeLayout :: PROPERTY_MENU] = $menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME);

		$menu_fixed = $course_type->get_layout_settings()->get_menu_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_MENU] = $menu_fixed;

		$breadcrumb = $course_type->get_layout_settings()->get_breadcrumb();
		$defaults[CourseTypeLayout :: PROPERTY_BREADCRUMB] = $breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME);

		$breadcrumb_fixed = $course_type->get_layout_settings()->get_breadcrumb_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_BREADCRUMB] = $breadcrumb_fixed;

		$feedback = $course_type->get_layout_settings()->get_feedback();
		$defaults[CourseTypeLayout :: PROPERTY_FEEDBACK] = $course_type_id?$feedback:1;

		$enable_introduction_text = $course_type->get_layout_settings()->get_intro_text();
		$defaults[CourseTypeLayout :: PROPERTY_INTRO_TEXT] = $course_type_id?$enable_introduction_text:1;

		$course_code_visible = $course_type->get_layout_settings()->get_course_code_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE] = $course_type_id?$course_code_visible:1;

		$course_manager_name_visible = $course_type->get_layout_settings()->get_course_manager_name_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE] = $course_type_id?$course_manager_name_visible:1;

		$course_languages_visible = $course_type->get_layout_settings()->get_course_languages_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE] = $course_type_id?$course_languages_visible:1;

		$feedback_fixed = $course_type->get_layout_settings()->get_feedback_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED] = $feedback_fixed;

		$enable_introduction_text_fixed = $course_type->get_layout_settings()->get_intro_text_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED] = $enable_introduction_text_fixed;

		$course_code_visible_fixed = $course_type->get_layout_settings()->get_course_code_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED] = $course_code_visible_fixed;

		$course_manager_name_visible_fixed = $course_type->get_layout_settings()->get_course_manager_name_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED] = $course_manager_name_visible_fixed;

		$course_languages_visible_fixed = $course_type->get_layout_settings()->get_course_languages_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED] = $course_languages_visible_fixed;

		parent :: setDefaults($defaults);
	}

	function get_form_type()
	{
		return $this->form_type;
	}
}
?>