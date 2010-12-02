<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;

use common\libraries\Utilities;
use common\libraries\FormValidator;
use common\libraries\ResourceManager;

use repository\ExternalSetting;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\libraries\Path;
use common\libraries\Translation;
/**
 * Description of mediamosa_external_repository_upload_form
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerUploadForm extends FormValidator
{

    private $params;
    private $application;
    private $upload_ticket;

    function __construct($upload_ticket, $params, $application)
    {
        $this->params = $params;
        $this->application = $application;
        $this->upload_ticket = $upload_ticket;

        $this->connector = $this->application->get_external_repository_manager_connector();
        $this->method = ExternalSetting :: get('upload_method', $this->connector->get_external_repository_instance_id());

        parent :: __construct('mediamosa_upload', $this->method, $this->upload_ticket['action']);

        $this->build_upload_form($method);
    }

    function build_upload_form()
    {

        $params = array();
        //transcode profiles
        if ($profiles = $this->connector->retrieve_mediamosa_transcoding_profiles())
        {
            foreach ($profiles as $profile_id => $profile)
            {
                $this->addElement('hidden', 'transcode[]', $profile_id);
                $params['transcode'][] = $profile_id;
            }
        }
        else
        {
            $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->application->redirect(Translation :: get('NoTranscodeProfiles'), 1, $params);
        }

        //$this->addElement('hidden', 'upload_ticket', $this->upload_ticket['upload_ticket']);
        $this->addElement('hidden', 'redirect_uri', 'http://' . $_SERVER['SERVER_NAME'] . $this->application->get_url($this->params),array('id' => 'redirect_uri'));
        $this->addElement('hidden', 'create_still', 'TRUE');
        //$apc_upload_progress_id = rand(0,10000);
        //$this->addElement('hidden', 'APC_UPLOAD_PROGRESS', $apc_upload_progress_id);
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));
        //$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/swfobject.js'));
        //$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/jquery.uploadify.v2.1.0.min.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/extensions/external_repository_manager/implementation/mediamosa/resources/javascript/handle_upload.js'));
        //$this->addElement('static', 'uploadify', Translation :: get('UploadDocument'), '<div id="uploadify"></div>');
        //$this->addElement('html', '<div id="uploadprogress"></div><script type="text/javascript">var apc_upload_progress_id = "' . $apc_upload_progress_id . '";
                                    //var mediamosa_url = "' . ExternalSetting :: get(MediamosaExternalRepositoryManager :: SETTING_URL, $this->application->get_external_repository()->get_id()) . '";</script>');
        //$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/extensions/external_repository_manager/implementation/mediamosa/resources/javascript/uploadprogress.js'));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

}
?>
