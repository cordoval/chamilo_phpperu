<?php
/**
 * $Id: course_type_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */

require_once dirname(__FILE__) . '/../course/common_form.class.php';

class CourseTypeForm extends CommonForm
{
   	
   	const CREATION_TARGET = 'creation_groups';
   	const CREATION_ELEMENTS = 'creation_groups_elements';
   	const CREATION_OPTION = 'creation_groups_option';

   	const CREATION_ON_REQUEST_TARGET = 'creation_on_request_groups';
   	const CREATION_ON_REQUEST_ELEMENTS = 'creation_on_request_groups_elements';
   	const CREATION_ON_REQUEST_OPTION = 'creation_on_request_groups_option';
   	
	function CourseTypeForm($form_type, $course_type, $action, $parent)
	{
		parent :: __construct($form_type, $course_type, $action, $parent, 'course_type_settings', 'post');
		$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/course_type_form.js'));
	}

	function build_basic_form()
	{
		$tabs = array();
		$tabs[] = new FormValidatorTab('build_general_settings_form', 'General');
		$tabs[] = new FormValidatorTab('build_layout_form', 'Layout');
		$tabs[] = new FormValidatorTab('build_tools_form', 'Tools');
		$tabs[] = new FormValidatorTab('build_rights_form', 'RightsManagement');
		$tabs[] = new FormValidatorTab('build_creation_rights_form', 'CreationRightsManagement');
		
		$this->add_tabs($tabs, 0);
	}

	function build_tools_form()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('basic');
		$data = array();
		//Tools defaults
		$course_type_tools = $this->object->get_tools();

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
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK, Translation :: get('AllowFeedback'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT, Translation :: get('AllowIntroduction'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW, Translation :: get('AllowStudentView'));
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
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED, Translation :: get('AllowFeedback'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED, Translation :: get('AllowIntroduction'));
		$this->addElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED, Translation :: get('AllowStudentView'));
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

