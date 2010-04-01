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
		
		$condition = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $criteria->get_id());
		$options = $this->data_manager->count_criterias_score($condition);
		
		if ($this->form_type == self :: TYPE_CREATOR_CRITERIA)
		{
			$this->build_creator_criteria_form();
			$this->setCriteriaDefaults();
			$this->setCriteriaScoreDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_CRITERIA)
		{
			$this->build_editor_criteria_form($options);
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
    
	function build_editor_criteria_form($options)
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
		
		$this->criteria_score_form($options);
		
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
            	$scores = $values[$key];
            	
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
	                	$criteria_score->set_target_scores($scores);
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
        $result = true;
        
        $condition = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $criteria->get_id());
        $scores_db = $this->data_manager->count_criterias_score($condition);

    	if($scores_db > 0)
    	{
	    	$cdm = CbaDataManager :: get_instance(); 
			$target_scores = $this->criteria_score->get_target_scores();
			
	    	foreach($target_scores as $index => $value)
			{
				$id = $value;
				$criteria_id = $this->criteria->get_id();
				$criteria_score = $cdm->retrieve_criteria_score_unique($id, $criteria_id);
	
				$criteria_score->delete();
			}
    	}
    	
        
    	foreach ($values as $key => $value)
        {     
        	
            if (strpos($key, 'description_score') !== false)
            {
            	$scores = $values[$key];
            	
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
	                	$criteria_score->set_target_scores($scores);
	                    $result &= $criteria_score->create();
	                }
        		}
        	}
        }
		return $result;
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
		$values = $this->exportValues();
				
		$cdm = CbaDataManager :: get_instance(); 
		$target_scores = $this->criteria_score->get_target_scores();
		
    	foreach($target_scores as $index => $value)
		{
			$id = $value;
			$criteria_id = $this->criteria->get_id();
			$criteria_score = $cdm->retrieve_criteria_score_unique($id, $criteria_id);

			$defaults[CriteriaScore :: PROPERTY_DESCRIPTION_SCORE . $index] = $criteria_score->get_description_score();
			$defaults[CriteriaScore :: PROPERTY_SCORE . $index] = $criteria_score->get_score();
		}		
		parent :: setDefaults($defaults);
	}
	
	
	
	// Dynamic form options
	
	function criteria_score_form($options)
    {
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
            unset($_SESSION['mc_up_option']);
            unset($_SESSION['mc_down_option']);
        }
        
        if (! isset($_SESSION['mc_number_of_options']))
        {
        	if($options == null)
        	{
            	$_SESSION['mc_number_of_options'] = 1;
        	}
        	else
        	{
        		$_SESSION['mc_number_of_options'] = $options;
        	}
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if(! isset($_SESSION['mc_up_option']))
        {
        	$_SESSION['mc_up_option'] = array();
        }
        
    	if(! isset($_SESSION['mc_down_option']))
        {
        	$_SESSION['mc_down_option'] = array();
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
    	if (isset($_POST['up']))
        {
            $indexes = array_keys($_POST['up']);
            $count = $_SESSION['mc_number_of_options'];
            for($i = 1; $i <= $count; $i++)
            {
            	if($i == $indexes[0])
            	{
            		$j = $i;
            		$i = $i - 1;
            	}
            }
            
        }
    	if (isset($_POST['down']))
        {
        	$indexes = array_keys($_POST['down']);
        	$count = $_SESSION['mc_number_of_options'];
            for($i = 1; $i <= $count; $i++)
            {
            	if($i == $indexes[0])
            	{
            		$j = $i;
            		$i = $i + 1;
            	}
            }
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
       
        
        for($option_number = 0; $option_number < $number_of_options; $option_number++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
				//$group[] = $this->add_option_number_field($option_number);
				$group[] = $this->add_description_text($option_number);
				$group[] = $this->add_description_score_field($option_number);
				$group[] = $this->add_score_text($option_number);
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

    
	/*function add_option_number_field($number = null)
    {
        $element = $this->createElement('text', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('CriteriaOptionNumber'), array("size" => "3"));
		if($element->getValue() == null)
		{
        	$element->setValue($number + 1);
        	$element->freeze();
		}
        return $element;
    }*/
    
	function add_description_text($number = null)
    {
        $element = $this->createElement('static', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('CriteriaDescriptionText'));
        $element->setValue(Translation :: get('Description'));
        $element->freeze();

        return $element;
    }
    
	function add_description_score_field($number = null)
    {
    	$element = $this->createElement('text', CriteriaScore :: PROPERTY_DESCRIPTION_SCORE . $number, Translation :: get('CriteriaDescriptionScore'), array("size" => "62"));
		return $element;
    }
    
	function add_score_text($number = null)
    {
        $element = $this->createElement('static', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('CriteriaScoreText'));
        $element->setValue(Translation :: get('Score'));
        $element->freeze();

        return $element;
    }
    
	function add_score_field($number = null)
    {
        $element = $this->createElement('text', CriteriaScore :: PROPERTY_SCORE . $number, Translation :: get('CriteriaScore'), array("size" => "10"));
    	return $element;
    }
    
	function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['up']) || isset($_POST['down']))
        {
            return false;
        }
        return parent :: validate();
    }
  
}
?>