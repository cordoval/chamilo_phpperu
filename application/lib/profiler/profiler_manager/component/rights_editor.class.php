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
class ProfilerManagerRightsEditorComponent extends ProfilerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ProfilerManager :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('BrowseProfiles')));

        $category = Request :: get('category');

        $this->set_parameter('category', $category);

        if ($category == 0)
        {
            $location[] = ProfilerRights::get_profiler_subtree_root();
            $edit_rights_right = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::EDIT_RIGHTS_RIGHT, 0, 0);

        }
        else
        {
            $location[] = ProfilerRights::get_location_by_identifier_from_profiler_subtree($category, ProfilerRights::TYPE_CATEGORY);
            $edit_rights_right = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::EDIT_RIGHTS_RIGHT, $this->get_category(), ProfilerRights::TYPE_CATEGORY);

        }

        if(!$edit_rights_right)
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $manager = new RightsEditorManager($this, $location);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        return ProfilerRights :: get_available_rights();
    }

}

?>
