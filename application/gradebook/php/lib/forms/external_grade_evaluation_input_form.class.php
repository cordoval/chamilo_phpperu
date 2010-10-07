<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'external_item.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_format/evaluation_format.class.php';
require_once Path :: get_plugin_path(). 'pear/HTML/QuickForm/Rule.php';
require_once WebApplication :: get_application_class_lib_path('weblcms') . 'course/course_user_relation.class.php';
require_once WebApplication :: get_application_class_lib_path('weblcms') . 'weblcms_manager/weblcms_manager.class.php';
class ExternalGradeEvaluationInputForm extends FormValidator
{
	const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $number_of_evaluations = 0;
	private $course_id;
	private $users;
	private $evaluation_format;
	private $form_type;
	private $description;
	private $title;
	private $format_id;
	private $user;
	
	function ExternalGradeEvaluationInputForm($form_type, $action, $category, $user, $values = null)
	{
		parent :: __construct('external_grade_evaluation_input_settings', 'post', $action);
		$this->description = $values['description'];
		$this->title = $values['title'];
		$this->form_type = $form_type;
		$this->format_id = $values['format_id'];
		$this->user = $user;
		if($this->format_id)
		{
			$format = EvaluationManager :: retrieve_evaluation_format($values['format_id']);
			$this->evaluation_format = EvaluationFormat :: factory($format->get_title());
			$users_ids = $values['target_elements']['user'];
		}
		$this->course_id = preg_replace("/[^0-9]/", '', $category);
		
		if($users_ids)
    	{
    		$this->users = $users_ids;
    	}
    	else
    	{
    		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->course_id);
    		$users_relations = WeblcmsManager :: retrieve_course_user_relations($condition);
	    	while($user = $users_relations->next_result())
	    	{
	    		$this->users[] = $user->get_user();
	    	}
    	}
		if($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_basic_form();
		}
		elseif($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_basic_form();
		}
    	
    	$this->setDefaults();
	}
	function build_basic_form()
	{
		$this->addElement('category', Translation :: get('ExternalGradeInput'));
		$this->grade_input_fields();
		$this->addElement('category');
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
			
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
	function grade_input_fields()
	{
		$udm = UserDataManager :: get_instance();
		if($this->evaluation_format->get_score_information())
    	{
    		$this->addElement('static', null, null, '<em>' . $this->evaluation_format->get_score_information() . '</em>');
    	}
    	$this->addElement('static', null, null, '<em>Score - Comment</em>');
		foreach($this->users as $user)
		{
			$group = array();
			$username = $udm->retrieve_user($user)->get_fullname();
			if (!$this->evaluation_format->get_score_set())
	    	{
//	    		$score_rule = new ValidateScoreStepRule($this->evaluation_format);
//	    		$boundaries_rule = new ValidateScoreBoundariesRule($this->evaluation_format);
//	    		if(!empty($values))
//	    		{
//		    		if(!$score_rule->validate($values[$this->evaluation_format->get_evaluation_field_name()]) || !$boundaries_rule->validate($values[$this->evaluation_format->get_evaluation_field_name()]) || !is_numeric($values[$this->evaluation_format->get_evaluation_field_name()]))
//		    		{
//		    			$this->grade_evaluation->set_score(null);
//		    		}
//	    		}
				
	    		$group[] = $this->createElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations, false,  array('size' => '4'));
	            $group[] = $this->createElement('text', GradeEvaluation :: PROPERTY_COMMENT . $this->number_of_evaluations, Translation :: get('Comment'), false);
	            $this->addGroup($group, 'grades', $username, null, false);
//	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('ValueShouldBeNumeric'), 'numeric');
//	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('DecimalValueNotAllowed'), $score_rule);
//	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('ScoreIsOutsideBoundaries'), $boundaries_rule);
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('DecimalValueNotAllowed'), $score_rule))));
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('ScoreIsOutsideBoundaries'), $boundaries_rule))));
//				$this->addGroupRule('grades', $rule);
//	            $this->addGroupRule('grades', array(
//	            	$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations => array(
//	            	array(Translation :: get('ValueShouldBeNumeric'), 'numeric'),
//	            	array(Translation :: get('DecimalValueNotAllowed'), $score_rule),
//	            	array(Translation :: get('ScoreIsOutsideBoundaries'), $boundaries_rule)
//	            	)
//	            	));
	            $this->number_of_evaluations++;
