<?php
require_once dirname(__FILE__) . '/../evaluation_manager/evaluation_manager.class.php';
class EvaluationForm extends FormValidator
{
	const PROPERTY_FORMAT_LIST = 'format_list';
	const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PARAM_SCORE = 'score';
    const PARAM_DESCRIPTION = 'description';
	
    private $publication;
    private $user;

    function EvaluationForm($form_type, $publication, $action, $user)
    {
    	parent :: __construct('evaluation_publication_settings', 'post', $action);
    	
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

        $this->setDefaults();
    }
    
    function build_basic_form()
    {
        $attributes = array();
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
		
        $formats = GradebookDataManager :: get_instance()->retrieve_all_active_evaluation_formats();
		while($format = $formats->next_result())
		{
			$formats_array[$format->get_id()] = $format->get_title();
		}
		$this->addElement('select', self :: PROPERTY_FORMAT_LIST ,Translation :: get('EvaluationFormat'), $formats_array);
		$this->add_textfield('score','score');
		$this->add_html_editor(GradeEvaluation :: PROPERTY_COMMENT, Translation :: get(get_class($this) . 'Comment'), $required, $htmleditor_options);
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
		$evaluation = new Evaluation();
		$evaluation->set_evaluator_id($this->user->get_id());
		$evaluation->set_user_id($this->publication->get_publisher());
		$evaluation->set_evaluation_date(Utilities :: to_db_date(time()));		
		$evaluation->set_format_id($values['format_list']);
		$evaluation->create();
		
		$internal_item_instance = new InternalItemInstance();
		$internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($this->publication->get_content_object()->get_type(), $this->publication->get_id())->get_id());
		$internal_item_instance->set_evaluation_id($evaluation->get_id());
		$internal_item_instance->create();
		
		$grade_evaluation = new GradeEvaluation();
		$grade_evaluation->set_score($values['score']);
		$grade_evaluation->set_comment($values['comment']);
		$grade_evaluation->create();
	}
}
?>