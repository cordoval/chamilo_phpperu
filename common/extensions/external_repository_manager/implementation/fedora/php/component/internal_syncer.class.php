<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use repository\content_object\document\Document;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\Redirect;
use common\libraries\PlatformSetting;
use common\libraries\StringUtilities;
use repository\RepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ExternalSync;
use common\libraries\Filesystem;

/**
 * Synchronize a Fedora object by writing Fedora's content to Chamilo.
 * Works only for Document objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerInternalSyncerComponent extends FedoraExternalRepositoryManager {

    function run() {
        if ($api = $this->create_api_component()) {
            return $api->run();
        }

        $id = Request::get(self::PARAM_EXTERNAL_REPOSITORY_ID);
        if ($id) {
            $object = $this->retrieve_external_repository_object($id);

            if (!$object->is_importable() && ($object->get_synchronization_status() == ExternalSync::SYNC_STATUS_INTERNAL || $object->get_synchronization_status() == ExternalSync::SYNC_STATUS_CONFLICT)) {
                $succes = $this->synchronize_internal_repository_object($object);
                $params = $this->get_parameters();
                $params[self::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;

                if ($succes) {
                    $this->redirect(Translation::get('Succes'), false, $params);
                } else {
                    $this->redirect(Translation::get('Failed'), true, $params);
                }
            }
        }
        $params = $this->get_parameters();
        $params[ExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager::ACTION_VIEW_EXTERNAL_REPOSITORY;
        $this->redirect(null, false, $params);
    }

    function synchronize_internal_repository_object(ExternalRepositoryObject $external_object) {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();

        $this->write_standard_properties($external_object, $content_object);
        $this->write_document_properties($external_object, $content_object);

        if ($content_object->update()) {
            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            return $synchronization_data->update();
        } else {
            return false;
        }
    }

    function write_standard_properties(ExternalRepositoryObject $external_object, $document) {
        $document->set_title($external_object->get_title());
        $document->set_description($this->get_description($external_object));
    }

    function write_document_properties(ExternalRepositoryObject $external_object, $document) {
        if (!$document instanceof Document) {
            return false;
        }

        $pid = $external_object->get_id();
        $ds = $this->get_datastream($external_object);
        $dsID = $ds->get_dsID();
        $ext = $ds->get_extention();
        $ext = $ext ? '.' . $ext : '';
        $content = $this->retrieve_datastream_content($pid, $dsID);
        $filename = $ds->get_title() . $ext;

        $path = Path::get_temp_path() . '/f' . sha1(Session::get_user_id() . time()) . $ext;
        $write_to_file = Filesystem::write_to_file($path, $content);

        $document->set_filename($filename);
        $document->set_temporary_file_path($path);
        return true;
    }

    protected function is_description_required() {
        return PlatformSetting::get('description_required', 'repository');
    }

    protected function get_description(ExternalRepositoryObject $external_object) {
        $result = $external_object->get_description();
        $result = $this->is_description_required() && empty($result) ? '-' : $result;
        return $result;
    }

    /**
     * Returns the datastream object to import.
     * If a datastream ID has been provided returns it. Otherwise returns the first non-system datastream.
     *
     * @param $external_object
     */
    protected function get_datastream(FedoraExternalRepositoryObject $external_object) {
        if ($dsID = Request::get(FedoraExternalRepositoryManager::PARAM_DATASTREAM_ID)) {
            return $external_object->get_datastreams($dsID);
        } else {
            $dss = $external_object->get_datastreams();
            foreach ($dss as $ds) {
                if (!$ds->is_system_datastream()) {
                    return $ds;
                }
            }
        }
        return false;
    }

    /**
     * Returns the datastream content as string
     *
     * @param unknown_type $pid
     * @param unknown_type $dsID
     */
    protected function retrieve_datastream_content($pid, $dsID) {
        $connector = $this->get_external_repository_manager_connector();
        $result = $connector->retrieve_datastream_content($pid, $dsID);
        return $result;
    }

}

?>