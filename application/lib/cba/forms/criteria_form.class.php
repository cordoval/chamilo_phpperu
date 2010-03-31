<?php
require_once dirname(__FILE__) . '/../criteria.class.php';
require_once dirname(__FILE__) . '/../criteria_score.class.php';
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
	private $criteria_score;
	private $user;
	private $owner_id;
	private $data_manager;
	
    function CriteriaForm($form_type, $criteria, $criteria_score, $action, $user)
    {
    	parent :: __construct('cba_settings', 'post', $action);

    	$this->criteria = $criteria;
    	$this->criteria_score = $criteria_score;
    	$this->user = $user;
		$this->form_type = $form_type;
		$this->owner_id = $criteria->get_owner_id();
		$this->data_manager = CbaDataManager :: get_instance();
		
		if ($this->form_type == self :: TYPE_CREATOR_CRITERIA)
		{
			$this->build_creator_criteria_form();
			$this->setCriteriaDefaults();
			$this->setCriteriaScoreDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_CRITERIA)
		{
			$this->build_editor_criteria_form();
			$this->setCriteriaDefaults();
			$this->setCriteriaScoreDefaults();
		}

    }
    

    // Forms
    
	function build_creator_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
		$select = $this->add_select(Criteria :: PROPERTY_PARENT_ID, Translation :: get('Category'), $this->categories);
        $category_id = Request :: get(CbaManager :: PARAM_CATEGORY_ID);
		$select->setSelected($category_id);
    	$this->addRule(Criteria :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		     
        $this->criteria_score_form();
        
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
        $select = $this->add_select(Criteria :: PROPERTY_PARENT_ID, Translation :: get('Category'), $this->categories);
        $select->setSelected($this->criteria->get_parent_id());
    	$this->addRule(Criteria :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->criteria_score_form($count_links);
		
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
    
    function create_criteria_score()
    {
    	$criteria = $this->criteria;
    	$criteria_score = $this->criteria_score; 	
    	$criteria_score->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();	
    	
    	$result = true;

        foreach ($values as $key => $value)
        {     	
            if (strpos($key, 'description_score') !== false)
            {
            	$description_score = array();
            	$description_score[] = $value;    
            	$criteria_score->set_description_score($value);        	
            }
            
        	if(strpos($key, 'description_score') === false)
        	{
        		if(strpos($key, 'score') !== false)
        		{
	        	
	        		$criteria_score->set_criteria_id($criteria->get_id());
	                $criteria_score->set_score($value);
	                
	                $conditions = array();
					$conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $criteria->get_id());				
	                $conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_DESCRIPTION_SCORE, $criteria_score->get_description_score());
					$conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_SCORE, $criteria_score->get_score());
						
	                $condition = new AndCondition($conditions);
	                $cats = $this->data_manager->count_criterias_score($condition);
	                
	                if ($cats > 0)
	                {
	                    $result = false;
	                }
	                else
	                {
	                    $result &= $criteria_score->create();
	                }
        		}
        	}
        }
        return $result;
    }
    
    
	function update_criteria()
    {
    	$criteria = $this->criteria;
    	$criteria->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $this->exportValue(Criteria :: PROPERTY_PARENT_ID);

    	$criteria->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$criteria->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);
    	$criteria->move($parent);

    	return $criteria->update();
    }
    
	function update_criteria_score()
    {
    	$criteria = $this->criteria;
    	$criteria_score = $this->criteria_score;
    	$criteria_score->set_owner_id($this->get_owner_id());
        $values = $this->exportValues();

        
		$count = sizeof($criteria_score);
		for($i = 0; $i < $count; $i++)
		{	
    		$criteria_score->set_description_score($values[CriteriaScore :: PROPERTY_DESCRIPTION_SCORE .$i]);
    		$criteria_score->set_score($values[CriteriaScore :: PROPERTY_SCORE .$i]);
		}
        
        $conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $criteria->get_id());				
        $conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_DESCRIPTION_SCORE, $criteria_score->get_description_score());
		$conditions[] = new EqualityCondition(CriteriaScore :: PROPERTY_SCORE, $criteria_score->get_score());
						
        $condition = new AndCondition($conditions);
        $cats = $this->data_manager->count_criterias_score($condition);

        
        if ($cats > 0)
        {
            return false;
        }
        
        return $criteria_score->update();
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
	
	function setCriteriaScoreDefaults($defaults = array ())
	{
		$criteria = $this->criteria;
		
		$criteria_score = $this->criteria_score;		
		/*$values = $this->exportValues();
		$parent = $this->exportValue(CriteriaScore :: PROPERTY_PARENT_ID);
		
		foreach($values as $key => $value)
		{
			echo $key . ': ';
			echo $value;
			echo '<br/>';		
		}
		exit();*/

		$count = sizeof($criteria_score);				
		for($i = 0; $i < $count; $i++)
		{
			$defaults[CriteriaScore :: PROPERTY_DESCRIPTION_SCORE . $i] = $criteria_score->get_description_score();
			$defaults[CriteriaScore :: PROPERTY_SCORE . $i] = $criteria_score->get_score();
		}
		parent :: setDefaults($defaults);
	}
	
	
	
	// Dynamic form options
	
	function criteria_score_form($number_options)
    {
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        
        $_SESSION['mc_number_of_options'] = $number_options;
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 2;
        }
        else
        {
        	$_SESSION['mc_number_of_options'] = $number_options;
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
            $number_options++;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
        
		/*$table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th>' . Translation :: get('OmschrijvingScore') . '</th>';     
        $table_header[] = '<th>' .Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<body>';
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {           
        	if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
           	$group = array();
            
	        $table_header[] = '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">';      
	        $table_header[] = '<td>';
			$table_header[] = $option_number;
	        $group[] = $this->add_description_score_field($option_number);
	        $group[] = & $this->add_description_score_field($option_number);
	        //$tabel_header[] = $this->createElement('text', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('DescriptionScore'), array("size" => "70"));
	        
			$table_header[] = '</td>';
	        $table_header[] = '<td>test2</td>';
	        $table_header[] = '<td>';

	        $table_header[] = '</td>';
	        $table_header[] = '</tr>';
            }

        }
        $table_header[] = '<tr><td>';
        //$table_header[] = $this->add_name_field($option_number);   
        //$this->addGroup($group, PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('CategoryName'), '', false);
                
        $table_header[] = '</td><td></td><td></td></tr>';
        $table_header[] = '</body>';
        $table_header[] = '</table>';
        $this->addElement('html', implode("\n", $table_header));*/
        
        for($option_number = 0; $option_number < $number_of_options; $option_number++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
				$group[] = $this->add_option_number_field($option_number);
				$group[] = $this->add_description_score_field($option_number);
                $group[] = $this->add_score_field($option_number);

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                	if($option_number == 0)
                	{
                		$group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                		$group[] = $this->createElement('image', 'up[' . $option_number . ']', Theme :: get_common_image_path() . 'action_up_na.png', array('class' => 'up_option', 'id' => 'up_' . $option_number)); 
						$group[] = $this->createElement('image', 'down[' . $option_number . ']', Theme :: get_common_image_path() . 'action_down.png', array('class' => 'down_option', 'id' => 'down_' . $option_number)); 
                	}
                	elseif($option_number > 0)
                	{
                		$group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                		$group[] = $this->createElement('image', 'up[' . $option_number . ']', Theme :: get_common_image_path() . 'action_up.png', array('class' => 'up_option', 'id' => 'up_' . $option_number)); 

                		if($number_of_options == ($option_number + 1))
                		{
                			$group[] = $this->createElement('image', 'down[' . $option_number . ']', Theme :: get_common_image_path() . 'action_down_na.png', array('class' => 'down_option', 'id' => 'down_' . $option_number));               
                		}
                		else
                		{
                			$group[] = $this->createElement('image', 'down[' . $option_number . ']', Theme :: get_common_image_path() . 'action_down.png', array('class' => 'down_option', 'id' => 'down_' . $option_number));               
                		}
                	}
                }
                $this->addGroup($group, PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('CriteriaOptionNumber'), '&nbsp;', false);

                $this->addRule(PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
            }
        }
        $this->addElement('style_button', 'add[]', Translation :: get('AddCriteriaOption'), array('class' => 'normal add add_option'));
    }

    
	function add_option_number_field($number = null)
    {
        $element = $this->createElement('text', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('CriteriaOptionNumber'), array("size" => "3"));
		if($element->getValue() == null)
		{
        	$element->setValue($number + 1);
        	$element->freeze();
		}
        return $element;
    }
    
	function add_description_score_field($number = null)
    {
    	$element = $this->createElement('text', CriteriaScore :: PROPERTY_DESCRIPTION_SCORE . $number, Translation :: get('CriteriaDescriptionScore'), array("size" => "70"));
		return $element;
    }
    
	function add_score_field($number = null)
    {
        $element = $this->createElement('text', CriteriaScore :: PROPERTY_SCORE . $number, Translation :: get('CriteriaScore'), array("size" => "10"));
    	return $element;
    }
    
	function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent :: validate();
    }
  
}
?>