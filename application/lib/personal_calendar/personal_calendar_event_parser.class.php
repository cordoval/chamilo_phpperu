<?php
abstract class PersonalCalendarEventParser 
{
	private $publication;
	private $start_date;
	private $end_date;
	private $parent;
	
	function PersonalCalendarEventParser($parent, $publication, $start_date, $end_date)
	{
		$this->parent = $parent;
		$this->publication = $publication;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
	}
	
	static function factory($parent, $publication, $start_date = 0, $end_date = 0)
	{
		$object = $publication->get_publication_object();
		$type = $object->get_type();
		$file = dirname (__FILE__) . '/personal_calendar_event_parser/personal_calendar_event_' . Utilities :: camelcase_to_underscores($type) . '_parser.class.php';
        
        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get('PersonalCalendarEventParser') . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';
            
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb('#', Translation :: get(PersonalCalendarManager:: APPLICATION_NAME)));
            
            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        
        $class = 'PersonalCalendarEvent' . Utilities :: underscores_to_camelcase($type) . 'Parser';
        require_once $file;
        return new $class($parent, $publication, $start_date, $end_date);
	}
	
	public function get_publication()
	{
		return $this->publication;
	}
	
	public function set_publication($publication)
	{
		$this->publication = $publication;
	}
	
	public function get_start_date()
	{
		return $this->start_date;
	}
	
	public function set_start_date($start_date)
	{
		$this->start_date = $start_date;
	}
	
	public function get_end_date()
	{
		return $this->end_date;
	}
	
	public function set_end_date($end_date)
	{
		$this->end_date = $end_date;
	}
	
	public function get_parent()
	{
		return $this->parent;
	}
	
	public function set_parent($parent)
	{
		$this->parent = $parent;
	}
	
	abstract function get_events();
	
    function get_publication_viewing_url($publication)
    {
        $parameters = array();
        $parameters[PersonalCalendarManager :: PARAM_ACTION] = PersonalCalendarManager :: ACTION_VIEW_PUBLICATION;
        $parameters[PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID] = $publication->get_id();
        $parameters[Application :: PARAM_APPLICATION] = PersonalCalendarManager :: APPLICATION_NAME;
        
        return $this->get_parent()->get_link($parameters);
    }
}
?>