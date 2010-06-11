<?php
/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/publication_manager/publication_manager.class.php';

class PhrasesManagerManagerComponent extends PhrasesManager
{  
    private $form;
    
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_manager = new PhrasesPublicationManager($this);
        $publication_manager->run();
    }
}
?>