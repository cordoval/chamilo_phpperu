<?php
/**
 * $Id: personal_calendar_data_manager.class.php 127 2009-11-09 13:11:56Z vanpouckesven $
 * @package application.personal_calendar
 */
require_once BasicApplication :: get_application_manager_path('personal_calendar');

/**
 * This abstract class provides the necessary functionality to connect a
 * personal calendar to a storage system.
 */
class PersonalCalendarDataManager
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function PersonalCalendarDataManager()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return PersonalCalendarDataManager The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_personal_calendar_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'PersonalCalendarDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Gets the events
     * @param int $from_date
     * @param int $to_date
     */
    static public function get_events($parent, $from_date, $to_date)
    {
        $events = self :: get_user_events($parent, $from_date, $to_date);
        $events = array_merge($events, self :: get_connector_events($parent, $from_date, $to_date));
        $events = array_merge($events, self :: get_user_shared_events($parent, $from_date, $to_date));
        return $events;
    }

    static public function get_user_events($parent, $from_date, $to_date)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        $condition = new EqualityCondition(PersonalCalendarPublication :: PROPERTY_PUBLISHER, $parent->get_user()->get_id());
        $publications = $dm->retrieve_personal_calendar_publications($condition);
        return self :: render_personal_calendar_events($parent, $publications, $from_date, $to_date);
    }

    static public function get_connector_events($parent, $from_date, $to_date)
    {
        $events = array();

        $path = dirname(__FILE__) . '/connector/';
        $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, false);
        foreach ($files as $file)
        {
            $application = str_replace('_connector.class.php', '', $file);
            $application = str_replace(PersonalCalendarManager :: APPLICATION_NAME . '_', '', $application);
            $application = Utilities :: camelcase_to_underscores($application);

            if (WebApplication :: is_active($application))
            {
                $file_class = split('.class.php', $file);
                require_once $path . $file;
                $class = Utilities :: underscores_to_camelcase($file_class[0]);

                $connector = new $class();
                $events = array_merge($events, $connector->get_events($parent->get_user(), $from_date, $to_date));
            }
        }

        return $events;
    }

    static public function get_user_shared_events($parent, $from_date, $to_date)
    {
        $events = array();
        $user_groups = $parent->get_user()->get_groups(true);

        $pcdm = PersonalCalendarDatamanager :: get_instance();
        $conditions = array();
        $conditions[] = new EqualityCondition('user_id', $parent->get_user()->get_id(), 'publication_user');
        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition('group_id', $user_groups, 'publication_group');
        }
        $condition = new OrCondition($conditions);
        $publications = $pcdm->retrieve_shared_personal_calendar_publications($condition);

        return self :: render_personal_calendar_events($parent, $publications, $from_date, $to_date, 'SharedEvents');
    }

    public function render_personal_calendar_events($parent, $publications, $from_date, $to_date, $source = PersonalCalendarManager :: APPLICATION_NAME)
    {
        $events = array();
        $query = Request :: post('query');

        while ($publication = $publications->next_result())
        {
            $parser = PersonalCalendarEventParser :: factory($parent, $publication, $from_date, $to_date);
            $events = array_merge($events, $parser->get_events());
        }
        return $events;
    }
}
?>