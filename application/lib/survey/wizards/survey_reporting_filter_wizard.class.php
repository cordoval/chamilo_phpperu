<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/analyzer/analyzer.class.php';

class SurveyReportingFilterWizard extends WizardPageValidator
{
    private $tabs_generator;
    private $tabs;
    private $parameters;
    private $publication_id;
    
    const CONTEXTS_TAB = 'contexts';
    const GROUPS_TAB = 'groups';
    const USERS_TAB = 'users';
    const QUESTIONS_TAB = 'questions';
    const CONTEXT_TEMPLATES_TAB = 'context_templates';
    const ANALYSE_TYPE_TAB = 'analyse_type';
    
    const PARAM_CONTEXTS = 'contexts';
    const PARAM_GROUPS = 'groups';
    const PARAM_USERS = 'users';
    const PARAM_QUESTIONS = 'questions';
    const PARAM_CONTEXT_TEMPLATES = 'context_templates';
    const PARAM_ANALYSE_TYPE = 'analyse_type';
    const PARAM_PUBLICATION_ID = 'publication_id';
    
    const TYPE_CONTEXTS = 1;
    const TYPE_GROUPS = 2;
    const TYPE_USERS = 3;
    const TYPE_QUESTIONS = 4;
    const TYPE_CONTEXT_TEMPLATES = 5;
    const TYPE_ANALYSE_TYPE = 6;

