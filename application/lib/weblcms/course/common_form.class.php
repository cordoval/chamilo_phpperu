<?php
/**
 * $Id: course_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_admin_path() . 'settings/settings_admin_connector.class.php';
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

abstract class CommonForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

   	const UNLIMITED_MEMBERS = 'unlimited_members';
   	
   	const SUBSCRIBE_DIRECT_TARGET = 'direct_target_groups';
   	const SUBSCRIBE_DIRECT_TARGET_ELEMENTS = 'direct_target_groups_elements';
   	const SUBSCRIBE_DIRECT_TARGET_OPTION = 'direct_target_groups_option';
   	
   	const SUBSCRIBE_REQUEST_TARGET = 'request_target_groups';
   	const SUBSCRIBE_REQUEST_TARGET_ELEMENTS = 'request_target_groups_elements';
   	const SUBSCRIBE_REQUEST_TARGET_OPTION = 'request_target_groups_option';
   	
   	const SUBSCRIBE_CODE_TARGET = 'code_target_groups';
   	const SUBSCRIBE_CODE_TARGET_ELEMENTS = 'code_target_groups_elements';
   	const SUBSCRIBE_CODE_TARGET_OPTION = 'code_target_groups_option';
   	
   	const UNSUBSCRIBE_TARGET = 'unsubscribe_target_groups';
   	const UNSUBSCRIBE_TARGET_ELEMENTS = 'unsubscribe_target_groups_elements';
   	const UNSUBSCRIBE_TARGET_OPTION = 'unsubscribe_target_groups_option';

    protected $parent;
    protected $object;
    protected $form_type;

    function CommonForm($form_type, $object, $action, $parent, $form_name, $method)
    {
        parent :: __construct($form_name, $method, $action);

        $this->object = $object;
		$this->parent = $parent;
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->add_progress_bar(2);
        $this->setDefaults();
        $this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/weblcms_common_form.js'));
		$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/viewable_checkbox.js'));
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    abstract function build_basic_form();
    
    abstract function build_general_settings_form();
    
	abstract function build_layout_form();

	abstract function build_tools_form();
	
	abstract function build_rights_form();

	function save()
	{
		switch($this->form_type)
		{
			case self::TYPE_CREATE: return $this->create();
									break;
			case self::TYPE_EDIT: return $this->update();
								  break;
		}
	}

    abstract function update();

    abstract function create();

    abstract function fill_general_settings();

	function fill_settings()
	{
		$object = $this->object;
		$values = $this->exportValues();
		if(get_class($this->object) == "CourseType")
			$object = $object->get_settings();
		$object->set_language($values[CourseSettings :: PROPERTY_LANGUAGE]);
		$object->set_visibility($this->parse_checkbox_value($values[CourseSettings :: PROPERTY_VISIBILITY]));
		$object->set_access($this->parse_checkbox_value($values[CourseSettings :: PROPERTY_ACCESS]));
		if($values[self::UNLIMITED_MEMBERS])
			$members = 0;
		else
			$members = $values[CourseSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS];
		$object->set_max_number_of_members($members);
		if(get_class($this->object) == "CourseType")
			return $object;
		else
		{
			$object->get_settings()->set_course_id($object->get_id());
			return $object->get_settings();
		}
	}

	function fill_layout()
	{
		$object = $this->object;
		$values = $this->exportValues();
		if(get_class($this->object) == "CourseType")
			$object = $object->get_layout_settings();
		$object->set_intro_text($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_INTRO_TEXT]));
		$object->set_student_view($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_STUDENT_VIEW]));
		$object->set_layout($values[CourseLayout :: PROPERTY_LAYOUT]);
		$object->set_tool_shortcut($values[CourseLayout :: PROPERTY_TOOL_SHORTCUT]);
		$object->set_menu($values[CourseLayout :: PROPERTY_MENU]);
		$object->set_breadcrumb($values[CourseLayout :: PROPERTY_BREADCRUMB]);
		$object->set_feedback($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_FEEDBACK]));
		$object->set_course_code_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE]));
		$object->set_course_manager_name_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE]));
		$object->set_course_languages_visible($this->parse_checkbox_value($values[CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE]));
		if(get_class($this->object) == "CourseType")
			return $object;
		else
		{
			$object->get_layout_settings()->set_course_id($this->object->get_id());
			return $object->get_layout_settings();
		}
	}

	abstract function fill_tools($tools);
	
	function fill_rights()
	{
		$object = $this->object;
		$values = $this->exportValues();
		if(get_class($this->object) == "CourseType")
			$object = $object->get_rights();
		$object->set_direct_subscribe_available($this->parse_checkbox_value($values[CourseRights :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE]));
		$object->set_request_subscribe_available($this->parse_checkbox_value($values[CourseRights :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE]));
		$object->set_code_subscribe_available($this->parse_checkbox_value($values[CourseRights :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE]));
		$object->set_unsubscribe_available($this->parse_checkbox_value($values[CourseRights :: PROPERTY_UNSUBSCRIBE_AVAILABLE]));
		if(get_class($this->object) == "CourseType")
			return $object;
		else
		{
			$object->set_code($values[CourseRights :: PROPERTY_CODE]);
			$object->get_rights()->set_course_id($this->object->get_id());
			return $object->get_rights();
		}
	}
	
	function fill_subscribe_rights()
	{
		$values = $this->exportValues();
		$groups_array = array();
		$group_key_check = array();
		$wdm = WeblcmsDataManager::get_instance();
		
		$class = get_class($this->object) . "GroupSubscribeRight";
		$id_method = "set_" . Utilities :: camelcase_to_underscores(get_class($this->object)) . "_id";
		

		for($i=0;$i<3;$i++)
		{
			$option = null;
			$target = null;
			$subscribe = null;
			$available = null;
			$fixed = null;
			switch($i)
			{
				case 0: $target = self :: SUBSCRIBE_DIRECT_TARGET_ELEMENTS;
						$option = self :: SUBSCRIBE_DIRECT_TARGET_OPTION;
						$available = CourseRights::PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE;
						$subscribe = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
						$fixed = "direct_fixed";
						break;
				case 1: $target = self :: SUBSCRIBE_REQUEST_TARGET_ELEMENTS;
						$option = self :: SUBSCRIBE_REQUEST_TARGET_OPTION;
						$available = CourseRights::PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE;
						$subscribe = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
						$fixed = "request_fixed";
						break;
				case 2: $target = self :: SUBSCRIBE_CODE_TARGET_ELEMENTS;
						$option = self :: SUBSCRIBE_CODE_TARGET_OPTION;
						$available = CourseRights::PROPERTY_CODE_SUBSCRIBE_AVAILABLE;
						$subscribe = CourseGroupSubscribeRight::SUBSCRIBE_CODE;
						$fixed = "code_fixed";
						break;
			}
			if($values[$option] && $values[$available])
			{
				foreach($values[$target]['group'] as $value)
				{
					if(!in_array($value, $group_key_check) && !in_array(0, $group_key_check))
					{
						$course_type_group_rights = new $class();
						$course_type_group_rights->$id_method($this->object->get_id());
						$course_type_group_rights->set_group_id($value);
						$course_type_group_rights->set_subscribe($subscribe);
						$groups_array[] = $course_type_group_rights;
						$group_key_check[] = $value;
					}
				}
			}
			elseif($values[$fixed])
			{
				$course_type_group_rights = $wdm->retrieve_course_type_group_rights_by_type($this->object->get_course_type_id(), $subscribe);
				while($group_right = $course_type_group_rights->next_result())
				{
					$course_group_right = CourseGroupSubscribeRight :: convert_course_type_right_to_course_right($group_right, $this->object->get_id());
					if(!in_array($course_group_right->get_group_id(), $group_key_check) && !in_array(0, $group_key_check))
					{
						$groups_array[] = $course_group_right;
						$group_key_check[] = $course_group_right->get_group_id();
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
	
	function fill_unsubscribe_rights()
	{
		$values = $this->exportValues();
		$groups_array = array();
		
		$class = get_class($this->object) . "GroupUnsubscribeRight";
		$id_method = "set_" . Utilities :: camelcase_to_underscores(get_class($this->object)) . "_id";
		
		if($values[self :: UNSUBSCRIBE_TARGET_OPTION])
		{
			foreach($values[self :: UNSUBSCRIBE_TARGET_ELEMENTS]['group'] as $value)
			{
				$course_group_rights = new $class();
				$course_group_rights->$id_method($this->object->get_id());
				$course_group_rights->set_group_id($value);
				$course_group_rights->set_unsubscribe(1);
				$groups_array[] = $course_group_rights;
			}
		}
		elseif($values['unsubscribe_fixed'])
		{
			$wdm = WeblcmsDataManager::get_instance();
			$course_type_group_rights = $wdm->retrieve_course_type_group_rights_by_type($this->object->get_course_type_id(), CourseGroupSubscribeRight::UNSUBSCRIBE);
			while($group_right = $course_type_group_rights->next_result())
			{
				$course_group_right = CourseGroupUnsubscribeRight :: convert_course_type_right_to_course_right($group_right, $this->object->get_id());
				$groups_array[] = $course_group_right;
				$group_key_check[] = $course_group_right->get_group_id();
			}
		}
		return $groups_array;
	}

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
    	$object = $this->object;
        $settings = $object;
        if(get_class($object) == "CourseType") $settings = $object->get_settings();
        elseif(is_null($this->object->get_id())) $settings = $this->object->get_course_type()->get_settings();
        $defaults[CourseSettings :: PROPERTY_LANGUAGE] = !is_null($settings->get_language())?$settings->get_language():LocalSetting :: get('platform_language');
		$defaults[CourseSettings :: PROPERTY_VISIBILITY] = !is_null($settings->get_visibility())?$settings->get_visibility():1;
		$defaults[CourseSettings :: PROPERTY_ACCESS] = !is_null($settings->get_access())? $settings->get_access():1;
		$defaults[CourseSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $settings->get_max_number_of_members();
		$defaults[self :: UNLIMITED_MEMBERS] = ($settings->get_max_number_of_members() == 0)? 1:0;

		$layout = $object;
		if(get_class($object) == "CourseType") $layout = $object->get_layout_settings();
        elseif(is_null($this->object->get_id())) $layout = $this->object->get_course_type()->get_layout_settings();
		$defaults[CourseLayout :: PROPERTY_STUDENT_VIEW] = !is_null($layout->get_student_view())?$layout->get_student_view():1;
		$defaults[CourseLayout :: PROPERTY_LAYOUT] = $layout->get_layout();
		$defaults[CourseLayout :: PROPERTY_TOOL_SHORTCUT] = $layout->get_tool_shortcut();
		$defaults[CourseLayout :: PROPERTY_MENU] = $layout->get_menu();
		$defaults[CourseLayout :: PROPERTY_BREADCRUMB] = $layout->get_breadcrumb();
		$defaults[CourseLayout :: PROPERTY_FEEDBACK] = !is_null($layout->get_feedback())?$layout->get_feedback():1;
		$defaults[CourseLayout :: PROPERTY_INTRO_TEXT] = !is_null($layout->get_intro_text())?$layout->get_intro_text():1;
		$defaults[CourseLayout :: PROPERTY_COURSE_CODE_VISIBLE] = !is_null($layout->get_course_code_visible())?$layout->get_course_code_visible():1;
		$defaults[CourseLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE] = !is_null($layout->get_course_manager_name_visible())?$layout->get_course_manager_name_visible():1;
		$defaults[CourseLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE] = !is_null($layout->get_course_languages_visible())?$layout->get_course_languages_visible():1;

		$rights = $object;
		if(get_class($object) == "CourseType") $rights= $object->get_rights();
        elseif(is_null($this->object->get_id())) $rights = $this->object->get_course_type()->get_rights();
		$defaults[CourseRights :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE] = !is_null($rights->get_direct_subscribe_available())? $rights->get_direct_subscribe_available():1;
		$defaults[CourseRights :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE] = !is_null($rights->get_request_subscribe_available())? $rights->get_request_subscribe_available():0;
		$defaults[CourseRights :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE] = !is_null($rights->get_code_subscribe_available())? $rights->get_code_subscribe_available():0;
		$defaults[CourseRights :: PROPERTY_UNSUBSCRIBE_AVAILABLE] = !is_null($rights->get_unsubscribe_available())? $rights->get_unsubscribe_available():1;
		
		$defaults[self :: SUBSCRIBE_DIRECT_TARGET_OPTION] = '0';
		$defaults[self :: SUBSCRIBE_REQUEST_TARGET_OPTION] = '0';
		$defaults[self :: SUBSCRIBE_CODE_TARGET_OPTION] = '0';
		$defaults[self :: UNSUBSCRIBE_TARGET_OPTION] = '0';
		
		if(!is_null($object->get_id()) || (get_class($object)=="Course" && !is_null($object->get_course_type()->get_id())))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			
			$retrieve_subscribe_method = "";
			$retrieve_unsubscribe_method = "";
			
			for($i=1;$i<3;$i++)
			{				
				switch($i)
				{
					case 1: $retrieve_subscribe_method = "retrieve_course_type_group_subscribe_rights"; 
							$retrieve_unsubscribe_method = "retrieve_course_type_group_unsubscribe_rights";
							if(get_class($object) == "Course")
							break;
					case 2: if(get_class($object) == "CourseType")
								continue;
							$retrieve_subscribe_method = "retrieve_course_group_subscribe_rights"; 
							$retrieve_unsubscribe_method = "retrieve_course_group_unsubscribe_rights";
							break;
				}
				
				$id = $object->get_id();
				if($i == 1 && get_class($object) == "Course")
					$id = $object->get_course_type()->get_id();
				$group_subscribe_rights = $wdm->$retrieve_subscribe_method($id);
				$group_unsubscribe_rights = $wdm->$retrieve_unsubscribe_method($id);
				
				while($right = $group_subscribe_rights->next_result())
				{
					if($right->get_group_id() != 0)
					{
						$element = null;
						$check_fixed = null;
						switch($right->get_subscribe())
						{
							case CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT: 
								$element = self :: SUBSCRIBE_DIRECT_TARGET_ELEMENTS;
								$check_fixed = "get_direct_subscribe_fixed";
								break;
							case CourseGroupSubscribeRight :: SUBSCRIBE_REQUEST: 
								$element = self :: SUBSCRIBE_REQUEST_TARGET_ELEMENTS;
								$check_fixed = "get_request_subscribe_fixed";
								break;
							case CourseGroupSubscribeRight :: SUBSCRIBE_CODE: 
								$element = self :: SUBSCRIBE_CODE_TARGET_ELEMENTS;
								$check_fixed = "get_code_subscribe_fixed";
								break;
						}
						if( get_class($object) == "CourseType" || ($i == 1 && ($object->$check_fixed() || $this->get_form_type() == self::TYPE_CREATE)) || ($i == 2 && !$object->$check_fixed()))
						{
							$selected_group = $this->get_group_array($right->get_group_id());
			            	$defaults[$element][$selected_group['id']] = $selected_group;
						}
					}
				}
				
				while($right = $group_unsubscribe_rights->next_result())
				{
					if($right->get_group_id() != 0)
					{
						if( get_class($object) == "CourseType" || ($i == 1 && ($object->get_unsubscribe_fixed() || $this->get_form_type() == self::TYPE_CREATE)) || ($i == 2 && !$object->get_unsubscribe_fixed()))
						{
							$element = self :: UNSUBSCRIBE_TARGET_ELEMENTS;
							$selected_group = $this->get_group_array($right->get_group_id());
				            $defaults[$element][$selected_group['id']] = $selected_group;
						}
					}
				}
			}
			if (count($defaults[self :: SUBSCRIBE_DIRECT_TARGET_ELEMENTS]) > 0 && !(get_class($object) == "Course" && $object->get_direct_subscribe_fixed()))
			{
	            $defaults[self :: SUBSCRIBE_DIRECT_TARGET_OPTION] = '1';
	            $active = $this->getElement(self :: SUBSCRIBE_DIRECT_TARGET_ELEMENTS);
	        	$active->setValue($defaults[self :: SUBSCRIBE_DIRECT_TARGET_ELEMENTS]);
			}
	        
	    	if (count($defaults[self :: SUBSCRIBE_REQUEST_TARGET_ELEMENTS]) > 0 && !(get_class($object) == "Course" && $object->get_request_subscribe_fixed()))
	    	{
	            $defaults[self :: SUBSCRIBE_REQUEST_TARGET_OPTION] = '1';
	            $active = $this->getElement(self :: SUBSCRIBE_REQUEST_TARGET_ELEMENTS);
	        	$active->setValue($defaults[self :: SUBSCRIBE_REQUEST_TARGET_ELEMENTS]);
	    	}
	        
	    	if (count($defaults[self :: SUBSCRIBE_CODE_TARGET_ELEMENTS]) > 0 && !(get_class($object) == "Course" && $object->get_code_subscribe_fixed()))
	        {
	            $defaults[self :: SUBSCRIBE_CODE_TARGET_OPTION] = '1';
	            $active = $this->getElement(self :: SUBSCRIBE_CODE_TARGET_ELEMENTS);
	        	$active->setValue($defaults[self :: SUBSCRIBE_CODE_TARGET_ELEMENTS]);
	        }
	        
			if (count($defaults[self :: UNSUBSCRIBE_TARGET_ELEMENTS]) > 0 && !(get_class($object) == "Course" && $object->get_unsubscribe_fixed()))
	        {
	            $defaults[self :: UNSUBSCRIBE_TARGET_OPTION] = '1';
	            $active = $this->getElement(self :: UNSUBSCRIBE_TARGET_ELEMENTS);
	        	$active->setValue($defaults[self :: UNSUBSCRIBE_TARGET_ELEMENTS]);
	        }
			
		}
		
        parent :: setDefaults($defaults);
    }
    
    function get_group_array($group_id)
    {
    	$gdm = GroupDataManager :: get_instance();
    	$group = $gdm->retrieve_group($group_id);
		$selected_group = array();
		$selected_group['id'] = 'group_' . $group->get_id();
		$selected_group['classes'] = 'type type_group';
		$selected_group['title'] = $group->get_name();
		$selected_group['description'] = $group->get_name();
		return $selected_group;
    }

	function get_form_type()
	{
		return $this->form_type;
	}
}
?>