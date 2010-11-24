<?php
namespace application\phrases;

/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */

class PhrasesManagerManagerComponent extends PhrasesManager
{  
    private $form;
    
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        PhrasesPublicationManager :: launch($this);
    }
}
?>