<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rights_editor
 *
 * @author Pieterjan Broekaert
 */
class rights_editor
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ProfilerManager :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('BrowseProfiles')));

        $category = Request :: get('category');
    	$publications = Request :: get(ProfilerManager::param_);
        $this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $publications);
        $this->set_parameter('category', $category);
    }
}

?>
