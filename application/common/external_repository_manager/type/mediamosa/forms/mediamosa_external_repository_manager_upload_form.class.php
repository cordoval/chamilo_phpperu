<?php
/**
 * Description of mediamosa_external_repository_upload_form
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerUploadForm extends FormValidator {

    private $params;
    private $application;
    private $upload_ticket;
    function MediamosaExternalRepositoryManagerUploadForm($upload_ticket, $params, $application)
    {
        $this->params = $params;
        $this->application = $application;
        $this->upload_ticket = $upload_ticket;

        $this->connector = $this->application->get_external_repository_connector();
        $this->method = ExternalRepositorySetting :: get('upload_method', $this->connector->get_external_repository_instance_id()) ;

        parent :: __construct('mediamosa_upload', $this->method, $this->upload_ticket['action']);

        $this->build_upload_form($method);
    }

    function build_upload_form()
    {
       
        $params = array();
        //transcode profiles
        if($profiles = $this->connector->retrieve_mediamosa_transcoding_profiles())
        {
            foreach($profiles as $profile_id => $profile)
            {
                $this->addElement('hidden', 'transcode[]', $profile_id);
                $params['transcode'][] = $profile_id;
            }
        }
        else
        {
            $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->application->redirect(Translation::get('NoTranscodeProfiles'), 1, $params);
        }

        //$this->addElement('hidden', 'upload_ticket', $this->upload_ticket['upload_ticket']);
        $this->addElement('hidden', 'redirect_uri', 'http://' . $_SERVER['SERVER_NAME'] . $this->application->get_url($this->params, true));
        $this->addElement('hidden', 'create_still', 'TRUE');
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));
    	
       if($this->method == MediamosaExternalRepositoryConnector :: METHOD_PUT)
        {
            /*$connector_cookie = $this->connector->get_connector_cookie();
            $this->addElement('hidden', $connector_cookie['name'],$connector_cookie['value']);

            $link = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'plugin/jquery/uploadify2/jquery.uploadify.v2.1.0.min.js');
            $link .= ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'plugin/jquery/uploadify2/swfobject.js');
            $link .= ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/external_repository_manager/type/mediamosa/javascript/handle_form.js');
            $this->addElement('static', 'uploadify', Translation :: get('UploadVideo'), $link . '<div id="uploadify"></div>');*/

           $params['create_still'] = 'TRUE';

          

           if($this->connector->mediamosa_put_upload(Path::get(WEB_PATH) . 'application/common/external_repository_manager/type/mediamosa/test/mvi_5988.avi',  $this->upload_ticket['action'], $params))
           {
                $this->application->redirect(Translation :: get('succes'), 0, $this->params);
           }
           else
           {
                $this->application->redirect(Translation :: get('failed'), 1, $this->params);
           }
        }

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Upload'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

}
?>
