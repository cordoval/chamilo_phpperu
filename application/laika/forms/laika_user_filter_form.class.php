<?php
/**
 * $Id: laika_user_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
require_once dirname(__FILE__) . '/../laika_manager/laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';

class LaikaBrowserFilterForm extends FormValidator
{
    const USER_FILTER_START_DATE = 'filter_start_date';
    const USER_FILTER_END_DATE = 'filter_end_date';
    const USER_FILTER_GROUP = 'filter_group';
    
    private $manager;
    private $renderer;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($manager, $url)
    {
        parent :: __construct('laika_lister_filter_form', 'post', $url);
        
        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;
        
        $this->build_form();
        
        $this->setDefaults();
        
        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $ldm = LaikaDataManager :: get_instance();
        
        $this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');
        
        $this->add_timewindow(self :: USER_FILTER_START_DATE, self :: USER_FILTER_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'), false);
        $this->addElement('select', self :: USER_FILTER_GROUP, Translation :: get('Group'), $this->get_groups());
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal search'));
    }

    function get_filter_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }
        
        $filter_start_date = Request :: get(self :: USER_FILTER_START_DATE);
        $filter_end_date = Request :: get(self :: USER_FILTER_END_DATE);
        $filter_group = Request :: get(self :: USER_FILTER_GROUP);
        
        $form_validates = $this->validate();
        
        if ($form_validates)
        {
            $values = $this->exportValues();
            
            $filter_start_date = Utilities :: time_from_datepicker_without_timepicker($values[self :: USER_FILTER_START_DATE]);
            $filter_end_date = Utilities :: time_from_datepicker_without_timepicker($values[self :: USER_FILTER_END_DATE]);
            $filter_group = $values[self :: USER_FILTER_GROUP];
        }
        else
        {
            $filter_start_date = Request :: get(self :: USER_FILTER_START_DATE);
            $filter_end_date = Request :: get(self :: USER_FILTER_END_DATE);
            $filter_group = Request :: get(self :: USER_FILTER_GROUP);
        }
        
        $conditions = array();
        
        $conditions[] = new InequalityCondition(LaikaAttempt :: PROPERTY_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $filter_start_date);
        $conditions[] = new InequalityCondition(LaikaAttempt :: PROPERTY_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, $filter_end_date);
        
        if ($filter_group != '0')
        {
            $gdm = GroupDataManager :: get_instance();
            $group = $gdm->retrieve_group($filter_group);
            $users = $group->get_users(true, true);
            
            if (count($users) == 0)
            {
                $users = array('0');
            }
            
            $conditions[] = new InCondition(LaikaAttempt :: PROPERTY_USER_ID, $users);
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

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
            $parameters[self :: USER_FILTER_START_DATE] = Utilities :: time_from_datepicker_without_timepicker($values[self :: USER_FILTER_START_DATE]);
            $parameters[self :: USER_FILTER_END_DATE] = Utilities :: time_from_datepicker_without_timepicker($values[self :: USER_FILTER_END_DATE]);
            $parameters[self :: USER_FILTER_GROUP] = $values[self :: USER_FILTER_GROUP];
            
            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: USER_FILTER_START_DATE] = Request :: get(self :: USER_FILTER_START_DATE);
            $parameters[self :: USER_FILTER_END_DATE] = Request :: get(self :: USER_FILTER_END_DATE);
            $parameters[self :: USER_FILTER_GROUP] = Request :: get(self :: USER_FILTER_GROUP);
            
            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $filter_start_date = Request :: get(self :: USER_FILTER_START_DATE);
        $filter_end_date = Request :: get(self :: USER_FILTER_END_DATE);
        $filter_group = Request :: get(self :: USER_FILTER_GROUP);
        
        return (isset($filter_group) && isset($filter_start_date) && isset($filter_end_date));
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right; clear: both;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    function get_groups()
    {
        $group_menu = new GroupMenu(null, null, false, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');
        
        $options = $renderer->toArray();
        
        if (count($options) === 0)
        {
            $options[0] = '--&nbsp;' . Translation :: get('NoGroupsAvailable') . '&nbsp;--';
        }
        
        return $options;
    }

    function setDefaults($defaults = array ())
    {
        $parameters_set = $this->get_parameters_are_set();
        
        if ($parameters_set)
        {
            $defaults[self :: USER_FILTER_START_DATE] = Request :: get(self :: USER_FILTER_START_DATE);
            $defaults[self :: USER_FILTER_END_DATE] = Request :: get(self :: USER_FILTER_END_DATE);
            $defaults[self :: USER_FILTER_GROUP] = Request :: get(self :: USER_FILTER_GROUP);
        }
        
        parent :: setDefaults($defaults);
    }
}
?>