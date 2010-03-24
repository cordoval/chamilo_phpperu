<?php
require_once dirname(__FILE__) . '/../competency.class.php';
require_once dirname(__FILE__) . '/../competency_indicator.class.php';
/**
 * This class describes a CompetencyForm object
 * 
 * @author Nick Van Loocke
 **/
class CompetencyForm extends FormValidator
{	
	const TYPE_CREATOR_COMPETENCY = 1;
	const TYPE_EDITOR_COMPETENCY = 2;
	
	const PARAM_TARGET = 'target_indicators';
	const PARAM_TARGET_ELEMENTS = 'target_indicators_elements';

	private $competency;
	private $competency_indicator;
	private $user;
	private $owner_id;
	private $data_manager;

    function CompetencyForm($form_type, $competency, $competency_indicator, $action, $user)
    {
    	parent :: __construct('cba_settings', 'post', $action);

    	$this->competency = $competency;
    	$this->competency_indicator = $competency_indicator;
    	$this->user = $user;
		$this->form_type = $form_type;
		$this->owner_id = $competency->get_owner_id();
		$this->data_manager = CbaDataManager :: get_instance();

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

		$this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, 0);
		
    	$this->addElement('select', Competency :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        $this->addRule(Competency :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->add_html_editor(Competency :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Competency :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');            

        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_indicator_feed.php';

        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        
        $this->add_indicators(self :: PARAM_TARGET, Translation :: get('AddCriterias'), $attributes);
        
        
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
    	$parent = $this->exportValue(Competency :: PROPERTY_PARENT_ID);
    	  	
    	$competency->set_title($values[Competency :: PROPERTY_TITLE]);
    	$competency->set_description($values[Competency :: PROPERTY_DESCRIPTION]);  
    	$competency->move($parent);

   		return $competency->create();
    }
    
    function create_competency_indicator()
    {
    	$competency = $this->competency;
    	$competency_indicator = $this->competency_indicator;  	
    	$values = $this->exportValues();
	   	
    	$competency_indicator->set_competency_id($competency->get_id());
    	
    	$result = true;
    	$indicators = $values[self :: PARAM_TARGET_ELEMENTS];
    	
    	foreach($indicators as $key => $value)
    	{
    		$indicator_id = substr($value, 10);
    		$competency_indicator->set_indicator_id($indicator_id);

    		$conditions = array();
			$conditions[] = new EqualityCondition(CompetencyIndicator :: PROPERTY_COMPETENCY_ID, $competency->get_id());				
        	$conditions[] = new EqualityCondition(CompetencyIndicator :: PROPERTY_INDICATOR_ID, $competency_indicator->get_indicator_id());
    		
            $condition = new AndCondition($conditions);
           	$cats = $this->data_manager->count_competency_indicator($condition);
                
            if ($cats > 0)
            {
                $result = false;
            }
            else
            {
              	$result &= $competency_indicator->create();
            }
    	}
        //dump($competency_indicator);
    	//exit();   	
    	return $result;
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