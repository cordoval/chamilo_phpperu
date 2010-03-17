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
    	
		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
    	$this->addElement('select', Criteria :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        $this->addRule(Criteria :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->extra_criteria_form();
		
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
    
	function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(CriteriaCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(CriteriaCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_criteria_categories($condition);
        while ($criteria = $cdm->next_result())
        {
            $this->categories[$criteria->get_id()] = str_repeat('--', $level) . ' ' . $criteria->get_name();
            $this->retrieve_categories_recursive($criteria->get_id(), $exclude_category, ($level + 1));
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
    
    
    // Create and Update functions (Criteria)
    
	function create_criteria()
    {
    	$criteria = $this->criteria;
    	$criteria->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $this->exportValue(Criteria :: PROPERTY_PARENT_ID);
    	
    	
    	$criteria->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$criteria->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);
    	$criteria->move($parent);

   		return $criteria->create();
    }
    
	function update_criteria()
    {
    	$criteria = $this->criteria;
    	$criteria->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $this->exportValue(Criteria :: PROPERTY_PARENT_ID);

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
	
	
	
	// Extra
	
	/*private function extra_criteria_form()
    {
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->add_name_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('CategoryName'), '', false);
                $this->addRule(PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
            	
            }
        }
        
        $this->addElement('image', 'add[]', Theme :: get_common_image_path() . 'action_list_add.png', array('style="border: 0px;"'));
        //$this->build_footer('Create');
    }
    
	function add_name_field($number = null)
    {
        $element = $this->createElement('text', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('Name'), array("size" => "50"));
        return $element;
    }*/
}
?>