		$lang_options = AdminDataManager :: get_languages();
		$this->addElement('select', CourseTypeSettings :: PROPERTY_LANGUAGE, Translation :: get('CourseTypeLanguage'), $lang_options);

		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY, Translation :: get('CourseTypeVisibility'));

		//Accessibility
		$choices = array();
		$choices[] = $this->createElement('radio', CourseTypeSettings :: PROPERTY_ACCESS, '', Translation :: get('Open'), 1);
		$choices[] = $this->createElement('radio', CourseTypeSettings :: PROPERTY_ACCESS, '', Translation :: get('Closed'), 0);
		$this->addGroup($choices, 'access_choices', Translation :: get('CourseTypeAccess'), '<br />', false);

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
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_TITULAR_FIXED, Translation :: get('CourseTypeTitularCannotSelect'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED, Translation :: get('CourseTypeLanguage'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED, Translation :: get('CourseTypeVisibility'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_ACCESS_FIXED, Translation :: get('CourseTypeAccess'));
		$this->addElement('checkbox', CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED , Translation :: get('MaximumNumberOfMembers'));
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

	function build_rights_form()
	{
		$attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'group/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
       // $attributes['exclude'] = array('user_' . $this->tool->get_user_id());
        $attributes['defaults'] = array();

        $legend_items = array();
        //$legend_items[] = new ToolbarItem(Translation :: get('CourseUser'), Theme :: get_common_image_path() . 'treemenu/user.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        //$legend_items[] = new ToolbarItem(Translation :: get('LinkedUser'), Theme :: get_common_image_path() . 'treemenu/user_platform.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('UserGroup'), Theme :: get_common_image_path() . 'treemenu/group.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar :: TYPE_HORIZONTAL);

        $this->addElement('category', Translation :: get('Subscribe'));
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE, Translation :: get('DirectSubscribeAvailable'), '', array('class' => 'available direct'));
        $this->addElement('html', '<div id="directBlock">');
        $this->add_receivers(self :: SUBSCRIBE_DIRECT_TARGET, Translation :: get('DirectSubscribeFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE, Translation :: get('RequestSubscribeAvailable'), '', array('class' => 'available request'));
        $this->addElement('html', '<div id="requestBlock">');
        $this->add_receivers(self :: SUBSCRIBE_REQUEST_TARGET, Translation :: get('RequestSubscribeFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE, Translation :: get('CodeSubscribeAvailable'), '', array('class' => 'available code'));
        $this->addElement('html', '<div id="codeBlock">');
        $this->add_receivers(self :: SUBSCRIBE_CODE_TARGET, Translation :: get('CodeSubscribeFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('category');
        $this->addElement('category', Translation :: get('Unsubscribe'));
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_UNSUBSCRIBE_AVAILABLE, Translation :: get('UnsubscribeAvailable'), '', array('class' => 'available unsubscribe'));
        $this->addElement('html', '<div id="unsubscribeBlock">');
        $this->add_receivers(self :: UNSUBSCRIBE_TARGET, Translation :: get('UnsubscribeFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('CourseTypeLockedProperties'));
		$this->add_information_message('', '', Translation :: get('LockedFunctionalityDescription'), true);
		$this->addElement('checkbox', CourseTypeRights :: PROPERTY_DIRECT_SUBSCRIBE_FIXED, Translation :: get('DirectSubscribe'));
		$this->addElement('checkbox', CourseTypeRights :: PROPERTY_REQUEST_SUBSCRIBE_FIXED, Translation :: get('RequestSubscribe'));
		$this->addElement('checkbox', CourseTypeRights :: PROPERTY_CODE_SUBSCRIBE_FIXED, Translation :: get('CodeSubscribe'));
		$this->addElement('checkbox', CourseTypeRights :: PROPERTY_UNSUBSCRIBE_FIXED , Translation :: get('Unsubscribe'));
		$this->addElement('category');
	}
	
	function build_creation_rights_form()
	{
		$attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'group/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
       // $attributes['exclude'] = array('user_' . $this->tool->get_user_id());
        $attributes['defaults'] = array();

        $legend_items = array();
        //$legend_items[] = new ToolbarItem(Translation :: get('CourseUser'), Theme :: get_common_image_path() . 'treemenu/user.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        //$legend_items[] = new ToolbarItem(Translation :: get('LinkedUser'), Theme :: get_common_image_path() . 'treemenu/user_platform.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('UserGroup'), Theme :: get_common_image_path() . 'treemenu/group.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar :: TYPE_HORIZONTAL);

        $this->addElement('category', Translation :: get('CreationRights'));
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_CREATION_AVAILABLE, Translation :: get('CreationAvailable'), '', array('class' => 'available creation'));
        $this->addElement('html', '<div id="creationBlock">');
        $this->add_receivers(self :: CREATION_TARGET, Translation :: get('CreationFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('checkbox', CourseTypeRights :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE, Translation :: get('CreationOnRequestAvailable'), '', array('class' => 'available creation_on_request'));
        $this->addElement('html', '<div id="creation_on_requestBlock">');
        $this->add_receivers(self :: CREATION_ON_REQUEST_TARGET, Translation :: get('CreationOnRequestFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('category');
	}
	
	function update()
	{
		$course_type = $this->fill_general_settings();

		if(!$course_type->update())
		{
			return false;
		}

		$course_type_rights = $this->fill_rights();
		if (!$course_type_rights->update())
		{
			return false;
		}
		
		$wdm = WeblcmsDataManager::get_instance();
		$previous_rights = null;
		$course_type_rights = null;
		for($i=0;$i<3;$i++)
		{
			switch($i)
			{
				case 0:
					$previous_rights = $wdm->retrieve_course_type_group_subscribe_rights($this->object->get_id());
					$course_type_rights = $this->fill_subscribe_rights();
					break;
				case 1:
					$previous_rights = $wdm->retrieve_course_type_group_unsubscribe_rights($this->object->get_id());
					$course_type_rights = $this->fill_unsubscribe_rights();
					break;
				case 2:
					$previous_rights = $wdm->retrieve_course_type_group_creation_rights($this->object->get_id());
					$course_type_rights = $this->fill_creation_rights();
					break;
			}
			while($previous_right = $previous_rights->next_result())
			{
				$validation = false;
				foreach($course_type_rights as $index => $right)
				{
					if($right->get_group_id() == $previous_right->get_group_id())
					{
						if(!$right->update())
							return false;
						unset($course_type_rights[$index]);
						$validation = true;
					}
				}
				if(!$validation)
				{
					if(!$previous_right->delete())
						return false;
				}
			}
			foreach($course_type_rights as $right)
			{
				if(!$right->create())
					return false;
			}
		}
		
		$course_type_settings = $this->fill_settings();

		if (!$course_type_settings->update())
		{
			return false;
		}

		$tools = $wdm->get_tools('basic');
		$selected_tools = $this->fill_tools($tools);
		$default_tools = $this->object->get_tools();

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

		$course_type_layout = $this->fill_layout();

		if($course_type_layout->update())
		{
			//update all course related to the coursetype
			$condition = new EqualityCondition(CourseTypeSettings :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());
			$courses = $wdm->retrieve_courses($condition);
			while($course = $courses->next_result())
			{
				$course = $wdm->retrieve_course($course->get_id());

				$course_settings = $this->fill_course_settings($course);
				if(!$course_settings->update())
					return false;
				$course_layout = $this->fill_course_layout($course);
				if(!$course_layout->update())
					return false;

				$selected_tools = $this->fill_tools($tools);
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

	function create()
	{
		$course_type = $this->fill_general_settings();

		if(!$course_type->create())
		{
			return false;
		}
		
		$course_type_rights = $this->fill_rights();
		
		if (!$course_type_rights->create())
		{
			return false;
		}
		
		$course_type_subscribe_rights = $this->fill_subscribe_rights();
		foreach($course_type_subscribe_rights as $right)
		{
			if(!$right->create())
				return false;
		}
		
		$course_type_unsubscribe_rights = $this->fill_unsubscribe_rights();
		foreach($course_type_unsubscribe_rights as $right)
		{
			if(!$right->create())
				return false;
		}
		
		$course_type_create_rights = $this->fill_creation_rights();
		foreach($course_type_create_rights as $right)
		{
			if(!$right->create())
				return false;
		}
		
		$course_type_settings = $this->fill_settings();

		if (!$course_type_settings->create())
		{
			return false;
		}

		$wdm = WeblcmsDataManager :: get_instance();
		$tools = $wdm->get_tools('basic');
		$selected_tools = $this->fill_tools($tools);
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

		$course_type_layout = $this->fill_layout();

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

	function fill_general_settings()
	{
		$course_type = $this->object;
		$values = $this->exportValues();
		$course_type->set_name($values[CourseType :: PROPERTY_NAME]);
		$course_type->set_description($values[CourseType :: PROPERTY_DESCRIPTION]);
		$course_type->set_active($values[CourseType :: PROPERTY_ACTIVE]);
		return $course_type;
	}

	function fill_settings()
	{
		$values = $this->exportValues();
		$settings = parent::fill_settings();
		$settings->set_course_type_id($this->object->get_id());
		$settings->set_titular_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_TITULAR_FIXED]));
		$settings->set_language_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED]));
		$settings->set_visibility_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED]));
		$settings->set_access_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS_FIXED]));
		$settings->set_max_number_of_members_fixed($this->parse_checkbox_value($values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED]));
		return $settings;
	}

	function fill_layout()
	{
		$values = $this->exportValues();
		$layout = parent::fill_layout();
		$layout->set_course_type_id($this->object->get_id());
		$layout->set_intro_text_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED]));
		$layout->set_student_view_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED]));
		$layout->set_layout_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_LAYOUT_FIXED]));
		$layout->set_tool_shortcut_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED]));
		$layout->set_menu_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_MENU_FIXED]));
		$layout->set_breadcrumb_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED]));
		$layout->set_feedback_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED]));
		$layout->set_course_code_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED]));
		$layout->set_course_manager_name_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED]));
		$layout->set_course_languages_visible_fixed($this->parse_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED]));
		return $layout;
	}

	function fill_tools($tools)
	{
		$tools_array = array();
		foreach($tools as $tool)
		{
			$element_name = $tool . "element";
			$element_default = $tool . "elementdefault";

			if($this->parse_checkbox_value($this->getSubmitValue($element_name)))
			{
				$course_type_tool = new CourseTypeTool();
				$course_type_tool->set_course_type_id($this->object->get_id());
				$course_type_tool->set_name($tool);
				$course_type_tool->set_visible_default($this->parse_checkbox_value($this->getSubmitValue($element_default)));
				$tools_array[] = $course_type_tool;
			}
		}
		return $tools_array;
	}
	
	function fill_rights()
	{
		$values = $this->exportValues();
		$rights = parent::fill_rights();
		$rights->set_course_type_id($this->object->get_id());
		$rights->set_direct_subscribe_fixed($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_DIRECT_SUBSCRIBE_FIXED]));
		$rights->set_request_subscribe_fixed($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_REQUEST_SUBSCRIBE_FIXED]));
		$rights->set_code_subscribe_fixed($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_CODE_SUBSCRIBE_FIXED]));
		$rights->set_unsubscribe_fixed($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_UNSUBSCRIBE_FIXED]));
		$rights->set_creation_available($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_CREATION_AVAILABLE]));
		$rights->set_creation_on_request_available($this->parse_checkbox_value($values[CourseTypeRights :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE]));
		return $rights;
	}
	
	function fill_creation_rights()
	{
		$values = $this->exportValues();
		$groups_array = array();
		$group_key_check = array();
		
		for($i=0;$i<2;$i++)
		{
			$option = null;
			$target = null;
			$subscribe = null;
			$available = null;
			switch($i)
			{
				case 0: $target = self :: CREATION_ELEMENTS;
						$option = self :: CREATION_OPTION;
						$available = CourseTypeRights::PROPERTY_CREATION_AVAILABLE;
						$subscribe = CourseTypeGroupCreationRight::CREATE_DIRECT;
						break;
				case 1: $target = self :: CREATION_ON_REQUEST_ELEMENTS;
						$option = self :: CREATION_ON_REQUEST_OPTION;
						$available = CourseTypeRights::PROPERTY_CREATION_ON_REQUEST_AVAILABLE;
						$subscribe = CourseTypeGroupCreationRight::CREATE_REQUEST;
						break;
			}
			if($values[$option] && $values[$available])
			{
				foreach($values[$target]['group'] as $value)
				{
					if(!in_array($value, $group_key_check) && !in_array(0, $group_key_check))
					{
						$course_type_group_rights = new CourseTypeGroupCreationRight();
						$course_type_group_rights->set_course_type_id($this->object->get_id());
						$course_type_group_rights->set_group_id($value);
						$course_type_group_rights->set_create($subscribe);
						$groups_array[] = $course_type_group_rights;
						$group_key_check[] = $value;
					}
				}
			}
			else
			{
				if(!in_array(0, $group_key_check) && $values[$available])
					$group_key_check[] = 0;
			}
		}
		return $groups_array;
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
		$course_type = $this->object;
		$defaults[CourseType :: PROPERTY_NAME] = $course_type->get_name();
		$defaults[CourseType :: PROPERTY_DESCRIPTION] = $course_type->get_description();
		$defaults[CourseType :: PROPERTY_ACTIVE] = $course_type->get_active();

		$course_type_settings = $this->object->get_settings();
		$defaults[CourseTypeSettings :: PROPERTY_TITULAR_FIXED] = $course_type_settings->get_titular_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_LANGUAGE_FIXED] = $course_type_settings->get_language_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED] = $course_type_settings->get_visibility_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_ACCESS_FIXED] = $course_type_settings->get_access_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED] = $course_type_settings->get_max_number_of_members_fixed();

		$course_type_rights = $this->object->get_rights();
		$defaults[CourseTypeRights :: PROPERTY_DIRECT_SUBSCRIBE_FIXED] = $course_type_rights->get_direct_subscribe_fixed();
		$defaults[CourseTypeRights :: PROPERTY_REQUEST_SUBSCRIBE_FIXED] = $course_type_rights->get_request_subscribe_fixed();
		$defaults[CourseTypeRights :: PROPERTY_CODE_SUBSCRIBE_FIXED] = $course_type_rights->get_code_subscribe_fixed();
		$defaults[CourseTypeRights :: PROPERTY_UNSUBSCRIBE_FIXED] = $course_type_rights->get_unsubscribe_fixed();
		$defaults[CourseTypeRights :: PROPERTY_CREATION_AVAILABLE] = !is_null($course_type_rights->get_creation_available())? $course_type_rights->get_creation_available():1;
		$defaults[CourseTypeRights :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE] = !is_null($course_type_rights->get_creation_on_request_available())? $course_type_rights->get_creation_on_request_available():0;
		
		$defaults[self :: CREATION_OPTION] = '0';
		$defaults[self :: CREATION_ON_REQUEST_OPTION] = '0';
		
		if(!is_null($course_type->get_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			
			$group_create_rights = $wdm->retrieve_course_type_group_creation_rights($course_type->get_id());
				
			while($right = $group_create_rights->next_result())
			{
				if($right->get_group_id() != 0)
				{
					$element = null;
					switch($right->get_create())
					{
						case CourseTypeGroupCreationRight :: CREATE_DIRECT: 
							$element = self :: CREATION_ELEMENTS;
							break;
						case CourseTypeGroupCreationRight :: CREATE_REQUEST: 
							$element = self :: CREATION_ON_REQUEST_ELEMENTS;
							break;
					}
					
					$selected_group = $this->get_group_array($right->get_group_id());
			        $defaults[$element][$selected_group['id']] = $selected_group;
				}
			}
			
			
			if (count($defaults[self :: CREATION_ELEMENTS]) > 0)
			{
	            $defaults[self :: CREATION_OPTION] = '1';
	            $active = $this->getElement(self :: CREATION_ELEMENTS);
	        	$active->setValue($defaults[self :: CREATION_ELEMENTS]);
			}
	        
	    	if (count($defaults[self :: CREATION_ON_REQUEST_ELEMENTS]) > 0)
	    	{
	            $defaults[self :: CREATION_ON_REQUEST_OPTION] = '1';
	            $active = $this->getElement(self :: CREATION_ON_REQUEST_ELEMENTS);
	        	$active->setValue($defaults[self :: CREATION_ON_REQUEST_ELEMENTS]);
	    	}
			
		}
		
		//Layout defaults.
		$course_type_id = $this->object->get_layout_settings()->get_course_type_id();

		$student_view_fixed = $this->object->get_layout_settings()->get_student_view_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED] = $student_view_fixed;

		$layout_fixed = $this->object->get_layout_settings()->get_layout_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_LAYOUT_FIXED] = $layout_fixed;

		$tool_shortcut_fixed = $this->object->get_layout_settings()->get_tool_shortcut_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED] = $tool_shortcut_fixed;

		$menu_fixed = $this->object->get_layout_settings()->get_menu_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_MENU_FIXED] = $menu_fixed;

		$breadcrumb_fixed = $this->object->get_layout_settings()->get_breadcrumb_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED] = $breadcrumb_fixed;

		$feedback_fixed = $this->object->get_layout_settings()->get_feedback_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED] = $feedback_fixed;

		$enable_introduction_text_fixed = $this->object->get_layout_settings()->get_intro_text_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED] = $enable_introduction_text_fixed;

		$course_code_visible_fixed = $this->object->get_layout_settings()->get_course_code_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED] = $course_code_visible_fixed;

		$course_manager_name_visible_fixed = $this->object->get_layout_settings()->get_course_manager_name_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED] = $course_manager_name_visible_fixed;

		$course_languages_visible_fixed = $this->object->get_layout_settings()->get_course_languages_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED] = $course_languages_visible_fixed;
		
		parent :: setDefaults($defaults);
	}
}
?>