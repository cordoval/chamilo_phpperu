<?php
/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/mastery_level_manager/mastery_level_manager.class.php';

class PhrasesManagerLevelerComponent extends PhrasesManager
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $mastery_level_manager = new PhrasesMasteryLevelManager($this);
        $mastery_level_manager->run();
    }
}
?>