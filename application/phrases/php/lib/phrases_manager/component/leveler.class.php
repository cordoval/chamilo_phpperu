<?php
namespace application\phrases;

use common\libraries\WebApplication;

/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('phrases') . 'phrases_manager/component/mastery_level_manager/mastery_level_manager.class.php';

class PhrasesManagerLevelerComponent extends PhrasesManager
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        PhrasesMasteryLevelManager :: launch($this);
    }
}
?>