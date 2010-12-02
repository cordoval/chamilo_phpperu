<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use repository\ExternalSetting;

class MediamosaExternalRepositoryManagerConfigurerComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        
        ExternalRepositoryComponent :: launch($this);

        /*
         * perfectly working code to update aut_app of assets when slave_app_ids has changed
         * code should however be refactored to only updated assets that have aut_app set
         * because only assets specifically set to anonymous user have to be updated
         *
         * CQL should be added to ensure this
         */
//        $slave_apps = ExternalSetting :: get(MediamosaExternalRepositoryManager :: SETTING_SLAVE_APP_IDS, $this->get_external_repository()->get_id());
//
//        //update all assets if slave app_ids have changed - due to restrictions only up to 200 - maybe better iterate users
//        //check one asset (all have same aut_app settings)
//        $ok = false;
//
//        $check_assets = $this->get_external_repository_manager_connector()->retrieve_external_repository_objects('', '', '', 1);
//        $check_asset = $check_assets->next_result();
//        $id = $check_asset->get_id();
//        $owner = $check_asset->get_owner_id();
//        $acl = $this->get_external_repository_manager_connector()->retrieve_mediamosa_asset_rights($id,$owner);
//
//        if(count($acl['aut_app'])){
//
//            foreach($acl['aut_app'] as $aut_app)
//            {
//               $aut_apps = $aut_app . '|';
//            }
//
//            $aut_apps_check = rtrim($aut_apps, '|');
//            if($slave_apps != $aut_apps_check)
//            {
//                $ok =true;
//            }
//        }
//        elseif(!empty($slave_apps))
//        {
//            $ok = true;
//        }
//                if(!$assets = $this->get_external_repository_manager_connector()->retrieve_external_repository_objects('', '', $offset, '', true))
//
//        if($ok)
//        {
//            //keep on iterating till all assets are covered
//            $offset = 0;
//            do{
//                if(!$assets = $this->get_external_repository_connector()->retrieve_external_repository_objects('', '', $offset, '', true))
//                {
//                    $this->redirect(Translation :: get('SlaveAppIdsNotUpdated'), true);
//                }
//                $offset += 200;
//            }while($assets->size());
//        }
    }
}
?>