//				$this->addRule($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations, Translation :: get('ThisFieldIsRequired'), 'required');
//	    	$idGrp[] = &HTML_QuickForm::createElement('text', 'lastname', 'Name', array('size' => 30));
//			$idGrp[] = &HTML_QuickForm::createElement('text', 'code', 'Code', array('size' => 5, 'maxlength' => 4));
//			$form->addGroup($idGrp, 'id', 'ID:', ',&nbsp');
//			// Complex rule for group's elements
//			$form->addGroupRule('id', array(
//			    'lastname' => array(
//			        array('Name is letters only', 'lettersonly'),
//			        array('Name is required', 'required')
//			    ),
//			    'code' => array(
//			        array('Code must be numeric', 'numeric')
//			    )
//			));
	    	}
	    	else
	    	{
	    		$score_set = $this->evaluation_format->get_score_set();
	    		$score_set['no_evaluation'] = Translation :: get('NoEvaluation');
	    		$group[] = $this->createElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations, null, $score_set);
	    		$group[] = $this->createElement('text', GradeEvaluation :: PROPERTY_COMMENT . $this->number_of_evaluations, Translation :: get('Comment'), false);
	    		$this->addGroup($group, 'grades', $username, null, false);
	    		$this->number_of_evaluations++;
//	    		$this->addRule($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations, Translation :: get('ThisFieldIsRequired'), 'required');
	    	}
		}
//		dump($groups);
//    	foreach($groups as $group)
//    	{
//    		dump($group);
//	    	$this->addGroup($group, 'grades', $username, null, false);
//	    	$this->addGroupRule('grades', $rule);
//    	}
	}
	function create_evaluation()
	{
		$export_values = $this->exportValues();
		$external_item = new ExternalItem();
		$external_item->set_title($this->title);
		$external_item->set_description($this->description);
		if(!$this->course_id)
			$external_item->set_category(null);
		else
			$external_item->set_category('C' . $this->course_id);
		if(!$external_item->create())
		{
			return false;
		}
		for($i=0;$i<$this->number_of_evaluations;$i++)
		{
			if(!$export_values[$this->evaluation_format->get_evaluation_field_name() . $i] == 'no_evaluation' || !$export_values[$this->evaluation_format->get_evaluation_field_name() . $i] == null)
			{
				$evaluation = new Evaluation();
				$evaluation->set_evaluator_id($this->user->get_id());
				$evaluation->set_user_id($this->users[$i]);
				$evaluation->set_evaluation_date(time());		
				$evaluation->set_format_id($this->format_id);
				if(!$evaluation->create())
				{
					return false;
				}
				
				$external_item_instance = new ExternalItemInstance();
				$external_item_instance->set_external_item_id($external_item->get_id());
				$external_item_instance->set_evaluation_id($evaluation->get_id());
				if(!$external_item_instance->create())
				{
					return false;
				}
		    	
				$grade_evaluation = new GradeEvaluation();
				$grade_evaluation->set_score($export_values[$this->evaluation_format->get_evaluation_field_name() . $i]);
				$grade_evaluation->set_comment($export_values['comment' . $i]);
				$grade_evaluation->set_id($evaluation->get_id());
				if(!$grade_evaluation->create(false))
				{
					return false;
				}
			}
		}
		return true;
	}
	function validate()
	{
		$export_values = $this->exportValues();
		if($export_values && !$this->evaluation_format->get_score_set())
		{
			$failures = 0;
			$score_rule = new ValidateScoreStepRule($this->evaluation_format);
		    $boundaries_rule = new ValidateScoreBoundariesRule($this->evaluation_format);
			for($i=0;$i<$this->number_of_evaluations;$i++)
			{
				if(!$score_rule->validate($export_values['points_evaluation'.$i]))
				{
					$failures++;
				}
				if(!$boundaries_rule->validate($export_values['points_evaluation'.$i]))
				{
					$failures++;
				}
			}
			if($failures == 0)
			{
				return parent :: validate();
			}
		}
	}
}

class ValidateScoreStepRule extends HTML_QuickForm_Rule
{
	private $evaluation_format;
	
	function ValidateScoreStepRule($evaluation_format)
	{
		$this->evaluation_format = $evaluation_format;
	}
	
	public function validate($evaluation_score)
	{
		$quotient = intval($evaluation_score / $this->evaluation_format->get_step());
		$mod = $evaluation_score - $quotient * $this->evaluation_format->get_step();
		if($mod != 0)
			return false;
		return true;
	}	
}

class ValidateScoreBoundariesRule extends HTML_QuickForm_Rule
{
	private $evaluation_format;
	
	function ValidateScoreBoundariesRule($evaluation_format)
	{
		$this->evaluation_format = $evaluation_format;
	}
	
	public function validate($evaluation_score)
	{
		if($evaluation_score < $this->evaluation_format->get_min_value() || $evaluation_score > $this->evaluation_format->get_max_value())
			return false;
		return true;
	}
}
?>