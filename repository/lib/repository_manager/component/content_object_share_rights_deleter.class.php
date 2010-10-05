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
class RepositoryManagerContentObjectShareRightsDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $content_object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        $is_in_shared_objects_browser = false; //to redirect to the right page

        $user_ids = Request :: get(RepositoryManager :: PARAM_TARGET_USER);
        if (!is_array($user_ids) && !is_null($user_ids))
        {
            $user_ids = array($user_ids);
        }

        $group_ids = Request :: get(RepositoryManager :: PARAM_TARGET_GROUP);
        if (!is_array($group_ids) && !is_null($group_ids))
        {
            $group_ids = array($group_ids);
        }

        $repo_data_manager = RepositoryDataManager :: get_instance();

        if ($user_ids) //delete the shares with selected users
        {
            foreach ($user_ids as $user_id)
            {
                $isDeleted = $repo_data_manager->delete_content_object_user_share_by_content_object_and_user_id($content_object_id, $user_id);
                if(!$isDeleted)
                    $failures++;
            }
        }
        else if ($group_ids) // delete the shares with selected groups
        {
            foreach ($group_ids as $group_id)
            {
                $isDeleted = $repo_data_manager->delete_content_object_group_share_by_content_object_and_group_id($content_object_id, $group_id);
                if(!$isDeleted)
                    $failures++;
            }
        }
        else //if no group or user selected, delete the share for everyone (delete was called upon in shared objects browser)
        {
            $is_in_shared_objects_browser = true;
            if (!is_array($content_object_id))
            {
                $ids = array($content_object_id);
            }
            foreach ($content_object_id as $id)
            {
                $isDeleted = $repo_data_manager->delete_all_content_object_user_shares_by_content_object_id($id);
                if(!$isDeleted)
                    $failures++;
                $isDeleted = $repo_data_manager->delete_all_content_object_group_shares_by_content_object_id($id);
                if(!$isDeleted)
                    $failures++;
            }
        }

        if($failures > 0)
        {
            $message = 'share(s) not deleted';
        }

        else
        {
            $message = 'share(s) deleted';
        }

        $parameters = $this->get_parameters();
        $parameters[Application :: PARAM_ACTION] = ($is_in_shared_objects_browser? RepositoryManager :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS : RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_SHARE_RIGHTS);

        $this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);

    }

    function get_additional_parameters()
    {
        $parameters[] = ContentObjectUserShare :: PROPERTY_USER_ID;
        $parameters[] = ContentObjectGroupShare :: PROPERTY_GROUP_ID;
        $parameters[] = RepositoryManager :: PARAM_CONTENT_OBJECT_ID;
        $parameters[] = RepositoryManager :: PARAM_CATEGORY_ID;
        $parameters[] = ContentObjectShare :: PARAM_TYPE;
        $parameters[] = RepositoryManager :: PARAM_SHOW_OBJECTS_SHARED_BY_ME;

        return $parameters;
    }

}

?>
