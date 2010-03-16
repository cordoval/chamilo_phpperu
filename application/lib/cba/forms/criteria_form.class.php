<?php
require_once dirname(__FILE__) . '/../criteria.class.php';
/**
 * This class describes a CriteriaForm object
 * 
 * @author Nick Van Loocke
 **/
class CriteriaForm extends FormValidator
{
	const TYPE_CREATOR_CRITERIA = 1;
	const TYPE_EDITOR_CRITERIA = 2;

	private $criteria;
	private $user;
	private $owner_id;

    function CriteriaForm($form_type, $criteria, $action, $user)
    {
    	parent :: __construct('criteria_settings', 'post', $action);

    	$this->criteria = $criteria;
    	$this->user = $user;
		$this->form_type = $form_type;
		$this->owner_id = $criteria->get_owner_id();

		if ($this->form_type == self :: TYPE_CREATOR_CRITERIA)
		{
			$this->build_creator_criteria_form();
			$this->setCriteriaDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_CRITERIA)
		{
			$this->build_editor_criteria_form();
			$this->setCriteriaDefaults();
		}

    }
    

    // Forms
    
	function build_creator_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    
	/**
     * Returns the ID of the owner of the CBA object being created or edited.
     * @return int The ID.
     */
    protected function get_owner_id()
    {
        return $this->owner_id;
    }
    
    
    // Create and Update functions (Criteria)
    
	function create_criteria()
    {
    	$criteria = $this->criteria;
    	$criteria->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	
    	$criteria->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$criteria->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);

   		return $criteria->create();
    }
    
	function update_criteria()
    {
    	$criteria = $this->criteria;
    	$values = $this->exportValues();

    	$criteria->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$criteria->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);

    	return $criteria->update();
    }

    
	// Default values (setter)
	
	function setCriteriaDefaults($defaults = array ())
	{
		$criteria = $this->criteria;

		$defaults[Criteria :: PROPERTY_ID] = $criteria->get_id();
    	$defaults[Criteria :: PROPERTY_TITLE] = $criteria->get_title();
    	$defaults[Criteria :: PROPERTY_DESCRIPTION] = $criteria->get_description();

		parent :: setDefaults($defaults);
	}
}
?>