<?php
require_once dirname(__FILE__) . '/../evaluation_manager/evaluation_manager.class.php';

class EvaluationForm extends FormValidator
{
	const PARAM_FORMAT_LIST = 'format_list';
	const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PARAM_SCORE = 'score';
    const PARAM_COMMENT = 'comment';
	
    private $publication;
    private $user;
    private $grade_evaluation;
    private $evaluation;

    function EvaluationForm($form_type, $evaluation, $grade_evaluation, $publication, $action, $user)
    {
    	parent :: __construct('evaluation_publication_settings', 'post', $action);
    	
    	$this->evaluation = $evaluation;
    	$this->grade_evaluation = $grade_evaluation;
    	$this->publication = $publication;
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
		$this->setEvaluationDefaults();
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
        $formats = GradebookDataManager :: get_instance()->retrieve_all_active_evaluation_formats();
		while($format = $formats->next_result())
		{
			$formats_array[$format->get_id()] = $format->get_title();
		}
		$select = $this->add_select(Evaluation :: PROPERTY_FORMAT_ID, Translation :: get('EvaluationFormat'), $formats_array);
        $select->setSelected($this->evaluation->get_format_id());
        
        
       // dump($this->evaluation->get_format_id() = $formats_array[$this->evaluation->get_format_id()]);exit;
        
		$this->add_textfield(GradeEvaluation :: PROPERTY_SCORE, Translation :: get('EvaluationScore'), true);
		$this->add_html_editor(GradeEvaluation :: PROPERTY_COMMENT, Translation :: get('Comment'), true);
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
		$values = $this->exportValues();
		
		$evaluation = $this->evaluation;
		$evaluation->set_evaluator_id($this->user->get_id());
		$evaluation->set_user_id($this->publication->get_publisher());
		$evaluation->set_evaluation_date(Utilities :: to_db_date(time()));		
		$evaluation->set_format_id($values['format_id']);
		if(!$evaluation->create())
		{
			return false;
		}
		
		$internal_item_instance = new InternalItemInstance();
		$internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($this->publication->get_content_object()->get_type(), $this->publication->get_id())->get_id());
		$internal_item_instance->set_evaluation_id($evaluation->get_id());
		if(!$internal_item_instance->create())
		{
			return false;
		}
		
		$grade_evaluation = $this->grade_evaluation;
		$grade_evaluation->set_score($values['score']);
		$grade_evaluation->set_comment($values['comment']);
		$grade_evaluation->set_id($evaluation->get_id());
		if(!$grade_evaluation->create())
		{
			return false;
		}
		$this->setEvaluationDefaults();
		return true;
	}
	
	function update_evaluation($evaluation_id)
	{
		$values = $this->exportValues();
		$evaluation = $this->evaluation;
		$evaluation->set_id($evaluation_id);
		$evaluation->set_evaluator_id($this->user->get_id());
		$evaluation->set_user_id($this->publication->get_publisher());
		$evaluation->set_evaluation_date(Utilities :: to_db_date(time()));		
		$evaluation->set_format_id($values['format_id']);
		if(!$evaluation->update())
		{
			return false;
		}
		
		$internal_item_instance = new InternalItemInstance();
		$internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($this->publication->get_content_object()->get_type(), $this->publication->get_id())->get_id());
		$internal_item_instance->set_evaluation_id($evaluation_id);
		if(!$internal_item_instance->update())
		{
			return false;
		}
		
		$grade_evaluation = $this->grade_evaluation;
		$grade_evaluation->set_score($values['score']);
		$grade_evaluation->set_comment($values['comment']);
		$grade_evaluation->set_id($evaluation->get_id());
		if(!$grade_evaluation->update())
		{
			return false;
		}
		
		return true;
	}
    // Default values (setter)
    
	function setEvaluationDefaults($defaults = array ())
	{
		
		$grade_evaluation = $this->grade_evaluation;
		$evaluation = $this->evaluation;
		$defaults[GradeEvaluation :: PROPERTY_SCORE] = $grade_evaluation->get_score();
	    $defaults[GradeEvaluation :: PROPERTY_COMMENT] = $grade_evaluation->get_comment();
	    $defaults[GradeEvaluation :: PROPERTY_ID] = $grade_evaluation->get_id();

	    $defaults[Evaluation :: PROPERTY_FORMAT_ID] = $evaluation->get_format_id();
	    $defaults[Evaluation :: PROPERTY_EVALUATION_DATE] = $evaluation->get_evaluation_date();
	    $defaults[Evaluation :: PROPERTY_USER_ID] = $evaluation->get_user_id();
	    $defaults[Evaluation :: PROPERTY_EVALUATOR_ID] = $evaluation->get_evaluator_id();
	    
		parent :: setDefaults($defaults);
		
	}
}
?>