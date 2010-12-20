<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ExternalSync;
use common\libraries\fedora_object_meta;
use common\libraries\SWITCH_object_meta;
use common\libraries\PlatformSetting;
use repository\RepositoryDataManager;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use repository\ContentObject;
use common\libraries\Session;

use Exception;

/**
 * Synchronize a Chamilo Document object by writing Chamilo's content to Fedora.
 * Works only for Document objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerExternalSyncerComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if ($api = $this->create_api_component())
        {
            return $api->run();
        }

        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        if (empty($id))
        {
            $this->redirect_to_browser();
        }

        $object = $this->retrieve_external_repository_object($id);
        if ($object == null)
        {
            $success = FedoraExternalRepositoryManager :: delete_synchronization_data($id, $this->get_external_repository_id());
        }
        else
            if (! $object->is_importable() && ($object->get_synchronization_status() == ExternalSync :: SYNC_STATUS_EXTERNAL || $object->get_synchronization_status() == ExternalSync :: SYNC_STATUS_CONFLICT))
            {
                $success = $this->synchronize_external_repository_object($object);
            }
            else
            {
                $this->redirect_to_browser();
            }

        $message = $success ? Translation :: get('Succes') : Translation :: get('Failed');
        $this->redirect_to_editor($message, ! $success);
    }

    function redirect_to_browser($message = '', $is_error_message = false)
    {
        $params = $this->get_parameters();
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = null;
        $this->redirect($message, $is_error_message, $params);
    }

    function redirect_to_editor($message = '', $is_error_message = false)
    {
        $params = $this->get_parameters();
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $this->redirect($message, $is_error_message, $params);
    }

    function get_external_repository_id()
    {
        return Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
    }

    function synchronize_external_repository_object(ExternalRepositoryObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();

        if ($this->update_document($external_object, $content_object))
        {
            $external_object = $this->get_external_repository_manager_connector()->retrieve_external_repository_object($external_object->get_id());
            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            return $synchronization_data->update();
        }
        else
        {
            return false;
        }
    }

    function update_document(ExternalRepositoryObject $external_object, Document $document)
    {
        $pid = $external_object->get_id();
        $label = $document->get_title();

        $mime_type = $document->get_mime_type();
        $path = Path :: get(SYS_FILE_PATH) . 'repository/' . $document->get_path();

        $name = $document->get_filename();

        if ($document->is_image())
        {
            $this->update_thumbnail($pid, $name, $path, $mime_type);
        }

        $this->update_data($pid, $name, $path, $mime_type);
        $this->update_label($pid, $label);

        $data = $external_object->get_metadata();
        $description = $document->get_description();
        $description = $this->html2txt($description);
        $description = html_entity_decode($description);
        $description = trim($description);
        $description = utf8_encode($description);

        $this->update_metadata($pid, $description, $data);
        return true;
    }

    function update_label($pid, $label)
    {
        $fedora = $this->get_external_repository_manager_connector()->get_fedora();
        $fedora->modify_object($pid, $label);
        try
        {
            $fedora->modify_datastream($pid, 'DS1', $label);
        }
        catch (Exception $e)
        {
            //ignore errors. Happens when the DS1 datastream does not exists
        }
    }

    function update_thumbnail($pid, $name, $path, $mime_type)
    {
        $connector = $this->get_external_repository_manager_connector();
        $connector->update_thumbnail($pid, $name, $path, $mime_type);
    }

    function update_metadata($pid, $description, $data)
    {
        $meta = new fedora_object_meta();
        $meta->pid = $pid;
        $meta->label = $data['title'];

        $switch = new SWITCH_object_meta();
        foreach ($data as $key => $value)
        {
            $switch->{$key} = $data[$key];
        }

        $switch->title = $data['title'];
        $switch->aaiid = FedoraExternalRepositoryManagerConnector :: get_owner_id();
        $switch->rights = isset($data['edit_rights']) ? $data['edit_rights'] : 'private';
        $switch->accessRights = isset($data['access_rights']) ? $data['access_rights'] : 'private';
        $switch->rightsHolder = $data['author'];
        $switch->publisher = PlatformSetting :: get('institution', 'admin');
        $switch->discipline = $data['subject'];
        $switch->discipline_text = $data['subject_dd']['subject_text'];
        $switch->creator = isset($data['author']) ? $data['author'] : $this->get_user()->get_fullname();
        $switch->description = $description;
        $switch->collections = $data['collection'];
        $switch->source = $this->get_external_repository_manager_connector()->get_datastream_content_url($meta->pid, 'DS1');

        $connector = $this->get_external_repository_manager_connector();
        $fedora = $connector->get_fedora();

        $content = SWITCH_object_meta :: get_rels_ext($meta, $switch);
        $fedora->update_datastream($pid, 'RELS-EXT', 'Relationships to other objects', $content, 'application/rdf+xml');
        $content = SWITCH_object_meta :: get_chor_dc($meta, $switch);
        $fedora->update_datastream($pid, 'CHOR_DC', 'SWITCH CHOR_DC record for this object', $content, 'text/xml');
    }

    function update_data($pid, $name, $path, $mime_type)
    {
        $fedora = $this->get_external_repository_manager_connector()->get_fedora();
        $content = file_get_contents($path);
        $fedora->update_datastream($pid, 'DS1', $name, $content, $mime_type, false);
    }

    function html2txt($html)
    {
        $search = array(
                '@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                '@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
                '@<[?]php[^>].*?[?]>@si',  //scripts php
                '@<[?][^>].*?[?]>@si',  //scripts php
                '@<[\/\!]*?[^<>]*?>@si',  // Strip out HTML tags
                '@<![\s\S]*?--[ \t\n\r]*>@')// Strip multi-line comments including CDATA
;
        $result = preg_replace($search, '', $html);
        return $result;
    }

}

?>