<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/profiler_manager/profiler_manager.class.php';

/**
 * Provides the rights and locations of the profiler application
 *
 * @author Pieterjan Broekaert
 */
class ProfilerRights
{
    const PUBLISH_RIGHT = 1;

    const TREE_TYPE_PROFILER = 1;

    function get_available_rights()
    {
        return array('Publish' => self :: PUBLISH_RIGHT);
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, ProfilerManager :: APPLICATION_NAME);
    }

}

?>
