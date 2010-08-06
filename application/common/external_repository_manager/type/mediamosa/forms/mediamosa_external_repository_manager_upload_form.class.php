<?php
/**
 * Description of mediamosa_external_repository_upload_form
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerUploadForm extends FormValidator {

    private $redirect_uri;
    private $uploadprogress_url;
    private $application;
    
    function MediamosaExternalRepositoryManagerUploadForm($action, $redirect_uri, $uploadprogress_url, $application)
    {
        $this->redirect_uri = $redirect_uri;
        $this->uploadprogress_url = $uploadprogress_url;
        $this->application = $application;
        parent :: __construct('mediamosa_upload', 'post', $action);

        $this->build_upload_form();
    }

    function build_upload_form()
    {
        $connector = $this->application->get_external_repository_connector();

        //token
        //$this->addElement('hidden', 'token', $this->token);

        /*
         * upon creation no rights are defined so no need to inherit any rights through inherit-acl-rights
         */

        //transcode profiles
        if($profiles = $connector->retrieve_mediamosa_transcoding_profiles())
        {
            foreach($profiles as $profile_id => $profile)
            {
                $this->addElement('hidden', 'transcode[]', $profile_id);
            }
        }
        else
        {
            //TODO:jens->throw error :: no profiles found
        }

        //redirect uri
        $this->addElement('hidden', 'redirect_uri', $this->redirect_uri);

        //retranscode???
        //filename??
        //create still =true
        $this->addElement('hidden', 'create_still', 'TRUE');
    	$this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));

    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Upload'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>
