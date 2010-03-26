<?php
require_once dirname(__FILE__) . '/../indicator.class.php';
require_once dirname(__FILE__) . '/../indicator_criteria.class.php';
/**
 * This class describes a IndicatorForm object
 * 
 * @author Nick Van Loocke
 **/
class IndicatorForm extends FormValidator
{
	const TYPE_CREATOR_INDICATOR = 1;
	const TYPE_EDITOR_INDICATOR = 2;
	
	const PARAM_TARGET = 'target_criterias';
	const PARAM_TARGET_ELEMENTS = 'target_criterias_elements';
	
	private $indicator;
	private $indicator_criteria;
	private $user;
	private $owner_id;
	private $data_manager;

    function IndicatorForm($form_type, $indicator, $indicator_criteria, $action, $user)
    {
    	parent :: __construct('cba_settings', 'post', $action);

    	$this->indicator = $indicator;
    	$this->indicator_criteria = $indicator_criteria;
    	$this->user = $user;
		$this->form_type = $form_type;
		$this->owner_id = $indicator->get_owner_id();
		$this->data_manager = CbaDataManager :: get_instance();

		if ($this->form_type == self :: TYPE_CREATOR_INDICATOR)
		{
			$this->build_creator_indicator_form();
			$this->setIndicatorDefaults();
		}
   	 	elseif ($this->form_type == self :: TYPE_EDITOR_INDICATOR)
		{
			$this->build_editor_indicator_form();
			$this->setIndicatorDefaults();
		}

    }
    
    // Forms
    
	function build_creator_indicator_form()
    {
    	$this->addElement('text', Indicator :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Indicator :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
		$select = $this->add_select(Indicator :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        $category_id = Request :: get(CbaManager :: PARAM_CATEGORY_ID);
		$select->setSelected($category_id);
    	$this->addRule(Indicator :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->add_html_editor(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

		$attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_criteria_feed.php';

        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        
        // DELETE ADD INDICATORS IN FORM VALIDATOR
        
        $this->add_indicators(self :: PARAM_TARGET, Translation :: get('AddIndicators'), $attributes);
        
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_indicator_form()
    {
    	$this->addElement('text', Indicator :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Indicator :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
    	$select = $this->add_select(Indicator :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        $select->setSelected($this->indicator->get_parent_id());
    	$this->addRule(Indicator :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_criteria_feed.php';

        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        
        $this->add_indicators(self :: PARAM_TARGET, Translation :: get('AddIndicators'), $attributes);
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
 	function add_indicators($elementName, $elementLabel, $attributes)
    {
		$element_finder = $this->createElement('element_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'], $attributes['defaults']);
		$element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
    }
     
	function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(IndicatorCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(IndicatorCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_indicator_categories($condition);
        while ($indicator = $cdm->next_result())
        {
            $this->categories[$indicator->get_id()] = str_repeat('--', $level) . ' ' . $indicator->get_name();
            $this->retrieve_categories_recursive($indicator->get_id(), $exclude_category, ($level + 1));
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


    // Create and Update functions (Indicator)
    
	function create_indicator()
    {
    	$indicator = $this->indicator;
    	$indicator->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $this->exportValue(Indicator :: PROPERTY_PARENT_ID);
    	
    	$indicator->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$indicator->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);
    	$indicator->move($parent);

   		return $indicator->create();
    }
    
	function create_indicator_criteria()
    {
    	$indicator = $this->indicator;
    	$indicator_criteria = $this->indicator_criteria;  
    	$indicator_criteria->set_owner_id($this->get_owner_id());	
    	$values = $this->exportValues();
	   	
    	$indicator_criteria->set_indicator_id($indicator->get_id());
    	
    	$result = true;
    	$criterias = $values[self :: PARAM_TARGET_ELEMENTS];
    	
    	foreach($criterias as $key => $value)
    	{
    		$criteria_id = substr($value, 10);
    		$indicator_criteria->set_criteria_id($criteria_id);

    		$conditions = array();
			$conditions[] = new EqualityCondition(IndicatorCriteria :: PROPERTY_INDICATOR_ID, $indicator->get_id());				
        	$conditions[] = new EqualityCondition(IndicatorCriteria :: PROPERTY_CRITERIA_ID, $indicator_criteria->get_criteria_id());
    		
            $condition = new AndCondition($conditions);
           	$cats = $this->data_manager->count_indicators_criteria($condition);
                
            if ($cats > 0)
            {
                $result = false;
            }
            else
            {
              	$result &= $indicator_criteria->create();
            }
    	}
        //dump($competency_indicator);
    	//exit();   	
    	return $result;
    }
    
	function update_indicator()
    {
    	$indicator = $this->indicator;
    	$indicator->set_owner_id($this->get_owner_id());
    	$values = $this->exportValues();
    	$parent = $this->exportValue(Indicator :: PROPERTY_PARENT_ID);

    	$indicator->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$indicator->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);
		$indicator->move($parent);

    	return $indicator->update();
    }

    
	// Default values (setter)
	
	function setIndicatorDefaults($defaults = array ())
	{
		$indicator = $this->indicator;

		$defaults[Indicator :: PROPERTY_ID] = $indicator->get_id();
    	$defaults[Indicator :: PROPERTY_TITLE] = $indicator->get_title();
    	$defaults[Indicator :: PROPERTY_DESCRIPTION] = $indicator->get_description();

		parent :: setDefaults($defaults);
	}
}
?>