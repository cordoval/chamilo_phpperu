<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of content_object_share_browser
 *
 * @author Pieterjan Broekaert
 */
class RepositoryManagerContentObjectShareRightsBrowserComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        
    }

}

?>
