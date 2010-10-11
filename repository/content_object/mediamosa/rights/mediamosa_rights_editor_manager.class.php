<?php

require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_connector.class.php';

class MediamosaRightsEditorManager extends RightsEditorManager
{
    private $sync;

    function run()
    {
        $parent = $this->get_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION);

        switch ($parent)
        {
            case self :: ACTION_BROWSE_RIGHTS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_SET_USER_RIGHTS :
                $component = $this->create_component('UserRightsSetter');
                break;
            case self :: ACTION_SET_GROUP_RIGHTS :
                $component = $this->create_component('GroupRightsSetter');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/content_object/mediamosa/rights/component/';
    }
    
    function update_mediamosa_rights()
    {
        $redm =  RepositoryDataManager :: get_instance();

        $locations = $this->get_locations();

        foreach($locations as $location)
        {
            $condition = new EqualityCondition(ExternalRepositorySync:: PROPERTY_CONTENT_OBJECT_ID, $location->get_identifier());
            $sync = $redm->retrieve_external_repository_sync($condition);

            $rdm = RightsDataManager :: get_instance();

            $external_repository = $sync->get_external_repository();
            $mmc = MediamosaExternalRepositoryConnector :: get_instance($external_repository);

            $rights = array();

    //        $user = Request :: get('user_id');
    //        $right = Request :: get('right_id');
    //        $locations = $this->get_locations();

            //$location = RepositoryRights :: get_location_id_by_identifier('content_object', Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID), Session :: get_user_id(), 'user_tree');

            $condition1 = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location->get_id());
            //$condition2 = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, RepositoryRights :: VIEW_RIGHT);
            $condition3 = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, RepositoryRights :: VIEW_RIGHT);

            $condition = new AndCondition($condition1, $condition3);
            $update = false;

            //users
            $rights_user_locations = $rdm->retrieve_user_right_locations($condition);


            if ($rights_user_locations)
            {
                $udm = UserDataManager :: get_instance();
                while ($rights_user_location = $rights_user_locations->next_result())
                {
                    $rights_user = $udm->retrieve_user($rights_user_location->get_user_id());

                    if(!$rights_user->is_anonymous_user())
                    {
                        $rights['aut_user'][] = $rights_user->get_id();
                        $update = true;
                    }
                    else
                    {
                        //master_slave
                        $slaves = explode('|', ExternalRepositorySetting :: get('slave_app_ids', $sync->get_external_repository_id()));

                        foreach($slaves as $slave)
                        {
                            $rights['aut_app'][] = $slave;
                            $update = true;
                        }
                    }
                }
            }

            //groups
            $rights_groups = $rdm->retrieve_group_right_locations($condition);

            if ($rights_groups)
            {
                while ($rights_group = $rights_groups->next_result())
                {
                    $rights['aut_group'][] = $rights_group->get_group_id();
                    $update = true;
                }
            }

            $user = $udm->retrieve_user(Session :: get_user_id());

            if($user->is_platform_admin())
            {
                $asset = $mmc->retrieve_mediamosa_asset($sync->get_external_repository_object_id());
                $owner_id = $asset->get_owner_id();
            }
            else
            {
                //if the user is not the original user updating the rights will not succeed
                $owner_id = Session :: get_user_id();
            }

            //update mediamosa
            if ($update) $mmc->set_mediamosa_asset_rights($sync->get_external_repository_object_id(), $rights, $owner_id);
            //$mmc->set_mediamosa_mediafile_rights($sync->get_external_repository_object_id(), $rights, $owner_id);
        }
        
        
        
    }

    function get_available_rights()
    {
       $array =  parent :: get_available_rights();
       unset($array['Reuse']);
       return $array;
    }
}
?>
