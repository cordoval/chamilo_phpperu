<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';

class SurveyReportingFilterWizard extends WizardPageValidator
{
    private $tabs_generator;
    private $tabs;
    private $parameters;
    private $selected_publication_ids;
    
    const CONTEXT_TEMPLATES_TAB = 'context_templates';
    const CONTEXTS_TAB = 'contexts';
    const GROUPS_TAB = 'groups';
    const USERS_TAB = 'users';
    const QUESTIONS_TAB = 'questions';
    
    const PARAM_CONTEXTS = 'contexts';
    const PARAM_GROUPS = 'groups';
    const PARAM_USERS = 'users';
    const PARAM_QUESTIONS = 'questions';
    
    const TYPE_CONTEXTS = 1;
    const TYPE_GROUPS = 2;
    const TYPE_USERS = 3;
    const TYPE_QUESTIONS = 4;

    function SurveyReportingFilterWizard($types, $selected_publication_ids, $actions)
    {
        parent :: __construct('survey_reporting_filter', 'post', $actions);
        $this->selected_publication_ids = $selected_publication_ids;
        
        $this->tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
        
        $this->tabs = array();
        
        foreach ($types as $type)
        {
            switch ($type)
            {
                case self :: TYPE_CONTEXTS :
                    $tab = new DynamicFormTab(SurveyReportingFilterWizard :: CONTEXTS_TAB, Translation :: get('Contexts'), null, 'build_contexts_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_GROUPS :
                    $tab = new DynamicFormTab(SurveyReportingFilterWizard :: GROUPS_TAB, Translation :: get('Groups'), null, 'build_groups_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_USERS :
                    $tab = new DynamicFormTab(SurveyReportingFilterWizard :: USERS_TAB, Translation :: get('Users'), null, 'build_users_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
                case self :: TYPE_QUESTIONS :
                    $tab = new DynamicFormTab(SurveyReportingFilterWizard :: QUESTIONS_TAB, Translation :: get('Questions'), null, 'build_question_form');
                    $this->tabs_generator->add_tab($tab);
                    break;
            }
        }
        $this->tabs_generator->render();
        $this->setDefaults();
    
    }

    function build_context_templates_form()
    {
        $this->addElement('html', '<p>' . Translation :: get('SelectAvailableContextTemplates') . '</p>');
        
        $attributes = array();
        
        $selected_survey_ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        foreach ($selected_survey_ids as $id)
        {
            $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
            $survey = $pub->get_publication_object();
            
            $context_template = $survey->get_context_template();
            $context_template_id = $context_template->get_id();
            $context_template_name = $context_template->get_name();
            $attributes[$context_template_id] = $context_template_name;
        }
        
        $multi_select = $this->addElement('multiselect', 'context_templates', Translation :: get('AvailableContextTemplates'), $attributes);
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
        
        foreach ($this->selected_publication_ids as $publication_id)
        {
            $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
            $survey = $pub->get_publication_object();
            $complex_questions = $survey->get_complex_questions();
            foreach ($complex_questions as $complex_question_id => $complex_question)
            {
                
                $question = $complex_question->get_ref_object();
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

    //    function getParameters()
    //    {
    //        $values = $this->exportValues();
    //        $parameters[self :: PARAM_CONTEXTS] = $values['contexts'];
    //        $parameters[self :: PARAM_GROUPS] = $values['groups']['group'];
    //        $parameters[self :: PARAM_USERS] = $values['users']['user'];
    //        $parameters[self :: PARAM_QUESTIONS] = $values[self :: PARAM_QUESTIONS];
    //        return $this->parameters;
    //    }
    

    function get_filter_parameters()
    {
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
            
            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: PARAM_CONTEXTS] = Request :: get(self :: PARAM_CONTEXTS);
            $parameters[self :: PARAM_GROUPS] = Request :: get(self :: PARAM_GROUPS);
            $parameters[self :: PARAM_USERS] = Request :: get(self :: PARAM_USERS);
            $parameters[self :: PARAM_QUESTIONS] = Request :: get(self :: PARAM_QUESTIONS);
            
            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $contexts = Request :: get(self :: PARAM_CONTEXTS);
        $groups = Request :: get(self :: PARAM_GROUPS);
        $users = Request :: get(self :: PARAM_USERS);
        $questions = Request :: get(self :: PARAM_QUESTIONS);
        
        return (isset($contexts) || isset($groups) || isset($users) || isset($questions));
    }

    function setDefaults($defaults = array ())
    {
        $contexts = Request :: get(self :: PARAM_CONTEXTS);
        $groups = Request :: get(self :: PARAM_GROUPS);
        $users = Request :: get(self :: PARAM_USERS);
        $questions = Request :: get(self :: PARAM_QUESTIONS);
        
        $contexts_set = isset($contexts);
        $groups_set = isset($groups);
        $users_set = isset($users);
        $questions_set = isset($questions);
        
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
        parent :: setDefaults($defaults);
    }

}

?>