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
	}

	function build_editing_form()
	{
		$this->build_basic_form();

		$this->addElement('hidden', Course :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty', 'onclick'=>'javascript: setTimeout(\'change_max_number_enable()\',30)'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	function build_creation_form()
	{
		$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty', 'onclick'=>'javascript: setTimeout(\'change_max_number_enable()\',30)'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	function build_basic_form()
	{
		$tabs = Array(new FormTab('build_general_settings_form','General'),
		new FormTab('build_tools_form', 'Application'),
		new FormTab('build_rights_form', 'Rights'),
		new FormTab('build_layout_form', 'Layout'));
		$selected_tab = 0;
		$this->add_tabs($tabs, $selected_tab);
		//$this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/weblcms.js'));
	}

	function build_rights_form()
	{
		$this->addElement('html', 'yuw from rights');
	}

	function build_tools_form()
	{
		$tools = $this->parent->get_all_non_admin_tools();
		$column_width = 99.9 / 3;

		//$table = new HTML_Table('style="width: 100%;"');
		//$table->setColCount($this->number_of_columns);
		$count = 0;
		$renderer = $this->defaultRenderer();
		$element_template = array();
		$element_template[] = '<div class="row" style="width: 29%">';
		$element_template[] = '<div class="formw">';
		$element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
		$element_template[] = '<div class="form_feedback"></div></div>';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '</div>';
		$element_template = implode("\n", $element_template);

		$this->addElement('html','<div style="width: 80%; margin:auto;">');
		foreach ($tools as $index => $tool)
		{
			$tool_image_src = Theme :: get_image_path() . 'tool_' . $tool . '.png';
			$tool_image_src_disabled = Theme :: get_image_path() . 'tool_' . $tool . '_na.png';
			$tool_image = $tool . "_image";
			$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool) . 'Title'));
			$element_name = $tool . "element";
			$element_default = $tool . "elementdefault";
			$renderer->setElementTemplate($element_template, $element_name);
			$renderer->setElementTemplate($element_template, $element_default);

			$this->addElement('html','<div class="tool" style="width: ' . $column_width . '%;">');
			$this->addElement('html',$title.'<br/><br/>');
			$this->addElement('html','<div style="float: left; width: 20%; padding-left: 12%">');
			$this->addElement('html','<img class="' . $tool_image .'" src="' . $tool_image_src . '" style="vertical-align: middle;" alt="' . $title . '"/>');
			$this->addElement('html','</div>');
			$this->addElement('html','<div style="float: left; width: 32%">');
			$this->addElement('checkbox', $element_name, $title, '',array('class'=>$element_name, 'checked'=>'checked'));
			$this->addElement('html','</div>');
			$this->addElement('html','<div class=\''.$element_default.'\' style="float: left; width: 32%">');
			$this->addElement('checkbox', $element_default, Translation :: get('IsVisible'),'', array('class'=>'viewablecheckbox', 'checked'=>'checked'));
			$this->addElement('html','</div>');
			$this->addElement('html','<div class="clear"></div>');
			$this->addElement('html','<br/>');
			$this->addElement('html','</div>');
			$this->addElement('html','<script type=\'text/javascript\'>
				/* <![CDATA[ */
				(function ($) 
				{		
						$.iphoneStyle'.$element_name.' = {
						   defaults: { checkedLabel: \'ON\', uncheckedLabel: \'OFF\', background: \'#fff\' }
						}
								
					    $.fn.iphoneStyle'.$element_name.' = function(options) {
					    options = $.extend($.iphoneStyle'.$element_name.'.defaults, options);
					    
					    return this.each(function() {
					      var elem = $(this);
					      
					      if (!elem.is(\':checkbox\'))
					        return;
					      
					      elem.css({ opacity: 0 });
					      elem.wrap(\'<div class="binary_checkbox" />\');
					      elem.after(\'<div class="handle"><div class="bg" style="background: \' + options.background + \'"/><div class="slider" /></div>\')
					          .after(\'<label class="off">\'+ options.uncheckedLabel + \'</label>\')
					          .after(\'<label class="on">\' + options.checkedLabel   + \'</label>\');
					      
					      var handle    = elem.siblings(\'.handle\'),
					          handlebg  = handle.children(\'.bg\'),
					          offlabel  = elem.siblings(\'.off\'),
					          onlabel   = elem.siblings(\'.on\'),
					          container = elem.parent(\'.binary_checkbox\'),
					          rightside = container.outerWidth() - 39;
					          image'.$tool_image.' = $(\'.'.$tool_image.'\');
					          default'.$element_default.' = $(\'.'.$element_default.'\');
					      
					      container.click(function() {
					        var is_onstate = (handle.position().left <= 0);
					            new_left   = (is_onstate) ? rightside : 0,
					            bgleft     = (is_onstate) ? 34 : 0;
					
					        handlebg.hide();
					        handle.animate({ left: new_left }, 100, function() {
					          handlebg.css({ left: bgleft }).show();
					        });
					        
					        if (is_onstate) {
					          offlabel.animate({ opacity: 0 }, 200);
					          onlabel.animate({ opacity: 1 }, 200);
					        } else {
					          offlabel.animate({ opacity: 1 }, 200);
					          onlabel.animate({ opacity: 0 }, 200);
					        }
					        
					        if (is_onstate)
					        {
					        	elem.attr(\'checked\', "true");
					        	image'.$tool_image.'.attr(\'src\', \''.$tool_image_src.'\');
					        	default'.$element_default.'.css(\'display\',\'inline\');
					        }
					        else
					        {
					        	elem.removeAttr(\'checked\');
					        	image'.$tool_image.'.attr(\'src\', \''.$tool_image_src_disabled.'\');
					        	default'.$element_default.'.css(\'display\',\'none\');
					        }
					        
					        return false;
					      });
					      
					      // initial load
					      if (elem.is(\':checked\')) {
					        offlabel.css({ opacity: 0 });
					        onlabel.css({ opacity: 1 });
					        handle.css({ left: rightside });
					        handlebg.css({ left: 34 });
					      }
					    });
					  };
				})(jQuery);
				/* ]]> */
				</script>');
			$this->addElement('html','<script type=\'text/javascript\'>
				/* <![CDATA[ */
				$(function () 
				{		
					$(document).ready(function ()
					{
						$(\'.'.$element_name.'\').iphoneStyle'.$element_name.'();
				
					});
				});
				/* ]]> */
				</script>');
				
			$count ++;
		}
		$this->addElement('html','</div>');
		$this->addElement('html','<script type=\'text/javascript\'>
				/* <![CDATA[ */
				(function ($) 
				{
					$.fn.viewableStyle = function() 
  					{
    					 return this.each(function() 
    					 {
     						 var elem = $(this);
      
     						 if (!elem.is(\':checkbox\'))
        						return;
      
     						elem.css(\'display\',\'none\');
     						elem.wrap(\'<div class="viewable_checkbox" />\');
      						elem.after(\'<img class="eye" src="layout/aqua/images/common/action_visible.png" style="vertical-align: middle;" alt=""/>\');
      
      						var eye = elem.siblings(\'.eye\'),
          						container = elem.parent(\'.viewable_checkbox\');
      
							container.click(function() 
							{
    							if(elem.attr(\'checked\'))
    		  						eye.attr(\'src\', \'layout/aqua/images/common/action_invisible.png\');
    							else
    	  							eye.attr(\'src\', \'layout/aqua/images/common/action_visible.png\');
    	  	
       							if (elem.attr(\'checked\'))
        							elem.removeAttr(\'checked\');
        						else
        							elem.attr(\'checked\', \'true\');
        
        						return false;
      						});
    					});
 					};
				})(jQuery);
				/* ]]> */
				</script>');
		$this->addElement('html','<script type=\'text/javascript\'>
				/* <![CDATA[ */
				$(function () 
				{		
					$(document).ready(function ()
					{
						$(\'.viewablecheckbox\').viewableStyle();
				
					});
				});
				/* ]]> */
				</script>');

	}

	function build_layout_form()
	{
		if (PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME))
		{
			$feedback= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK, Translation :: get('Feedback'));
			$feedback_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED, Translation :: get('IsFixed'));
			$this->add_row_elements_required(array($feedback, $feedback_fixed));
		}
			
		$enable_introduction_text= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT, Translation :: get('IntroductionToolTitle'));
		$enable_introduction_text_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED, Translation :: get('IsFixed'));
		$this->add_row_elements_required(array($enable_introduction_text, $enable_introduction_text_fixed));

		$student_view= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW, Translation :: get('StudentView'));
		$student_view_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED, Translation :: get('IsFixed'));
		$this->add_row_elements_required(array($student_view, $student_view_fixed));
			
		$course_code_visible= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE, Translation :: get('CourseCodeTitleVisible'));
		$course_code_visible_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED, Translation :: get('IsFixed'));
		$this->add_row_elements_required(array($course_code_visible, $course_code_visible_fixed));

		$course_manager_name_visible= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE, Translation :: get('CourseManagerNameTitleVisible'));
		$course_manager_name_visible_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED, Translation :: get('IsFixed'));
		$this->add_row_elements_required(array($course_manager_name_visible, $course_manager_name_visible_fixed));

		$course_languages_visible= $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE, Translation :: get('CourseLanguageVisible'));
		$course_languages_visible_fixed = $this->createElement('checkbox', CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED, Translation :: get('IsFixed'));
		$this->add_row_elements_required(array($course_languages_visible, $course_languages_visible_fixed));

		//$this->addElement('html', '<div style="clear: both;"></div>');
			
		//$this->addElement('html', '</div>');
			
		$this->addElement('select', CourseTypeLayout :: PROPERTY_LAYOUT, Translation :: get('Layout'), CourseTypeLayout :: get_layouts());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT, Translation :: get('ToolShortcut'), CourseTypeLayout :: get_tool_shortcut_options());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_MENU, Translation :: get('Menu'), CourseTypeLayout :: get_menu_options());
		$this->addElement('select', CourseTypeLayout :: PROPERTY_BREADCRUMB, Translation :: get('Breadcrumb'), CourseTypeLayout :: get_breadcrumb_options());
			
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
	}

	function build_general_settings_form()
	{
		$this->addElement('text', CourseType :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "40"));
		$this->addRule(CourseType :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('textarea', CourseType :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "50"));
		$this->addRule(CourseType :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

		$adm = AdminDataManager :: get_instance();
		$lang_options = $adm->get_languages();
		$languages = $this->createElement('select', CourseTypeSettings :: PROPERTY_LANGUAGES, Translation :: get('Language'), $lang_options);
		$languages_fixed = $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_LANGUAGES_FIXED, Translation :: get('IsFixed'), '');

		$this->add_row_elements_required(array($languages, $languages_fixed));

		$visibility = $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY, Translation :: get('Visibility'));
		$visibility_fixed = $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED, Translation :: get('IsFixed'));

		$this->add_row_elements_required(array($visibility, $visibility_fixed));

		$access= $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_ACCESS, Translation :: get('Access'));
		$access_fixed = $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_ACCESS_FIXED, Translation :: get('IsFixed'));

		$this->add_row_elements_required(array($access, $access_fixed));

		$members = $this->createElement('text', CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS , Translation :: get('MaximumNumberOfMembers'), array('id' => 'max_number','size' => '4'));
		$members_unlimited = $this->createElement('checkbox', 'unlimited' , Translation :: get('Unlimited'),'', array('id' => 'unlimited', 'onclick' => 'javascript:change_max_number_enable();'));
		$members_fixed = $this->createElement('checkbox', CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED , Translation :: get('IsFixed'));

		$this->add_row_elements_required(array($members, $members_unlimited, $members_fixed));

		$this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var prev_text = 0;
					
					function change_max_number_enable() 
					{
						un = document.getElementById(\"unlimited\");
						el = document.getElementById(\"max_number\");
						
						if(un.checked)
						{
							el.disabled = true;
							prev_text = el.value;
							el.value = 0;
						}
						else
						{
							el.disabled = false;
							el.value = prev_text;
						}
					}
					
					change_max_number_enable();
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
		
		$tools = $this->fill_course_type_tools();
		$validation = true;
		
		foreach($tools as $tool)
		{
			$tool->update();
		}
		
		
		if(!$validation)
		{
			return false;
		}
		
		$course_type_layout = $this->fill_course_type_layout();
		
		if($course_type_layout->update())
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
		
		$tools = $this->fill_course_type_tools();
		$validation = true;
		
		foreach($tools as $tool)
		{
			$tool->create();
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
		return $course_type;
	}
	
	function fill_course_type_settings()
	{
		$course_type = $this->course_type;
		$values = $this->exportValues();
		$course_type_settings = $course_type->get_settings();
		$course_type_settings->set_course_type_id($course_type->get_id());
		$course_type_settings->set_languages($values[CourseTypeSettings :: PROPERTY_LANGUAGES]);
		$course_type_settings->set_languages_fixed($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_LANGUAGES_FIXED]));
		$course_type_settings->set_visibility($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY]));
		$course_type_settings->set_visibility_fixed($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED]));
		$course_type_settings->set_access($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS]));
		$course_type_settings->set_access_fixed($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_ACCESS_FIXED]));
		if($this->get_checkbox_value($values['unlimited']))
			$members = 0;
		else
			$members = $values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS];
		$course_type_settings->set_max_number_of_members($members);
		$course_type_settings->set_max_number_of_members_fixed($this->get_checkbox_value($values[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED]));
		return $course_type_settings;
	}
	
	function fill_course_type_layout()
	{
		$course_type = $this->course_type;
		$values = $this->exportValues();
		$course_type_layout = $course_type->get_layout();
		$course_type_layout->set_course_type_id($this->course_type->get_id());	
		$course_type_layout->set_intro_text($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT]);
		$course_type_layout->set_intro_text_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED]));
		$course_type_layout->set_student_view($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW]);
		$course_type_layout->set_student_view_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED]));
		$course_type_layout->set_layout($values[CourseTypeLayout :: PROPERTY_LAYOUT]);
		$course_type_layout->set_layout_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_LAYOUT_FIXED]));
		$course_type_layout->set_tool_shortcut($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT]);
		$course_type_layout->set_tool_shortcut_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT_FIXED]));
		$course_type_layout->set_menu($values[CourseTypeLayout :: PROPERTY_MENU]);
		$course_type_layout->set_menu_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_MENU_FIXED]));
		$course_type_layout->set_breadcrumb($values[CourseTypeLayout :: PROPERTY_BREADCRUMB]);
		$course_type_layout->set_breadcrumb_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_BREADCRUMB_FIXED]));
		$course_type_layout->set_feedback($values[CourseTypeLayout :: PROPERTY_FEEDBACK]);
		$course_type_layout->set_feedback_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED]));
		$course_type_layout->set_course_code_visible($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE]);
		$course_type_layout->set_course_code_visible_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED]));
		$course_type_layout->set_course_manager_name_visible($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE]);
		$course_type_layout->set_course_manager_name_visible_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED]));
		$course_type_layout->set_course_languages_visible($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE]);
		$course_type_layout->set_course_languages_visible_fixed($this->get_checkbox_value($values[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED]));
		
		return $course_type_layout;		
	}
	
	function fill_course_type_tools()
	{
		$tools = $this->parent->get_all_non_admin_tools();
		$tools_array = array();
		$values = $this->exportValues();
		foreach($tools as $tool)
		{
			$element_name = $tool . "element";
			$element_default = $tool . "elementdefault";
			
			if($this->get_checkbox_value($values[$element_name]))
			{
				$course_type_tool = new CourseTypeTool();
				$course_type_tool->set_course_type_id($this->course_type->get_id());
				$course_type_tool->set_name($tool);
				$course_type_tool->set_visible_default($this->get_checkbox_value($values[$element_default]));
				$tools_array[] = $course_type_tool;
			}
		}
		return $tools_array;
	}
	
	function get_checkbox_value($checkbox)
	{
		if(isset($checkbox) && $checkbox == 1)
		return 1;
		else
		return 0;
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

		$course_type_id = $course_type->get_settings()->get_course_type_id();
		$course_type_settings = $course_type->get_settings();
		$defaults[CourseTypeSettings :: PROPERTY_LANGUAGES] = $course_type_settings->get_languages();
		$defaults[CourseTypeSettings :: PROPERTY_LANGUAGES_FIXED] = $course_type_settings->get_languages_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_VISIBILITY] = $course_type_id?$course_type_settings->get_visibility():1;
		$defaults[CourseTypeSettings :: PROPERTY_VISIBILITY_FIXED] = $course_type_settings->get_visibility_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_ACCESS] = $course_type_id?$course_type_settings->get_access():1;
		$defaults[CourseTypeSettings :: PROPERTY_ACCESS_FIXED] = $course_type_settings->get_access_fixed();
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $course_type_settings->get_max_number_of_members();
		$defaults['unlimited'] = ($course_type_settings->get_max_number_of_members() == 0)? 1:0;
		$defaults[CourseTypeSettings :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED] = $course_type_settings->get_max_number_of_members_fixed();

		//		$layout = $course->get_layout();
		//		$defaults[Course :: PROPERTY_LAYOUT] = $layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME);
		//
		//		$tool_shortcut = $course->get_tool_shortcut();
		//		$defaults[Course :: PROPERTY_TOOL_SHORTCUT] = $tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME);
		//
		//		$menu = $course->get_menu();
		//		$defaults[Course :: PROPERTY_MENU] = $menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME);
		//
		//		$breadcrumb = $course->get_breadcrumb();
		//		$defaults[Course :: PROPERTY_BREADCRUMB] = $breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME);
		//
		//		$feedback = $course->get_allow_feedback();
		//		$defaults[Course :: PROPERTY_ALLOW_FEEDBACK] = $feedback ? $feedback : PlatformSetting :: get('feedback', WeblcmsManager :: APPLICATION_NAME);
		//
		//		$course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
		//
		//		if ($course_can_have_theme)
		//		{
		//			$defaults[Course :: PROPERTY_THEME] = $course->get_theme();
		//		}

		//Layout defaults.
		$course_type_id = $course_type->get_layout()->get_course_type_id();
		$student_view = $course_type->get_layout()->get_student_view();
		$defaults[CourseTypeLayout :: PROPERTY_STUDENT_VIEW] = $course_type_id?$student_view:1;
		
		$student_view_fixed = $course_type->get_layout()->get_student_view_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_STUDENT_VIEW_FIXED] = $student_view_fixed;

		$layout = $course_type->get_layout()->get_layout();
		$defaults[CourseTypeLayout :: PROPERTY_LAYOUT] = $layout ? $layout : PlatformSetting :: get('default_course_layout', WeblcmsManager :: APPLICATION_NAME);

		$tool_shortcut = $course_type->get_layout()->get_tool_shortcut();
		$defaults[CourseTypeLayout :: PROPERTY_TOOL_SHORTCUT] = $tool_shortcut ? $tool_shortcut : PlatformSetting :: get('default_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME);

		$menu = $course_type->get_layout()->get_menu();
		$defaults[CourseTypeLayout :: PROPERTY_MENU] = $menu ? $menu : PlatformSetting :: get('default_course_menu_selection', WeblcmsManager :: APPLICATION_NAME);

		$breadcrumb = $course_type->get_layout()->get_breadcrumb();
		$defaults[CourseTypeLayout :: PROPERTY_BREADCRUMB] = $breadcrumb ? $breadcrumb : PlatformSetting :: get('default_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME);

		$feedback = $course_type->get_layout()->get_feedback();
		$defaults[CourseTypeLayout :: PROPERTY_FEEDBACK] = $course_type_id?$feedback:1;

		$enable_introduction_text = $course_type->get_layout()->get_intro_text();
		$defaults[CourseTypeLayout :: PROPERTY_INTRO_TEXT] = $course_type_id?$enable_introduction_text:1;

		$course_code_visible = $course_type->get_layout()->get_course_code_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE] = $course_type_id?$course_code_visible:1;

		$course_manager_name_visible = $course_type->get_layout()->get_course_manager_name_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE] = $course_type_id?$course_manager_name_visible:1;

		$course_languages_visible = $course_type->get_layout()->get_course_languages_visible();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE] = $course_type_id?$course_languages_visible:1;
		
		$feedback_fixed = $course_type->get_layout()->get_feedback_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_FEEDBACK_FIXED] = $feedback;
		
		$enable_introduction_text_fixed = $course_type->get_layout()->get_intro_text_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_INTRO_TEXT_FIXED] = $enable_introduction_text_fixed;

		$course_code_visible_fixed = $course_type->get_layout()->get_course_code_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_CODE_VISIBLE_FIXED] = $course_code_visible_fixed;

		$course_manager_name_visible_fixed = $course_type->get_layout()->get_course_manager_name_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED] = $course_manager_name_visible_fixed;

		$course_languages_visible_fixed = $course_type->get_layout()->get_course_languages_visible_fixed();
		$defaults[CourseTypeLayout :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED] = $course_languages_visible_fixed;

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
}
?>