<?php
require_once dirname(__FILE__) . '/../competency.class.php';
/**
 * This class describes a CompetencyForm object
 * 
 * @author Nick Van Loocke
 **/
class CompetencyForm extends FormValidator
{
	
	const TYPE_CREATOR_COMPETENCY = 1;
	const TYPE_EDITOR_COMPETENCY = 2;

	private $competency;
	private $user;
	private $owner_id;

    function CompetencyForm($form_type, $competency, $action, $user)
    {
    	parent :: __construct('competency_settings', 'post', $action);

    	$this->competency = $competency;
    	$this->user = $user;
		$this->form_type = $form_type;
		$this->owner_id = $competency->get_owner_id();

		if ($this->form_type == self :: TYPE_CREATOR_COMPETENCY)
		{
			$this->build_creator_competency_form();
			$this->setCompetencyDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_COMPETENCY)
		{
			$this->build_editor_competency_form();
			$this->setCompetencyDefaults();
		}

    }
    
    
    //Forms

	function build_creator_competency_form()
    {
    	$this->addElement('text', Competency :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Competency :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Competency :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Competency :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
    	$this->addElement('select', Competency :: PROPERTY_CATEGORY, Translation :: get('SelectCategory'), $this->categories);
        $this->addRule(Competency :: PROPERTY_CATEGORY, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_competency_form()
    {
    	$this->addElement('text', Competency :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Competency :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Competency :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Competency :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    
	function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(CompetencyCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(CompetencyCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_competency_categories($condition);
        while ($competency = $cdm->next_result())
        {
            $this->categories[$competency->get_id()] = str_repeat('--', $level) . ' ' . $competency->get_name();
            $this->retrieve_categories_recursive($competency->get_id(), $exclude_category, ($level + 1));
        }
    }
    
	/**
     * Returns the ID of the owner of the CBA object being created or edited.
     * @return int The ID.
     */
    protected function get_owner_id()
    {
        return $this->owner_id;
    }
    
   
    // Create and Update functions (Competency)
    
	function create_competency()
    {
    	$competency = $this->competency;
    	$competency->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $form->exportValue(Competency :: PROPERTY_CATEGORY);
    	
    	$competency->set_title($values[Competency :: PROPERTY_TITLE]);
    	$competency->set_description($values[Competency :: PROPERTY_DESCRIPTION]);      
    	$competency->move($parent);

   		return $competency->create();
    }
    
	function update_competency()
    {
    	$competency = $this->competency;
    	$values = $this->exportValues();

    	$competency->set_title($values[Competency :: PROPERTY_TITLE]);
    	$competency->set_description($values[Competency :: PROPERTY_DESCRIPTION]);

    	return $competency->update();
    }
    
	
    // Default values (setter)
    
	function setCompetencyDefaults($defaults = array ())
	{
		$competency = $this->competency;

		$defaults[Competency :: PROPERTY_ID] = $competency->get_id();
    	$defaults[Competency :: PROPERTY_TITLE] = $competency->get_title();
    	$defaults[Competency :: PROPERTY_DESCRIPTION] = $competency->get_description();
    	
		parent :: setDefaults($defaults);
	}
	
}
?>