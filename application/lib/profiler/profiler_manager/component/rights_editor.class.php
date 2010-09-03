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
        $category = Request :: get('category');
        $publication_id = Request :: get(ProfilerManager::PARAM_PROFILE_ID);

        $trail->add(new Breadcrumb($this->get_rights_editor_url($category)));

        $this->set_parameter('category', $category);
        if (!$publication_id) // the location type is category
        {
            if ($category == 0)
            {
                $location[] = ProfilerRights::get_profiler_subtree_root();
                $edit_rights_right = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT_RIGHTS, 0, 0);
            }
            else
            {
                $location[] = ProfilerRights::get_location_by_identifier_from_profiler_subtree($category, ProfilerRights::TYPE_CATEGORY);
                $edit_rights_right = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT_RIGHTS, $category, ProfilerRights::TYPE_CATEGORY);
            }
        }
        else //location type is publication
        {
            $location[] = ProfilerRights::get_location_by_identifier_from_profiler_subtree($publication_id, ProfilerRights::TYPE_PUBLICATION);
            $edit_rights_right = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT_RIGHTS, $publication_id, ProfilerRights::TYPE_PUBLICATION);;
        }

        if (!$edit_rights_right)
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
