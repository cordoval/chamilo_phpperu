<?php
class ExternalGradeEvaluationInputForm extends FormValidator
{
	const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $number_of_evaluations = 0;
	private $course_id;
	private $users;
	private $evaluation_format;
	function ExternalGradeEvaluationInputForm($form_type, $action, $category, $user, $values)
	{
		parent :: __construct('external_grade_evaluation_input_settings', 'post', $action);
		dump($values);
		$format = EvaluationManager :: retrieve_evaluation_format($values['format_id']);
		$this->evaluation_format = EvaluationFormat :: factory($format->get_title());
		$this->course_id = preg_replace("/[^0-9]/", '', $category);
		$users_ids = $values['target_elements']['user'];
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
		else
		{
			$this->build_basic_form();
		}
    	
    	$this->setDefaults();
	}
	function build_basic_form()
	{
		$this->addElement('category', Translation :: get('ExternalGradeProperties'));
		$this->grade_input_fields();
		$this->addElement('category');
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
			
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    	dump($this->users);
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
	    		$score_rule = new ValidateScoreStepRule($this->evaluation_format);
	    		$boundaries_rule = new ValidateScoreBoundariesRule($this->evaluation_format);
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
	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('ValueShouldBeNumeric'), 'numeric');
	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('DecimalValueNotAllowed'), $score_rule);
	            $rule[$this->evaluation_format->get_evaluation_field_name() . $this->number_of_evaluations][] = array(Translation :: get('ScoreIsOutsideBoundaries'), $boundaries_rule);
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('DecimalValueNotAllowed'), $score_rule))));
//	            $this->addGroupRule('grades', array($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations => array(array(Translation :: get('ScoreIsOutsideBoundaries'), $boundaries_rule))));
	            $this->addGroupRule('grades', $rule);
	            $this->number_of_evaluations++;
//				$this->addRule($this->evaluation_format->get_evaluation_field_name() . $number_of_evaluations, Translation :: get('ThisFieldIsRequired'), 'required');
	    	}
	    	else
	    	{
	    		$score_set = $this->evaluation_format->get_score_set();
	    		$score_set['no_evaluation'] = Translation :: get('NoEvaluation');
	    		$group[] = $this->createElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name(). $number_of_evaluations, null, $score_set);
	    		$group[] = $this->createElement('text', GradeEvaluation :: PROPERTY_COMMENT . $number_of_evaluations, Translation :: get('Comment'), false);
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