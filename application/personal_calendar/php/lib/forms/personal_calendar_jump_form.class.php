<?php
/**
 * $Id: personal_calendar_jump_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */

class PersonalCalendarJumpForm extends FormValidator
{
    private $manager;
    private $renderer;

    const JUMP_DAY = 'day';
    const JUMP_MONTH = 'month';
    const JUMP_YEAR = 'year';
    
    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function PersonalCalendarJumpForm($manager, $url)
    {
        parent :: __construct('personal_calendar_jump_form', 'post', $url);

        $this->renderer = $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->renderer->setFormTemplate('<form {attributes}><div class="jump_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{element}</div></div>');

        $this->addElement('static', null, null, Translation :: get('JumpTo', PersonalCalendarManager :: APPLICATION_NAME));
        $this->addElement('select', self :: JUMP_DAY, null, $this->get_days(), array('class' => 'postback'));
        $this->addElement('select', self :: JUMP_MONTH, null, $this->get_months(), array('class' => 'postback'));
        $this->addElement('select', self :: JUMP_YEAR, null, $this->get_years(), array('class' => 'postback'));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Jump'), array('class' => 'normal'));
		$time = Request :: get(PersonalCalendarManager::PARAM_TIME) ? intval(Request :: get(PersonalCalendarManager::PARAM_TIME)) : time();
        $this->setDefaults(array(self :: JUMP_DAY => date('j', $time), self :: JUMP_MONTH => date('n', $time), self :: JUMP_YEAR => date('Y', $time)));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div class="content_object" style="margin-top:10px;padding:10px;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }
    
    function get_time()
    {
    	$values = $this->exportValues();
    	return mktime(0, 0, 0, $values[self :: JUMP_MONTH], $values[self :: JUMP_DAY], $values[self :: JUMP_YEAR]); 
    }

    function get_days()
    {
    	$time = Request :: get(PersonalCalendarManager::PARAM_TIME) ? intval(Request :: get(PersonalCalendarManager::PARAM_TIME)) : time();
    	$number_days = date('t', $time);
    	$days = array();
    	for($i = 1; $i <= $number_days; $i++)
    	{
    		$days[$i] = $i;
    	}
    	return $days;
    }
    
    function get_months()
    {
    	$MonthsLong = array(Translation :: get("JanuaryLong"), Translation :: get("FebruaryLong"), Translation :: get("MarchLong"), Translation :: get("AprilLong"), Translation :: get("MayLong"), Translation :: get("JuneLong"), Translation :: get("JulyLong"), Translation :: get("AugustLong"), Translation :: get("SeptemberLong"), Translation :: get("OctoberLong"), Translation :: get("NovemberLong"), Translation :: get("DecemberLong"));
		$months = array();
    	foreach($MonthsLong as $key => $month)
		{
			$months[$key+1] = $month; 
		}    	
    	return $months;
    }
    
    function get_years()
    {
    	$time = Request :: get(PersonalCalendarManager::PARAM_TIME) ? intval(Request :: get(PersonalCalendarManager::PARAM_TIME)) : time();
    	$year = date('Y', $time);
    	$years = array();
    	for($i = $year - 5; $i <= $year + 5; $i++)
    	{
    		$years[$i] = $i;
    	}
    	return $years;
    }
}
?>