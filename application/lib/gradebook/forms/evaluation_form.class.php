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
		
        $formats = GradebookDatamanager :: get_instance()->retrieve_all_active_evaluation_formats();
		while($format = $formats->next_result())
		{
			$formats_array[$format->get_id()] = $format->get_title();
		}
		$this->addElement('select', self :: PROPERTY_FORMAT_LIST ,Translation :: get('EvaluationFormat'), $formats_array);
		$this->add_textfield(PARAM_SCORE,'score');
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
    
    static function create_internal_item($publication)
    {
    	$paramaters['publication'] = $publication;
    	$evaluation_manager = new EvaluationManager($this, EvaluationManager :: ACTION_CREATE_INTERNAL_ITEM, $parameters);
    }
}
?>