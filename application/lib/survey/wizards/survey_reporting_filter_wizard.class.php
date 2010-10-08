<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';

class SurveyReportingFilterWizard extends WizardPageValidator
{
	private $tabs_generator;
	private $tabs;
	private $parameters;
	
	const CONTEXT_TEMPLATES_TAB = 'context_templates';
	const CONTEXTS_TAB = 'contexts';
	const GROUPS_TAB = 'groups'; 
	const USERS_TAB = 'users';
	
	const PARAM_CONTEXTS = 'contexts';
	const PARAM_GROUPS = 'groups';
	const PARAM_USERS = 'users'; 
	
	const TYPE_ALL = 0;
	const TYPE_CONTEXTS = 1;
	const TYPE_GROUPS = 2;
	const TYPE_USERS = 3;
	const TYPE_CONTEXTS_GROUPS = 4;
	const TYPE_CONTEXTS_USERS = 5;
	const TYPE_GROUPS_USERS = 6;
	
	function SurveyReportingFilterWizard($type,$selected_survey_ids, $actions)
    {  
    	parent :: __construct('survey_reporting_filter', 'post', $actions);
    	
    	$this->tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
    	
		$this->tabs = array();
		
		switch($type)
		{
			case 0: 
				/*$this->tabs[0]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXT_TEMPLATES_TAB, Translation :: get('ContextTemplates'), null, 'build_context_templates_form');
				$this->tabs[1]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
				$this->tabs[2]= new DynamicFormTab(SurveyReportingFilterWizard::GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
				$this->tabs[3]= new DynamicFormTab(SurveyReportingFilterWizard::USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
			
				$this->tabs_generator->add_tab($this->tabs[0]);
				$this->tabs_generator->add_tab($this->tabs[1]);
				$this->tabs_generator->add_tab($this->tabs[2]);
	       		$this->tabs_generator->add_tab($this->tabs[3]); */	     
				$this->tabs[0]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
				$this->tabs[1]= new DynamicFormTab(SurveyReportingFilterWizard::GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
				$this->tabs[2]= new DynamicFormTab(SurveyReportingFilterWizard::USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
				$this->tabs_generator->add_tab($this->tabs[0]);
				$this->tabs_generator->add_tab($this->tabs[1]);
				$this->tabs_generator->add_tab($this->tabs[2]);
    			break;
			case 1:
				$this->tabs[0]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
				$this->tabs_generator->add_tab($this->tabs[0]);
				break;
			case 2:
				$this->tabs[1]= new DynamicFormTab(SurveyReportingFilterWizard::GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
				$this->tabs_generator->add_tab($this->tabs[1]);
				break;
			case 3:
				$this->tabs[2]= new DynamicFormTab(SurveyReportingFilterWizard::USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
				$this->tabs_generator->add_tab($this->tabs[2]);
				break;
			case 4:
				$this->tabs[0]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
				$this->tabs[1]= new DynamicFormTab(SurveyReportingFilterWizard::GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
				$this->tabs_generator->add_tab($this->tabs[0]);
				$this->tabs_generator->add_tab($this->tabs[1]);
				break;
			case 5:
				$this->tabs[0]= new DynamicFormTab(SurveyReportingFilterWizard::CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
				$this->tabs[2]= new DynamicFormTab(SurveyReportingFilterWizard::USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
				$this->tabs_generator->add_tab($this->tabs[0]);
				$this->tabs_generator->add_tab($this->tabs[2]);
				break;
			case 6:
				$this->tabs[1]= new DynamicFormTab(SurveyReportingFilterWizard::GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
				$this->tabs[2]= new DynamicFormTab(SurveyReportingFilterWizard::USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
				$this->tabs_generator->add_tab($this->tabs[1]);
				$this->tabs_generator->add_tab($this->tabs[2]);
    			break;
		}
    		
       	$this->tabs_generator->render();
		
    }

	function build_context_templates_form()
    {
    	$this->addElement('html', '<p>'.Translation :: get('SelectAvailableContextTemplates').'</p>');
    	
    	$attributes = array();
    	
    	$selected_survey_ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
    	
        foreach ($selected_survey_ids as $id)
    	{
	    	$pub = SurveyDataManager::get_instance()->retrieve_survey_publication($id);
	       	$survey = $pub->get_publication_object();
	       	
	       	$context_template = $survey->get_context_template();
	       	$context_template_id = 	$context_template->get_id();
	        $context_template_name = $context_template->get_name();
	        $attributes[$context_template_id] = $context_template_name;
        }
        
        $multi_select = $this->addElement('multiselect','context_templates', Translation :: get('AvailableContextTemplates'), $attributes);
    	$multi_select->setAttribute("style","width:auto; min-width:200px;");
    	$multi_select->setAttribute("class","normal button");
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'),self::CONTEXT_TEMPLATES_TAB);

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    function build_contexts_form()
    {
		$this->addElement('html', '<p>'.Translation :: get('SelectAvailableContexts').'</p>');
    	
		$attributes = array();
    	$attributes['search_url'] = Path :: get(WEB_PATH) . 'application/lib/survey/xml_feeds/xml_context_feed.php';
  	
    	$locale = array();
        $locale['Display'] = Translation :: get('ChooseContext');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
		$attributes['options'] = array('load_elements' => false);
    	$element_finder = $this->createElement('element_finder','contexts',Translation :: get('AvailableContexts'),$attributes['search_url'],$attributes['locale'],$attributes['defaults'], $attributes['options']);
      	$element_finder->excludeElements($attributes['exclude']);
      	$this->addElement($element_finder);
      	
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button','submit', Translation :: get('Filter'), array('class' => 'positive'),self::CONTEXTS_TAB);
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
    }
    
	function build_groups_form()
    {
    	$this->addElement('html', '<p>'.Translation :: get('SelectAvailableGroups').'</p>');
    	
    	$attributes = array();
    	$attributes['search_url'] = Path :: get(WEB_PATH) . 'group/xml_feeds/xml_group_feed.php';
		$locale = array();
        $locale['Display'] = Translation :: get('ChooseGroup');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
		$attributes['options'] = array('load_elements' => false);
    	$element_finder = $this->createElement('element_finder','groups',Translation :: get('AvailableGroups'),$attributes['search_url'],$attributes['locale'],$attributes['defaults'], $attributes['options']);
    	$element_finder->excludeElements($attributes['exclude']);
    	$this->addElement($element_finder);
    	
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'),self::GROUPS_TAB);
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

  
    }
    
	function build_users_form()
    {
    	$this->addElement('html', '<p>'.Translation :: get('SelectAvailableUsers').'</p>');
    	
		$attributes = array();
    	$attributes['search_url'] = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
  		$locale = array();
        $locale['Display'] = Translation :: get('ChooseUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
		$attributes['options'] = array('load_elements' => false);
    	$element_finder = $this->createElement('element_finder','users',Translation :: get('AvailableUsers'),$attributes['search_url'],$attributes['locale'],$attributes['defaults'], $attributes['options']);
    	$element_finder->excludeElements($attributes['exclude']);
    	$this->addElement($element_finder);
    	
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'),self::USERS_TAB);
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    function getParameters()
    {
    	$values = $this->exportValues();
    	$this->parameters[self::PARAM_CONTEXTS] = $values['contexts'];
    	$this->parameters[self::PARAM_GROUPS] = $values['groups']['group'];
    	$this->parameters[self::PARAM_USERS] = $values['users']['user'];
    	return $this->parameters;
    }
}

?>