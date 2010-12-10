<?php
namespace repository\content_object\mediamosa;

use repository\ContentObjectShareForm;
use repository\RepositoryDataManager;
use repository\ExternalSetting;
use repository\ExternalSync;

use common\libraries\EqualityCondition;
use common\libraries\Session;

use common\extensions\external_repository_manager\implementation\mediamosa\MediamosaExternalRepositoryManagerConnector;
use common\extensions\external_repository_manager\implementation\mediamosa\MediamosaExternalRepositoryManager;

use user\UserDataManager;




class MediamosaContentObjectShareForm extends ContentObjectShareForm
{
    function __construct($form_type, $content_object_ids = array(), $user, $action)
    {
        parent :: __construct($form_type, $content_object_ids, $user, $action);
    }

    function synchronize_share()
    {
        $values = $this->exportValues();
        $user_ids = $values[self :: PARAM_TARGET_ELEMENTS][self :: PARAM_USER];
        $group_ids = $values[self :: PARAM_TARGET_ELEMENTS][self :: PARAM_GROUP];
        $right_id = $values[self :: PARAM_RIGHT];

        foreach($this->get_content_object_ids() as $content_object_id)
        {
            $redm =  RepositoryDataManager :: get_instance();
            
            $condition = new EqualityCondition(ExternalSync:: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
            $sync = $redm->retrieve_external_sync($condition);

            $external_repository = $sync->get_external();
            $mmc = MediamosaExternalRepositoryManagerConnector :: get_instance($external_repository);

            $udm = UserDataManager :: get_instance();

            $update = false;

            foreach($user_ids as $user_id)
            {
                $rights_user = $udm->retrieve_user($user_id);
                if(!$rights_user->is_anonymous_user())
                {
                    $rights['aut_user'][] = $mmc->get_mediamosa_user_id($rights_user->get_id());
                    $update = true;
                }
                else
                {
                    //master_slave
                    $slaves = explode('|', ExternalSetting :: get(MediamosaExternalRepositoryManager :: SETTING_SLAVE_APP_IDS, $sync->get_external_id()));

                    foreach($slaves as $slave)
                    {
                        $rights['aut_app'][] = $slave;
                        $update = true;
                    }
                }
            }

            foreach($group_ids as $group_id)
            {
                $rights['aut_group'][] = $mmc->get_mediamosa_group_id($group_id);
                $update = true;
            }

            $user = $udm->retrieve_user(Session :: get_user_id());

            //a platform admin can always change the rights of an asset
            if($user->is_platform_admin())
            {
                $asset = $mmc->retrieve_mediamosa_asset($sync->get_external_object_id());
                $owner_id = $asset->get_owner_id();
            }
            else
            {
                //if the user is not the original user updating the rights will not succeed
                $owner_id = Session :: get_user_id();
            }

            //update mediamosa
            if ($update)
            {
                return $mmc->set_mediamosa_asset_rights($sync->get_external_object_id(), $rights, $mm->get_mediamosa_user($owner_id));
            }
            //$mmc->set_mediamosa_mediafile_rights($sync->get_external_repository_object_id(), $rights, $owner_id);
        }
    }

    function create_content_object_share()
    {
        if($this->synchronize_share())
        {
            return parent :: create_content_object_share();
        }
        return false;
    }

    function update_content_object_share($target_user_ids = array(), $target_group_ids = array())
    {
        if($this->synchronize())
        {
            return parent :: update_content_object_share($target_user_ids, $target_group_ids);
        }
        return false;
    }
}

?>