    function SurveyReportingFilterWizard($types, $publication_id, $actions)
    {
        parent :: __construct('survey_reporting_filter', 'post', $actions);
        $this->publication_id = $publication_id;
        
        $this->tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
        
        $this->tabs = array();
        
        foreach ($types as $type)
        {
            switch ($type)
            {
                case self :: TYPE_CONTEXTS :
                    $tab = new DynamicFormTab(self :: CONTEXTS_TAB, Translation :: get(self :: CONTEXTS_TAB), null, 'build_contexts_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_GROUPS :
                    $tab = new DynamicFormTab(self :: GROUPS_TAB, Translation :: get(self :: GROUPS_TAB), null, 'build_groups_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_USERS :
                    $tab = new DynamicFormTab(self :: USERS_TAB, Translation :: get(self :: USERS_TAB), null, 'build_users_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_QUESTIONS :
                    $tab = new DynamicFormTab(self :: QUESTIONS_TAB, Translation :: get(self :: QUESTIONS_TAB), null, 'build_question_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_CONTEXT_TEMPLATES :
                    $tab = new DynamicFormTab(self :: CONTEXT_TEMPLATES_TAB, Translation :: get(self :: CONTEXT_TEMPLATES_TAB), null, 'build_context_templates_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_ANALYSE_TYPE :
                    $tab = new DynamicFormTab(self :: ANALYSE_TYPE_TAB, Translation :: get(self :: ANALYSE_TYPE_TAB), null, 'build_analyse_type_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
            }
        }
        dump($this->publication_id);
        $this->addElement('hidden', self :: PARAM_PUBLICATION_ID, $this->publication_id);
        $this->tabs_generator->render();
        $this->setDefaults();
    
    }

    function build_analyse_type_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAnalyseType') . '</p>');
        
        $types = SurveyAnalyzer :: get_supported_analyse_types();
        
        $this->add_select(self :: PARAM_ANALYSE_TYPE, Translation :: get('AvailableAnalyseTypes'), $types, true);
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: ANALYSE_TYPE_TAB);
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_context_templates_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableLevels') . '</p>');
        
        $attributes = array();
        
        $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);
        $survey = $pub->get_publication_object();
        $levels = $survey->count_levels();
        $level = 1;
        while ($level <= $levels)
        {
            $context_template = $survey->get_context_template($level);
            $context_template_id = $context_template->get_id();
            $context_template_name = $context_template->get_context_type_name();
            $attributes[$context_template_id] = $context_template_name;
            $level ++;
        }
        
        $multi_select = $this->addElement('multiselect', self :: PARAM_CONTEXT_TEMPLATES, Translation :: get('AvailableLevels'), $attributes);
        $multi_select->setAttribute("style", "width:auto; min-width:200px;");
        $multi_select->setAttribute("class", "normal button");
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: CONTEXT_TEMPLATES_TAB);
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_contexts_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableContexts') . '</p>');
        
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
        $element_finder = $this->createElement('element_finder', 'contexts', Translation :: get('AvailableContexts'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: CONTEXTS_TAB);
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function build_groups_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableGroups') . '</p>');
        
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
        $element_finder = $this->createElement('element_finder', 'groups', Translation :: get('AvailableGroups'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: GROUPS_TAB);
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function build_users_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableUsers') . '</p>');
        
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
        $element_finder = $this->createElement('element_finder', 'users', Translation :: get('AvailableUsers'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: USERS_TAB);
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_question_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableQuestions') . '</p>');
        
        $attributes = array();
        
        $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);
        $survey = $pub->get_publication_object();
        
        $complex_questions = $survey->get_complex_questions();
        
        foreach ($complex_questions as $complex_question_id => $complex_question)
        {
            
            $question = $complex_question->get_ref_object();
            if (! $question instanceof SurveyDescription)
            {
                $attributes[$complex_question_id] = $question->get_title();
            }
        }
        
        $multi_select = $this->addElement('multiselect', self :: PARAM_QUESTIONS, Translation :: get('AvailableQuestions'), $attributes);
        $multi_select->setAttribute("style", "width:auto; min-width:200px;");
        $multi_select->setAttribute("class", "normal button");
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'positive'), self :: QUESTIONS_TAB);
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function get_filter_parameters()
    {
        
    	dump($this->exportValues());
    	
    	if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return array();
        }
        
        if ($this->validate())
        {
            $values = $this->exportValues();
            
            $parameters = array();
            $parameters[self :: PARAM_CONTEXTS] = $values[self :: PARAM_CONTEXTS];
            $parameters[self :: PARAM_GROUPS] = $values[self :: PARAM_GROUPS]['group'];
            $parameters[self :: PARAM_USERS] = $values[self :: PARAM_USERS]['user'];
            $parameters[self :: PARAM_QUESTIONS] = $values[self :: PARAM_QUESTIONS];
            $parameters[self :: PARAM_CONTEXT_TEMPLATES] = $values[self :: PARAM_CONTEXT_TEMPLATES];
            $parameters[self :: PARAM_ANALYSE_TYPE] = $values[self :: PARAM_ANALYSE_TYPE];
            $parameters[self :: PARAM_PUBLICATION_ID] = $values[self :: PARAM_PUBLICATION_ID];
            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: PARAM_CONTEXTS] = Request :: get(self :: PARAM_CONTEXTS);
            $parameters[self :: PARAM_GROUPS] = Request :: get(self :: PARAM_GROUPS);
            $parameters[self :: PARAM_USERS] = Request :: get(self :: PARAM_USERS);
            $parameters[self :: PARAM_QUESTIONS] = Request :: get(self :: PARAM_QUESTIONS);
            $parameters[self :: PARAM_CONTEXT_TEMPLATES] = Request :: get(self :: PARAM_CONTEXT_TEMPLATES);
            $parameters[self :: PARAM_ANALYSE_TYPE] = Request :: get(self :: PARAM_ANALYSE_TYPE);
            $parameters[self :: PARAM_PUBLICATION_ID] = Request :: get(self :: PARAM_PUBLICATION_ID);
            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $contexts = Request :: get(self :: PARAM_CONTEXTS);
        $groups = Request :: get(self :: PARAM_GROUPS);
        $users = Request :: get(self :: PARAM_USERS);
        $questions = Request :: get(self :: PARAM_QUESTIONS);
        $context_templates = Request :: get(self :: PARAM_CONTEXT_TEMPLATES);
        $analyse_type = Request :: get(self :: PARAM_ANALYSE_TYPE);
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        return (isset($contexts) || isset($groups) || isset($users) || isset($questions) || isset($context_templates) || isset($analyse_type)|| isset($publication_id));
    }

    function setDefaults($defaults = array ())
    {
        $contexts = Request :: get(self :: PARAM_CONTEXTS);
        $groups = Request :: get(self :: PARAM_GROUPS);
        $users = Request :: get(self :: PARAM_USERS);
        $questions = Request :: get(self :: PARAM_QUESTIONS);
        $context_templates = Request :: get(self :: PARAM_CONTEXT_TEMPLATES);
        $analyse_type = Request :: get(self :: PARAM_ANALYSE_TYPE);
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        $contexts_set = isset($contexts);
        $groups_set = isset($groups);
        $users_set = isset($users);
        $questions_set = isset($questions);
        $context_templates_set = isset($context_templates);
        $analyse_type_set = isset($analyse_type);
        $publication_id_set = isset($publication_id);
        
        if ($contexts_set)
        {
            $defaults[self :: PARAM_CONTEXTS] = Request :: get(self :: PARAM_CONTEXTS);
        }
        if ($groups_set)
        {
            $defaults[self :: PARAM_GROUPS] = Request :: get(self :: PARAM_GROUPS);
        }
        if ($users_set)
        {
            $defaults[self :: PARAM_USERS] = Request :: get(self :: PARAM_USERS);
        }
        if ($questions_set)
        {
            $defaults[self :: PARAM_QUESTIONS] = Request :: get(self :: PARAM_QUESTIONS);
        }
        if ($context_templates_set)
        {
            $defaults[self :: PARAM_CONTEXT_TEMPLATES] = Request :: get(self :: PARAM_CONTEXT_TEMPLATES);
        }
        if ($analyse_type_set)
        {
            $defaults[self :: PARAM_ANALYSE_TYPE] = Request :: get(self :: PARAM_ANALYSE_TYPE);
        }
        else
        {
            $defaults[self :: PARAM_ANALYSE_TYPE] = 'absolute';
        }
    	if ($publication_id_set)
        {
            $defaults[self :: PARAM_PUBLICATION_ID] = Request :: get(self :: PARAM_PUBLICATION_ID);
        }else{
        	$defaults[self :: PARAM_PUBLICATION_ID] = $this->publication_id;
        }
        
        parent :: setDefaults($defaults);
    }

}

?>