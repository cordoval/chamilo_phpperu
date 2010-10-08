<?php
/**
 * Description of uploaderclass
 *
 * @author jevdheyd
 */

require_once(dirname(__FILE__).'/../forms/mediamosa_external_repository_manager_form.class.php');
require_once(dirname(__FILE__).'/../forms/mediamosa_external_repository_manager_upload_form.class.php');
require_once Path :: get_repository_path() . 'lib/external_repository_user_quotum.class.php';

class MediamosaExternalRepositoryManagerUploaderComponent extends MediamosaExternalRepositoryManager {

    function run()
    {
        //check quota if user exists
        //otherwise create one and set quota
        $connector = $this->get_external_repository_connector();
        $user = Session :: get_user_id();
        $over_quota = true;

        if($mediamosa_user = $connector->retrieve_mediamosa_user($user))
        {
            if((string) $mediamosa_user->user_over_quota == 'false' && (string) $mediamosa_user->quota_available_mb != '0')
            {
                $over_quota = false;
            }
        }
        else
        {
            $rdm = RepositoryDataManager :: get_instance();
            if($quotum = $rdm->retrieve_external_repository_user_quotum($user, Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY)))
            {
                if($connector->set_mediamosa_user_quotum($user, $quotum->get_quotum()))
                {
                    $over_quota = false;
                }
            }
            else
            {
                if($connector->set_mediamosa_default_user_quotum($user))
                {
                    $over_quota = false;
                }
            }
        }

        if(!$over_quota)
        {
            $form = new MediamosaExternalRepositoryManagerForm(MediamosaExternalRepositoryManagerForm :: TYPE_CREATE, $this->get_url(), $this);
            if($form->validate())
            {
               //if - create necessary objects and upload metadata
               if($ticket_response = $form->prepare_upload())
               {
                   $params = array();
                   $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                   $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY] = $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
                   $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $ticket_response['asset_id'];
                   $params['message'] = Translation :: get('UploadSuccess') . '. ' . Translation :: get('TranscodeNeeded');

                   //generate uploadform
                   $uploadform = new MediamosaExternalRepositoryManagerUploadForm($ticket_response, $params, $this);

                   $this->display_header($trail, false);

                   $uploadform->display();
                   $this->display_footer();
               }
               else
               {
                   $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                   $this->redirect(Translation::get('failed'), 1, $params);
               }
            }
            else
            {
                $this->display_header($trail, false);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation::get('OverQuota'), 1, $params);

        }
     }
}
?>
