<?php
require_once dirname(__FILE__) . '/../evaluation_manager/evaluation_manager.class.php';
require_once dirname(__FILE__) . '/../evaluation_format/evaluation_format.class.php';

require_once dirname(__FILE__) . '/../../../../plugin/pear/HTML/QuickForm/Rule.php';

class EvaluationForm extends FormValidator
{
	const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PARAM_SCORE = 'score';
    const PARAM_COMMENT = 'comment';
	
    private $publication_id;
    private $user;
    private $grade_evaluation;
    private $evaluation;
    private $form_type;
    private $publisher_id;
    private $evaluation_format;
    
    private $allow_creation = false;

    function EvaluationForm($form_type, $evaluation, $grade_evaluation, $publication_id, $publisher_id,$action, $user)
    {
    	parent :: __construct('evaluation_publication_settings', 'post', $action);
    	$this->evaluation = $evaluation;
    	$this->grade_evaluation = $grade_evaluation;
    	$this->publication_id = $publication_id;
        $this->user = $user;
        $this->form_type = $form_type;
        $this->publisher_id = $publisher_id;
		if($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_basic_creation_form();
		}
		else
		{
	    	$this->addElement('category', Translation :: get('EvaluationProperties'));
			$this->build_evaluation_format_element();
			$this->build_editing_form();
	    	$this->addElement('category');
		}
		$this->setEvaluationDefaults();
    }
    
    function set_allow_creation($value)
    {
    	$this->allow_creation = $value;
    }
    
    function is_creation_allowed()
    {
    	return $this->allow_creation;
    }
    
