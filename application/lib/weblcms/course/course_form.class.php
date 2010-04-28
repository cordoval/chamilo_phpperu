<?php
/**
 * $Id: course_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_admin_path() . 'settings/settings_admin_connector.class.php';
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/common_form.class.php';
require_once dirname(__FILE__) . '/rights_tree_renderer.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

class CourseForm extends CommonForm
{
    private $user;
    private $course_type_id;

    function CourseForm($form_type, $course, $user, $action, $parent)
    {
    	$this->course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
		$this->allow_no_course_type = $user->is_platform_admin() || PlatformSetting::get('allow_course_creation_without_coursetype', 'weblcms');
        $wdm = WeblcmsDataManager :: get_instance();
        if(!is_null($this->course_type_id))
        {
       		$course->set_course_type($wdm->retrieve_course_type($this->course_type_id));
       		$course_type_id = $course->get_course_type()->get_id();
       		if(empty($course_type_id) && ($this->course_type_id != 0 || $form_type == self::TYPE_CREATE || $this->allow_no_course_type))
        		$this->course_type_id = $course->get_course_type()->get_id();
        }
        else
        	$this->course_type_id = $course->get_course_type()->get_id();
    	
       	$this->user = $user;
       	
        parent :: __construct($form_type, $course, $action, $parent, 'course_settings', 'post');
		$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/course_form.js'));
		$this->addElement('html', "<script type=\"text/javascript\">
			/* <![CDATA[ */
			var current_course_type = " . (is_null($this->course_type_id)?'0':$this->course_type_id) . ";
			/* ]]> */
			</script>\n");
    }

    function build_basic_form()
    {
  		$right = $this->can_user_create($this->user);
       	if($right == CourseTypeGroupCreationRight::CREATE_REQUEST && $this->form_type == self::TYPE_CREATE)
       		$this->addElement('html', Display :: normal_message(Translation :: get('CourseTypeRequestFormNeeded'),true));
    	
    	$tabs = Array();
    	$tabs[] = new FormValidatorTab('build_general_settings_form','General');
		$tabs[] = new FormValidatorTab('build_layout_form', 'Layout');
    	if($this->form_type == self::TYPE_CREATE)
    		$tabs[] = new FormValidatorTab('build_tools_form', 'Tools');
		$tabs[] = new FormValidatorTab('build_rights_form', 'Rights');
		$selected_tab = 0;
		$this->add_tabs($tabs, $selected_tab);
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
    
    function build_general_settings_form()
    {
        $user_options = array();

        $udm = UserDataManager :: get_instance();
        $wdm = WeblcmsDataManager :: get_instance();

        if(!$this->object->get_titular_fixed() || $this->user->is_platform_admin())
        {
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
	            $user_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->object->get_id());
	            $user_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
	            $user_condition = new AndCondition($user_conditions);
	
	            $users = $wdm->retrieve_course_user_relations($user_condition);
	
	            while ($user = $users->next_result())
	            {
	            	$userobject = $udm->retrieve_user($user->get_user());
	                $user_options[$userobject->get_id()] = $userobject->get_lastname() . '&nbsp;' . $userobject->get_firstname();
	            }
	        }
        }

        $this->addElement('category', Translation :: get('CourseSettings'));

        $this->addElement('hidden', Course :: PROPERTY_ID, '', array('class' => 'course_id'));

        $wdm = WeblcmsDataManager :: get_instance();
		$course_type_objects = $wdm->retrieve_active_course_types();
        $course_types = array();
        if(empty($this->course_type_id) || $this->allow_no_course_type)
        	$course_types[0] = Translation :: get('NoCourseType');
        $this->size = $course_type_objects->size();
        if($this->size != 0)
        {
        	$count = 0;
        	while($course_type = $course_type_objects->next_result())
        	{
        		if($course_type->can_user_create($this->user))
        		{
	        		$course_types[$course_type->get_id()] = $course_type->get_name();
	        		if(is_null($this->course_type_id) && count == 0 && !$this->allow_no_course_type)
	        		{
	        			$parameters = array('go' => WeblcmsManager :: ACTION_CREATE_COURSE, 'course_type' => $course_type->get_id());
	        			$this->parent->simple_redirect($parameters);
	        		}
        		}
        	}
        	$this->addElement('select', Course :: PROPERTY_COURSE_TYPE_ID,  Translation :: get('CourseType'), $course_types, array('class' => 'course_type_selector'));
        	$this->addRule('CourseType', Translation :: get('ThisFieldIsRequired'), 'required');
        }
     	else
     	{
       		$course_type_name = Translation :: get('NoCourseType');
       		if(!is_null($this->course_type_id))
       			$course_type_name = $this->object->get_course_type()->get_name();
     		$this->addElement('static', 'course_type', Translation :: get('CourseType'), $course_type_name);
        	$this->addElement('hidden', Course :: PROPERTY_COURSE_TYPE_ID, 0 );
     	}

        $this->addElement('text', Course :: PROPERTY_NAME, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(Course :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', Course :: PROPERTY_VISUAL, Translation :: get('VisualCode'), array("size" => "50"));

        $this->get_categories(0);
        if(count($this->categories)>0)
        	$this->addElement('select', Course :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->categories);
        else
        {
        	$category_name = Translation :: get('NoCategories');
        	$this->addElement('static', 'Category_Static', Translation :: get('Category'), $category_name);
        	$this->addElement('hidden', Course :: PROPERTY_CATEGORY, 0 );
        }

        
        if(!$this->object->get_titular_fixed() || $this->user->is_platform_admin())
        {
       		$this->addElement('select', Course :: PROPERTY_TITULAR, Translation :: get('Teacher'), $user_options);
        	$this->addRule(Course :: PROPERTY_TITULAR, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        else
        {
        	$user_name = $this->user->get_lastname() . '&nbsp;' . $this->user->get_firstname();
        	$this->addElement('static', 'Titular_Static',  Translation :: get('Teacher'), $user_name);
        	$this->addElement('hidden', Course :: PROPERTY_TITULAR, $this->user->get_id());
        }

        $this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, Translation :: get('Extlink_name'), array("size" => "50"));
        $this->addElement('text', Course :: PROPERTY_EXTLINK_URL, Translation :: get('Extlink_url'), array("size" => "50"));

        $adm = AdminDataManager :: get_instance();
		$lang_options = AdminDataManager :: get_languages();

		$language_disabled = $this->object->get_language_fixed();
		if($language_disabled)
		{
			$lang = $adm->retrieve_language_from_english_name($this->object->get_course_type()->get_settings()->get_language())->get_original_name();
			$this->addElement('static', 'static_language', Translation :: get('CourseTypeLanguage'), $lang);
			$this->addElement('hidden', CourseSettings :: PROPERTY_LANGUAGE, $lang);
		}
		else
			$this->addElement('select', CourseSettings :: PROPERTY_LANGUAGE, Translation :: get('CourseTypeLanguage'), $lang_options);

		$visibility_disabled = $this->object->get_visibility_fixed();
		$attr_array = array();
		if($visibility_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseSettings :: PROPERTY_VISIBILITY, Translation :: get('CourseTypeVisibility'), '', $attr_array);

		$access_disabled = $this->object->get_access_fixed();
		//Accessibility
		if($access_disabled)
		{
			$access = $this->object->get_access();
			if($access)
				$access_name = Translation :: get('Open');
			else
				$access_name = Translation :: get('Closed');
			$this->addElement('static', 'static_member', Translation :: get('CourseTypeAccess'), $access_name);
			$this->addElement('hidden', CourseTypeSettings :: PROPERTY_ACCESS, $access );
		}
		else
		{
			$choices = array();
			$choices[] = $this->createElement('radio', CourseTypeSettings :: PROPERTY_ACCESS, '', Translation :: get('Open'), 1);
			$choices[] = $this->createElement('radio', CourseTypeSettings :: PROPERTY_ACCESS, '', Translation :: get('Closed'), 0);
			$this->addGroup($choices, 'access_choices', Translation :: get('CourseTypeAccess'), '<br />', false);
		}
		
		$members_disabled = $this->object->get_max_number_of_members_fixed();
		$max = "Unlimited";
		if($this->object->get_course_type()->get_settings()->get_max_number_of_members()>0)
			$max = $this->object->get_course_type()->get_settings()->get_max_number_of_members();
		if($members_disabled)
		{
			$this->addElement('static', 'static_member', Translation :: get('MaximumNumberOfMembers'), $max);
			$this->addElement('hidden', CourseSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS, $max );
		}
		else
		{
	        $choices = array();
	        $choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Unlimited'), 1, array('onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_MEMBERS . '_window\')', 'id' => self :: UNLIMITED_MEMBERS));
	        $choices[] = $this->createElement('radio', self :: UNLIMITED_MEMBERS, '', Translation :: get('Limited'), 0, array('onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_MEMBERS . '_window\')'));
	        $this->addGroup($choices, 'choices', Translation :: get('MaximumNumberOfMembers'), '<br />', false);
	        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_MEMBERS . '_window">');
	        $this->add_textfield(CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS, null, false);
	        $this->registerRule('max_members', null, 'HTML_QuickForm_Rule_Max_Members', dirname(__FILE__) .'/../course_type/max_members.rule.class.php');
        	$this->addRule(Array('choices',CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS), Translation :: get('IncorrectNumber'), 'max_members');
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

		$layouts = $this->object->get_course_type()->get_layout_settings()->get_layouts();
		$layout_disabled = $this->object->get_layout_fixed();
		if($layout_disabled)
		{
			$this->addElement('static', 'static_layout', Translation :: get('Layout'), $layouts[$this->object->get_layout()]);
		}
		else
		{
			$this->addElement('select', CourseLayout :: PROPERTY_LAYOUT, Translation :: get('Layout'), CourseLayout :: get_layouts());
		}

		$tool_shortcut = $this->object->get_course_type()->get_layout_settings()->get_tool_shortcut_options();
		$tool_shortcut_disabled = $this->object->get_tool_shortcut_fixed();
		if($tool_shortcut_disabled)
		{
			$this->addElement('static', 'static_tool_shortcut', Translation :: get('ToolShortcut'), $tool_shortcut[$this->object->get_tool_shortcut()]);
		}
		else
		{
			$this->addElement('select', CourseLayout :: PROPERTY_TOOL_SHORTCUT, Translation :: get('ToolShortcut'), CourseLayout :: get_tool_shortcut_options());
		}


		$menu = $this->object->get_course_type()->get_layout_settings()->get_menu_options();
		$menu_disabled = $this->object->get_menu_fixed();
		if($menu_disabled)
		{
			$this->addElement('static', 'static_tool_shortcut', Translation :: get('Menu'), $menu[$this->object->get_menu()]);
		}
		else
		{
			$this->addElement('select', CourseLayout :: PROPERTY_MENU, Translation :: get('Menu'), CourseLayout :: get_menu_options());
		}

		$breadcrumb = $this->object->get_course_type()->get_layout_settings()->get_breadcrumb_options();
		$breadcrumb_disabled = $this->object->get_breadcrumb_fixed();
		if($breadcrumb_disabled)
		{
			$this->addElement('static', 'static_tool_shortcut', Translation :: get('Breadcrumb'), $breadcrumb[$this->object->get_breadcrumb()]);
		}
		else
		{
			$this->addElement('select', CourseLayout :: PROPERTY_BREADCRUMB, Translation :: get('Breadcrumb'), CourseLayout :: get_breadcrumb_options());
		}

		$this->addElement('category');

		$this->addElement('category', Translation :: get('Functionality'));
		$feedback_disabled = $this->object->get_feedback_fixed();
		$attr_array = array();
		if($feedback_disabled)
				$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_FEEDBACK, Translation :: get('AllowFeedback'), '', $attr_array);

		$intro_text_disabled = $this->object->get_intro_text_fixed();
		$attr_array = array();
		if($intro_text_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_INTRO_TEXT, Translation :: get('AllowIntroduction'), '', $attr_array);

		$student_view_disabled = $this->object->get_student_view_fixed();
		$attr_array = array();
		if($student_view_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_STUDENT_VIEW, Translation :: get('AllowStudentView'), '', $attr_array);

		$course_code_visible_disabled = $this->object->get_course_code_visible_fixed();
		$attr_array = array();
		if($course_code_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE, Translation :: get('CourseCodeTitleVisible'), '', $attr_array);

		$course_manager_name_visible_disabled = $this->object->get_course_manager_name_visible_fixed();
		$attr_array = array();
		if($course_manager_name_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE, Translation :: get('CourseManagerNameTitleVisible'), '', $attr_array);

		$course_languages_visible_disabled = $this->object->get_course_languages_visible_fixed();
		$attr_array = array();
		if($course_languages_visible_disabled)
			$attr_array = array('disabled' => 'disabled');
		$this->addElement('checkbox', CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE, Translation :: get('CourseLanguageVisible'), '', $attr_array);
		$this->addElement('category');
	}

	function build_tools_form()
	{
		//Tools defaults
		if(!empty($this->course_type_id))
			$course_type_tools = $this->object->get_course_type()->get_tools();
		else
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$course_type_tools = WeblcmsDataManager :: get_tools('basic');
		}
		foreach ($course_type_tools as $course_type_tool)
		{
			if(!empty($this->course_type_id))
				$tool = $course_type_tool->get_name();
			else
				$tool = $course_type_tool;
		    $tool_data = array();

			$element_default_arr = array('class'=>'viewablecheckbox', 'style'=>'width=80%');
			if(empty($this->course_type_id) || $course_type_tool->get_visible_default())
				$element_default_arr['checked'] = "checked";

			$tool_image_src = Theme :: get_image_path() . 'tool_mini_' . $tool . '.png';
			$tool_image = $tool . "_image";
			$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool) . 'Title'));
			$element_default = $tool . "elementdefault";

			$tool_data[] = '<img class="' . $tool_image .'" src="' . $tool_image_src . '" style="vertical-align: middle;" alt="' . $title . '"/>';
			$tool_data[] = $title;
			$tool_data[] = '<div class="'.$element_default.'"/>'.$this->createElement('checkbox', $element_default, Translation :: get('IsVisible'),'', $element_default_arr)->toHtml().'</div>';
			$count ++;

			$data[] = $tool_data;
		}

        $table = new SortableTableFromArray($data);
        $table->set_header(0, '', false);
        $table->set_header(1, Translation :: get('ToolName'), false);
        $table->set_header(2, Translation :: get('IsToolVisible'), false, null, array('style'=>'width: 30%; text-align: center;'));
        $this->addElement('html', '<div style="width:60%; margin-left:15%;">'.$table->as_html().'</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var common_image_path = '".Theme :: get_common_image_path()."';
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

        $constant_available = null;
        $constant_type = null;
        $type = null;
        for($i=0; $i<3; $i++)
        {
        	switch($i)
        	{
        		case 0: $constant_available = CourseRights :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE;
        				$constant_right = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
        				$target_option = self :: SUBSCRIBE_DIRECT_TARGET_OPTION;
        				$target = self :: SUBSCRIBE_DIRECT_TARGET;
        				$type = 'Direct';
        				break;
        		case 1: $constant_available = CourseRights :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE;
        				$constant_right = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
        				$target_option = self :: SUBSCRIBE_REQUEST_TARGET_OPTION;
        				$target = self :: SUBSCRIBE_REQUEST_TARGET;
        				$type = 'Request';
        				break;
        		case 2: $constant_available = CourseRights :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE;
        				$constant_right = CourseGroupSubscribeRight::SUBSCRIBE_CODE;
        				$target_option = self :: SUBSCRIBE_CODE_TARGET_OPTION;
        				$target = self :: SUBSCRIBE_CODE_TARGET;
        				$type = 'Code';
        				break;
        	}
        	$method = "get_".strtolower($type)."_subscribe_fixed";
	        $course_subscribe_disabled = $this->object->$method();
			$attr_array = array('class' => 'available '.strtolower($type));
			if($course_subscribe_disabled)
				$attr_array['disabled'] = 'disabled';
	        $this->addElement('checkbox', $constant_available, Translation :: get($type.'SubscribeAvailable'), '', $attr_array);
	        $this->addElement('html', '<div id="'.strtolower($type).'Block">');
	        if($i == 2)
	        	$this->addElement('text', CourseRights :: PROPERTY_CODE, Translation :: get('EnterCode'), array("size" => "50"));
	       	if($course_subscribe_disabled)
	       	{
	       		$this->addElement('hidden', strtolower($type).'_fixed' , 1);
	       		$groups_result = WeblcmsDataManager::get_instance()->retrieve_course_type_group_rights_by_type($this->course_type_id, $constant_right);
	    		$groups = array();
	    		while($group = $groups_result->next_result())
	    			$groups[] = $group->get_group_id();
	    		if(count($groups)>1 || $groups[0] != 1)
	    		{
	    			$this->addElement('static', 'static_'.strtolower($type).'_subscribe_for', Translation :: get($type.'SubscribeFor'), Translation :: get('SubscribedGroups'));
	    			$this->addElement('hidden', $target_option, 1);
	        		$tree = new RightsTreeRenderer($groups);
	        		$tree = $tree->render_as_tree();
	        		$this->addElement('html', '<div style="width: 100%; margin-left: 20%">');
	        		$this->addElement('html', $tree);
	        		$this->addElement('html', '</div>');
	    		}
	    		elseif(count($groups)==1 && $groups[0] == 1)
	    		{
	    			$this->addElement('static', 'static_'.strtolower($type).'_subscribe_for', Translation :: get($type.'SubscribeFor'), Translation :: get('Everybody'));
	    			$this->addElement('hidden', $target_option , 0, array('id'=>'receiver_'.$target));
	    		}
	       	}
	       	else
	        	$this->add_receivers($target, Translation :: get($type.'SubscribeFor'), $attributes, 'Everybody');
	        $this->addElement('html', '</div>');
        }
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Unsubscribe'));
        $course_unsubscribe_disabled = $this->object->get_unsubscribe_fixed();
		$attr_array = array('class' => 'available unsubscribe');
		if($course_unsubscribe_disabled)
			$attr_array['disabled'] = 'disabled';
        $this->addElement('checkbox', CourseRights :: PROPERTY_UNSUBSCRIBE_AVAILABLE, Translation :: get('UnsubscribeAvailable'), '', $attr_array);
        $this->addElement('html', '<div id="unsubscribeBlock">');
        if($course_unsubscribe_disabled)
       	{
       		$groups_result = WeblcmsDataManager::get_instance()->retrieve_course_type_group_rights_by_type($this->course_type_id, CourseGroupSubscribeRight::UNSUBSCRIBE);
    		$groups = array();
    		while($group = $groups_result->next_result())
    			$groups[] = $group->get_group_id();
    		if(count($groups)>1 || $groups[0] != 1)
    		{
    			$this->addElement('static', 'static_unsubscribe_for', Translation :: get('UnsubscribeFor'), Translation :: get('SubscribedGroups'));
        		$tree = new RightsTreeRenderer($groups);
        		$tree = $tree->render_as_tree();
        		$this->addElement('html', '<div style="width: 100%; margin-left: 20%">');
        		$this->addElement('html', $tree);
        		$this->addElement('html', '</div>');
    		}
    		elseif(count($groups)==1 && $groups[0] == 1)
    			$this->addElement('static', 'static_unsubscribe_for', Translation :: get('UnsubscribeFor'), Translation :: get('Everybody'));
       	}
       	else
        	$this->add_receivers(self :: UNSUBSCRIBE_TARGET, Translation :: get('UnsubscribeFor'), $attributes, 'Everybody');
        $this->addElement('html', '</div>');
        $this->addElement('category');
	}

    function update()
    {
        $course = $this->fill_general_settings();

    	if(!$course->update())
		{
			return false;
		}

		$course_settings = $this->fill_settings();

		if (!$course_settings->update())
		{
			return false;
		}

		$course_layout = $this->fill_layout();
		if(!$course_layout->update())
			return false;

		$course_rights = $this->fill_rights();
		if(!$course_rights->update())
			return false;
			
		$wdm = WeblcmsDataManager::get_instance();
		$previous_rights = null;
		$course_rights = null;
		for($i=0;$i<2;$i++)
		{
			switch($i)
			{
				case 0:
					$previous_rights = $wdm->retrieve_course_group_subscribe_rights($this->object->get_id());
					$course_rights = $this->fill_subscribe_rights();
					break;
				case 1:
					$previous_rights = $wdm->retrieve_course_group_unsubscribe_rights($this->object->get_id());
					$course_rights = $this->fill_unsubscribe_rights();
					break;
			}
			while($previous_right = $previous_rights->next_result())
			{
				$validation = false;
				foreach($course_rights as $index => $right)
				{
					if($right->get_group_id() == $previous_right->get_group_id())
					{
						if(!$right->update())
							return false;
						unset($course_rights[$index]);
						$validation = true;
					}
				}
				if(!$validation)
				{
					if(!$previous_right->delete())
						return false;
				}
			}
			
			foreach($course_rights as $right)
			{
				if(!$right->create())
					return false;
			}
		}

		return true;
    }

    function create()
    {
        $course = $this->fill_general_settings();
		$course->set_settings($this->fill_settings());
		$course->set_layout($this->fill_layout());
		$course->rights($this->fill_rights());
		
		if(!$course->create())
			return false;
		
        $wdm = WeblcmsDataManager :: get_instance();
		if(!empty($this->course_type_id))
			$tools = $this->object->get_course_type()->get_tools();
		else
			$tools = WeblcmsDataManager :: get_tools('basic');

		$selected_tools = $this->fill_tools($tools);

		if(!$wdm->create_course_modules($selected_tools, $this->object->get_id()))
			return false;
			
		$course_subscribe_rights = $this->fill_subscribe_rights();
		foreach($course_subscribe_rights as $right)
		{
			if(!$right->create())
				return false;
		}
		
		$course_unsubscribe_rights = $this->fill_unsubscribe_rights();
		foreach($course_unsubscribe_rights as $right)
		{
			if(!$right->create())
				return false;
		}
		
        if (! $this->user->is_platform_admin())
            $user_id = $this->user->get_id();
        else
            $user_id = $course->get_titular();
            
		$right = $this->can_user_create($this->user);
       	if($right == CourseTypeGroupCreationRight::CREATE_REQUEST && $this->form_type == self::TYPE_CREATE)
       		return $course;
        elseif ($wdm->subscribe_user_to_course($course, '1', '1', $user_id))
            return true;
        else
            return false;
    }

    function fill_general_settings()
    {
    	$course = $this->object;
		$values = $this->exportValues();
    	//$course->set_id($values[Course :: PROPERTY_ID]);
    	$course->set_course_type_id($values[Course :: PROPERTY_COURSE_TYPE_ID]);
    	
    	if($values[Course :: PROPERTY_VISUAL])
    	{
       		$course->set_visual($values[Course :: PROPERTY_VISUAL]);
    	}
    	else
    	{
    		$course->set_visual(strtoupper(uniqid()));
    	}
        $course->set_name($values[Course :: PROPERTY_NAME]);
        $course->set_category($values[Course :: PROPERTY_CATEGORY]);
        $course->set_titular($values[Course :: PROPERTY_TITULAR]);
        $course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
        $course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
        return $course;
    }

	function fill_tools($tools)
	{
		$tools_array = array();

		foreach($tools as $index => $tool)
		{
			if(!empty($this->course_type_id))
				$tool = $tool->get_name();
			$element_default = $tool . "elementdefault";
			$course_module = new CourseModule();
			$course_module->set_course_code($this->object->get_id());
			$course_module->set_name($tool);
			$course_module->set_visible($this->parse_checkbox_value($this->getSubmitValue($element_default)));
			$course_module->set_section("basic");
			$course_module->set_sort($index);
			$tools_array[] = $course_module;
		}
		return $tools_array;
	}

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $course = $this->object;
        $defaults[Course :: PROPERTY_ID] = $course->get_id();
        $defaults[Course :: PROPERTY_COURSE_TYPE_ID] = $this->course_type_id;
        $defaults[Course :: PROPERTY_VISUAL] = $course->get_visual();
        $defaults[Course :: PROPERTY_TITULAR] = !is_null($course->get_titular())?$course->get_titular():$this->user->get_id();
        $defaults[Course :: PROPERTY_NAME] = $course->get_name();
        $defaults[Course :: PROPERTY_CATEGORY] = $course->get_category();
        $defaults[Course :: PROPERTY_EXTLINK_NAME] = $course->get_extlink_name();
        $defaults[Course :: PROPERTY_EXTLINK_URL] = $course->get_extlink_url();

		$defaults[CourseRights :: PROPERTY_CODE] = $course->get_code();
		
        parent :: setDefaults($defaults);
    }
    
    function can_user_create()
    {
    	$course_type = $this->object->get_course_type();
       	if(!is_null($course_type) && !empty($course_type))
       	{
       		return $course_type->can_user_create($this->user);
       	}
       	else return CourseTypeGroupCreationRight :: CREATE_NONE;
    }
}
?>