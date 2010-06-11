<?php
/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */

class PhrasesManagerStarterComponent extends PhrasesManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->display_header();

        echo '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES)) . '">Phrases</a><br /><br />';
        echo '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_MASTERY_LEVELS)) . '">Mastery Levels</a><br /><br />';
        echo '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_TAKE_ASSESSMENT)) . '">Take</a><br /><br />';

        $this->display_footer();
    }
}
?>