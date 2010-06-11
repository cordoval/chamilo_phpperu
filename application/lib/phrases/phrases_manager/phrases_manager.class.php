<?php
/**
 * $Id: personal_calendar_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager
 */
require_once dirname(__FILE__) . '/../phrases_publication.class.php';
require_once dirname(__FILE__) . '/../phrases_data_manager.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PhrasesManager extends WebApplication
{
    const APPLICATION_NAME = 'phrases';

    const PARAM_PHRASE_ID = 'phrase';

    const ACTION_VIEW_START = 'start';
    const ACTION_MANAGE_PHRASES = 'manage';
    const ACTION_MANAGE_MASTERY_LEVELS = 'mastery';
    const ACTION_MANAGE_CATEGORIES = 'categories';
    const ACTION_TAKE_ASSESSMENT = 'take';

    /**
     * Constructor
     * @param int $user_id
     */
    public function PhrasesManager($user)
    {
        parent :: __construct($user);
    }

    /**
     * Runs the personal calendar application
     */
    public function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_MANAGE_PHRASES :
                $component = $this->create_component('Manager');
                break;
            case self :: ACTION_MANAGE_MASTERY_LEVELS :
                $component = $this->create_component('Leveler');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_TAKE_ASSESSMENT :
                $component = $this->create_component('Taker');
                break;
            case self :: ACTION_VIEW_START :
                $component = $this->create_component('Starter');
                break;
            default :
                $this->set_action(self :: ACTION_VIEW_START);
                $component = $this->create_component('Starter');
        }
        $component->run();
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function retrieve_phrases_publication($publication_id)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->retrieve_phrases_publication($publication_id);
    }

    function retrieve_phrases_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->retrieve_phrases_publications($condition, $order_by, $offset, $max_objects);
    }

    function count_phrases_publications($condition = null)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->count_phrases_publications($condition);
    }

    function get_publication_deleting_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PHRASE_ID => $publication->get_id()));
    }

    function get_publication_editing_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_PHRASE_ID => $publication->get_id()));
    }

    function get_publication_viewing_url($publication)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_PHRASE;
        $parameters[self :: PARAM_PHRASE_ID] = $publication->get_id();
        $parameters[Application :: PARAM_APPLICATION] = self :: APPLICATION_NAME;

        return $this->get_link($parameters);
    }

    function get_user_info($user_id)
    {
        return UserDataManager :: get_instance()->retrieve_user($user_id);
    }

    function retrieve_phrases_mastery_level($mastery_level_id)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->retrieve_phrases_mastery_level($mastery_level_id);
    }

    function retrieve_phrases_mastery_levels($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->retrieve_phrases_mastery_levels($condition, $order_by, $offset, $max_objects);
    }

    function count_phrases_mastery_levels($condition = null)
    {
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->count_phrases_mastery_levels($condition);
    }
}
?>