    function build_basic_form()
    {
        /*$attributes = array();
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();*/
//	    $select->setSelected($this->evaluation->get_format_id());
    	
    	$values = $this->getSubmitValues();
    	$format = EvaluationManager :: retrieve_evaluation_format($values['format_id']);
    	if(!$format)
    		$format = EvaluationManager :: retrieve_evaluation_format($this->evaluation->get_format_id());
    	$this->evaluation_format = EvaluationFormat :: factory(EvaluationFormat :: get_folder($format->get_title()),EvaluationFormat :: name_to_underscore($format->get_title()).'.class.php');
    	if (!$this->evaluation_format->get_score_set())
    	{
    		if(!is_numeric($values[$this->evaluation_format->get_evaluation_field_name()]))
    			$this->grade_evaluation->set_score(null);
            $this->addElement('static', null, null, '<em>' . $this->evaluation_format->get_score_information() . '</em>');
    		$this->addElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name(), Translation :: get('score'));
            $this->addRule($this->evaluation_format->get_evaluation_field_name(), Translation :: get('ValueShouldBeNumeric'), 'numeric');
			$this->addRule($this->evaluation_format->get_evaluation_field_name(), Translation :: get('ScoreIsNotAValidValue'), new ValidateEvaluationScoreRule($this->evaluation_format));
    	}
    	else
    	{
    		$this->addElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name(), Translation :: get('score'), $this->evaluation_format->get_score_set());
    	}
		$this->addRule($this->evaluation_format->get_evaluation_field_name(), Translation :: get('ThisFieldIsRequired'), 'required');
		$this->add_html_editor(GradeEvaluation :: PROPERTY_COMMENT, Translation :: get('Comment'), false);
    }
    
    function build_evaluation_format_element()
    {
    	if ($this->form_type == self :: TYPE_CREATE)
			$formats_array[0] = Translation :: get('ChooseEvaluationFormat');
        $formats = EvaluationManager :: retrieve_all_active_evaluation_formats();
		while($format = $formats->next_result())
		{
			$formats_array[$format->get_id()] = ucfirst($format->get_title());
		}
		$select = $this->add_select(Evaluation :: PROPERTY_FORMAT_ID, Translation :: get('EvaluationFormat'), $formats_array, false, array('class' => 'change_evaluation_format'));
		$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/change_evaluation_format.js'));
	    $this->addElement('style_submit_button', 'select_format', Translation :: get('Formatter'), array('class' => 'normal filter'));
    }
    
    function build_basic_creation_form()
    {
	    $this->addElement('category', Translation :: get('EvaluationProperties'));
	    $this->build_evaluation_format_element();
    	$values = $this->getSubmitValues();
		if($values['format_id'] > 0)
		{
			$this->build_creation_form();
		}	
	    $this->addElement('category');
    }
    
    function build_editing_form()
    {
        $this->build_basic_form();

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
//    
//    static function create_internal_item($publication)
//    {
//    	$paramaters['publication'] = $publication;
//    	$evaluation_manager = new EvaluationManager($this, EvaluationManager :: ACTION_CREATE_INTERNAL_ITEM, $parameters);
//    }
	function create_evaluation()
	{
		$export_values = $this->exportValues();
		$submit_values = $this->getSubmitValues();
		$evaluation_succes = false;
		$internal_item_instancr_succes = false;
		$grade_evaluation_succes = false;
		
		$evaluation = $this->evaluation;
		$evaluation->set_evaluator_id($this->user->get_id());
		$evaluation->set_user_id($this->publisher_id);
		$evaluation->set_evaluation_date(Utilities :: to_db_date(time()));		
		$evaluation->set_format_id($export_values['format_id']);
		if($evaluation->create())
		{
			$evaluation_succes = true;
		}
		
		$internal_item_instance = new InternalItemInstance();
		$internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication(Request :: get('application'), $this->publication_id)->get_id());
		$internal_item_instance->set_evaluation_id($evaluation->get_id());
		if($internal_item_instance->create())
		{
			$internal_item_instancr_succes = true;
		}
    	
		$grade_evaluation = $this->grade_evaluation;
		$grade_evaluation->set_score($submit_values[$this->evaluation_format->get_evaluation_field_name()]);
		$grade_evaluation->set_comment($submit_values['comment']);
		$grade_evaluation->set_id($evaluation->get_id());
		if($grade_evaluation->create(false))
		{
			$grade_evaluation_succes = true;
		}
		if($evaluation && $internal_item_instance && $grade_evaluation)
		{
			return true;
		}
		return false;
	}
	
	function update_evaluation($evaluation_id)
	{
		$values = $this->exportValues();
		$evaluation = $this->evaluation;
		$evaluation->set_id($evaluation_id);
		$evaluation->set_evaluator_id($this->user->get_id());
		$evaluation->set_user_id($this->publisher_id);
		$evaluation->set_evaluation_date(Utilities :: to_db_date(time()));		
		$evaluation->set_format_id($values['format_id']);
		if(!$evaluation->update())
		{
			return false;
		}
		
		$internal_item_instance = new InternalItemInstance();
		$internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication(Request :: get('application'), $this->publication_id)->get_id());
		$internal_item_instance->set_evaluation_id($evaluation_id);
		if(!$internal_item_instance->update())
		{
			return false;
		}
		
		$grade_evaluation = $this->grade_evaluation;
		$grade_evaluation->set_score($values[$this->evaluation_format->get_evaluation_field_name()]);
		$grade_evaluation->set_comment($values['comment']);
		$grade_evaluation->set_id($evaluation->get_id());
		if(!$grade_evaluation->update())
		{
			return false;
		}
		
		return true;
	}
    // Default values (setter)
    
	function setEvaluationDefaults($set_score_null_after_format_switch = false, $defaults = array ())
	{
		$grade_evaluation = $this->grade_evaluation;
		$evaluation = $this->evaluation;
		if ($grade_evaluation->get_id())
		{
			if(!$set_score_null_after_format_switch)
				$defaults[$this->evaluation_format->get_evaluation_field_name()] = $grade_evaluation->get_score();
			else
			{
				$defaults[$this->evaluation_format->get_evaluation_field_name()] = null;
			}
		    $defaults[GradeEvaluation :: PROPERTY_COMMENT] = $grade_evaluation->get_comment();
		    $defaults[GradeEvaluation :: PROPERTY_ID] = $grade_evaluation->get_id();
	
		    $defaults[Evaluation :: PROPERTY_FORMAT_ID] = $evaluation->get_format_id();
		    $defaults[Evaluation :: PROPERTY_EVALUATION_DATE] = $evaluation->get_evaluation_date();
		    $defaults[Evaluation :: PROPERTY_USER_ID] = $evaluation->get_user_id();
		    $defaults[Evaluation :: PROPERTY_EVALUATOR_ID] = $evaluation->get_evaluator_id();
			parent :: setDefaults($defaults);
		}
	}
	
	function validate()
	{
		$values = $this->getSubmitValues();
        if ($values['submit'])
        {
	        $this->setEvaluationDefaults();
        	return parent :: validate();
        }
//        elseif($this->evaluation_format->get_evaluation_field_type() == 'text')
//        {
//        	$this->setEvaluationDefaults(true);
//        	return false;
//        }
	}
}

class ValidateEvaluationScoreRule extends HTML_QuickForm_Rule
{
	private $evaluation_format;
	
	function ValidateEvaluationScoreRule($evaluation_format)
	{
		$this->evaluation_format = $evaluation_format;
	}
	
	public function validate($evaluation_score)
	{
		$quotient = intval($evaluation_score / $this->evaluation_format->get_step());
		$mod = $evaluation_score - $quotient * $this->evaluation_format->get_step();
		if($evaluation_score < $this->evaluation_format->get_min_value() || $evaluation_score > $this->evaluation_format->get_max_value() || $mod != 0)
			return false;
		return true;
	}	
}
?>