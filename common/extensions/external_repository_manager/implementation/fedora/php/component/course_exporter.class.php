<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Session;
use application\weblcms\WeblcmsDataManager;
use repository\ContentObjectExport;
use common\libraries\Filesystem;
use common\libraries\fedora_object_meta;
use common\libraries\SWITCH_object_meta;
use common\libraries\PlatformSetting;

require_once dirname(__FILE__) . '/../forms/fedora_course_selection_form.class.php';
require_once dirname(__FILE__) . '/../forms/fedora_metadata_form.class.php';
require_once dirname(__FILE__) . '/../forms/fedora_confirm_form.class.php';
require_once dirname(__FILE__) . '/../forms/fedora_course_publication_selection_form.class.php';
require_once Path::get_application_path() . 'weblcms/php/lib/course_type/course_type_tool.class.php';

/**
 * Export a course's content to Fedora in IMS CP format.
 * Wizard: select a course, select course's publications, enter metadata, confirm, send.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerCourseExporterComponent extends FedoraExternalRepositoryManager {
    const ACTION_SELECT_COURSE = 'action_select_course';
    const ACTION_SELECT_COURSE_PUBLICATIONS = 'action_select_course_publications';
    const ACTION_METADATA = 'action_metadata';
    const ACTION_EXPORT = 'action_export';
    const ACTION_CONFIRM = 'action_confirm';
    const ACTION_SEND = 'action_send';

    function run() {
        if (get_class($this) != __CLASS__) {
            $this->step();
        } else if ($api = $this->create_api_component()) {
            return $api->run();
        } else {
            return $this->step();
        }
    }

    /**
     * Returns data from the previous step. Persit across redirection.
     */
    protected function get_data() {
        $key = 'fedora_data';
        $result = Session::retrieve($key);
        return unserialize($result);
    }

    /**
     * Set data for the next step. Persist across redirection.
     */
    protected function set_data($value) {
        $key = 'fedora_data';
        Session::register($key, Serialize($value));
    }

    /**
     * Redirect to the next step.
     *
     * @param any $data data to be passed from one step to another
     */
    protected function move_next($data=false) {
        if ($data) {
            $this->set_data($data);
        }
        $next_action = $this->next_action();
        $parameters = $this->get_wizard_parameters($next_action);
        Redirect::url($parameters);
    }

    /**
     * Performs a step.
     * Stay on the current step if the form is not validated.
     * If a function exists for the step executes it.
     * Move to the next step on success.
     *
     *
     * @param unknown_type $action
     */
    protected function step($action=false) {
        $action = $action ? $action : $this->get_wizard_action();
        $data = $this->get_data();

        $form = $this->create_form($action, $data);
        if ($form->validate()) {
            $f = array($this, $action);

            if (is_callable($f)) {
                $data = $form->exportValues();
                $result = call_user_func($f, $data);

                $this->move_next($result);
            } else {
                $data = $form->exportValues();
                $this->move_next($result);
            }
        } else {
            $this->display($form);
        }
    }

    /**
     * Display a form. With header and footer.
     *
     * @param unknown_type $form
     */
    protected function display($form=false) {
        $form = $form ? $form : $this->get_form();
        $this->display_header($trail = null, false);
        $form->display();
        $this->display_footer();
    }

    /**
     * Returns the url for the action.
     *
     * @param string $action if not provided default to the current action
     */
    protected function get_wizard_url($action=false) {
        $parameters = $this->get_wizard_parameters($action);
        $result = Redirect::get_url($parameters, $filter);
        return $result;
    }

    /**
     * Returns the url parameters for the action.
     *
     * @param string $action if not provided default to the current action
     */
    protected function get_wizard_parameters($action=false) {
        $parameters = $_GET;

        $filter = array();
        if ($action) {
            $parameters[self::PARAM_WIZARD_ACTION] = $action;
        }

        if ($action == self::ACTION_SELECT_COURSE) {
            $filter[] = self::PARAM_COURSE_ID;
        }

        if ($filter) {
            $url_parameters = array();
            foreach ($parameters as $key => $value) {
                if (!in_array($key, $filter)) {
                    $url_parameters[$key] = $value;
                }
            }
            $result = $url_parameters;
        } else {
            $result = $parameters;
        }
        return $result;
    }

    /**
     * Returns the current action for the component.
     *
     */
    protected function get_wizard_action() {
        $result = Request::get(self::PARAM_WIZARD_ACTION);
        $result = $result ? $result : self::ACTION_SELECT_COURSE;
        return $result;
    }

    /**
     * Returns the action to be executed after an action.
     *
     * @param string $action
     * @return string
     */
    protected function next_action($action=false) {
        $action = $action ? $action : $this->get_wizard_action();
        $steps[self::ACTION_SELECT_COURSE] = self::ACTION_SELECT_COURSE_PUBLICATIONS;
        $steps[self::ACTION_SELECT_COURSE_PUBLICATIONS] = self::ACTION_METADATA;
        $steps[self::ACTION_METADATA] = self::ACTION_CONFIRM;
        $steps[self::ACTION_CONFIRM] = self::ACTION_SEND;
        $steps[self::ACTION_SEND] = self::ACTION_SELECT_COURSE;
        return $steps[$action];
    }

    /**
     * Returns the form to be displayed for a step.
     *
     * @param string $action action for the step
     * @param any $p1 form constructor parameter
     */
    protected function create_form($action=false, $p1=null) {
        $action = $action ? $action : $this->get_wizard_action();
        $p1 = $p1 ? $p1 : $this->get_data();
        $parameters = $this->get_wizard_parameters($action);
        switch ($action) {
            case self::ACTION_SELECT_COURSE_PUBLICATIONS:
                $result = new FedoraCoursePublicationSelectionForm($this, $parameters, $p1);
                return $result;

            case self::ACTION_METADATA:
                $result = new FedoraMetadataForm($this, $parameters, $p1);
                return $result;

            case self::ACTION_CONFIRM:
                $result = new FedoraConfirmForm($this, $parameters, $p1);
                return $result;

            default:
            case self::ACTION_SELECT_COURSE:
                $result = new FedoraCourseSelectionForm($this, $parameters, $p1);
                return $result;
        }
    }

    /**
     * Action to be performed once the form has been validated.
     *
     * @param array $data
     */
    protected function action_select_course_publications($data) {
        if ($file = $this->export($data)) {
            $data['file'] = $file;
        }
        return $data;
    }

    /**
     * Action to be performed once the form has been validated.
     *
     * @param array $data
     */
    protected function action_metadata($data) {
        if ($label = isset($data['title']) ? $data['title'] : false) {
            $connector = $this->get_external_repository_manager_connector();
            $object = $connector->get_object_by_label($label);
            $data['pid'] = $object['pid'];

            $name = 'f' . sha1('fedora_temp_thumbnail' . Session::get_user_id() . uniqid()) . '.tmp';
            $path = Path::get_temp_path() . $name;
            $href = Path::get(WEB_TEMP_PATH) . $name;
            $file = $_FILES['thumbnail'];
            if ($file['tmp_name']) {
                Filesystem::move_file($file['tmp_name'], $path);
                $file['tmp_name'] = $path;
                $file['path'] = $path;
                $file['href'] = $href;

                $data['thumbnail'] = $file;
            } else {
                $data['thumbnail'] = false;
            }
        }
        return $data;
    }

    /**
     * Action to be performed once the form has been validated.
     *
     * @param array $data
     */
    protected function action_confirm($data) {
        $result = $this->send($data);
        Session::unregister('fedora_data'); //avoid leaving unnecessary data in the session cache.
        if ($result) {
            $message = $result = Translation::get('ExternalRepositoryExportSuccess');
            $error = '';
        } else {
            $error = $result = Translation::get('ExternalRepositoryExportFailure');
            $message = '';
        }

        $parameters = $this->get_wizard_parameters();
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        unset($parameters[self::PARAM_COURSE_ID]);
        unset($parameters[self::PARAM_WIZARD_ACTION]);

        $this->redirect($message, $error, $parameters);
        return $result;
    }

    /**
     * Export a course to a file.
     *
     * @param string $form
     * @return array pointing to the file or false if export failed
     */
    protected function export($values) {
        $dm = WeblcmsDataManager::get_instance();
        $publication_ids = array_keys($values['publications']);
        $objects = array();
        foreach ($publication_ids as $id) {
            $publication = $dm->retrieve_content_object_publication($id);
            $objects[] = $publication->get_content_object();
        }
        $exporter = ContentObjectExport::factory('cp', $objects);
        if ($path = $exporter->export_content_object()) {
            $name = Session::get_user_id() . '/fedora_up/' . 'course.zip';
            $filepath = Path::get(SYS_TEMP_PATH) . $name;
            $webpath = Path::get(WEB_TEMP_PATH) . $name;

            $copy_file = Filesystem::copy_file($path, $filepath, true);
            Filesystem::remove($path);

            $result = array();
            $result['title'] = Translation::get('Download');
            $result['path'] = $filepath;
            $result['href'] = $webpath;
        } else {
            return false;
        }
        return $result;
    }

    /**
     * Send to fedora
     *
     * @param array $data array containing the path to the file as well as metadata used to export.
     */
    protected function send($data) {
        $connector = $this->get_external_repository_manager_connector();

        $pid = isset($data['pid']) ? $data['pid'] : false;
        $isnew = empty($pid);
        $path = $data['file']['path'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mime = ext_to_mimetype($ext);
        $meta = new fedora_object_meta();
        $meta->pid = $isnew ? $connector->get_nextPID() : $pid;
        $meta->label = $data['title'];
        $meta->mime = $mime;
        $meta->owner = $connector->get_owner_id();
        $content = file_get_contents($path);
        Filesystem::remove($path);

        if ($thumbnail = @$data['thumbnail']) {
            $meta->thumbnail_label = $thumbnail['name'];
            $meta->thumbnail_mime = $thumbnail['type'];
            $meta->thumbnail = file_get_contents($thumbnail['path']);
        }

        if ($pid) {
            $connector->purge_external_repository_object($pid);
        }
        $foxml = $this->content_to_foxml($content, $meta, $data);
        $result = $connector->ingest($foxml, $meta->pid, $meta->label, $meta->owner);
        Filesystem::remove($path);
        return $result;
    }

    /**
     * Package content and metadata into a FOXML representation ready to be ingested into Fedora.
     *
     * @param string $content file's content
     * @param $meta basic Fedora metadata
     * @param array $data additional metadata
     */
    protected function content_to_foxml($content, $meta, $data) {
        $switch = new SWITCH_object_meta();
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $switch->{$key} = $data[$key];
            }
        }
        $switch->aaiid = $meta->owner;
        $switch->rights = isset($data['edit_rights']) ? $data['edit_rights'] : 'private';
        $switch->accessRights = isset($data['access_rights']) ? $data['access_rights'] : 'private';
        $switch->rightsHolder = $data['author'];
        $switch->publisher = PlatformSetting::get('institution', 'admin');
        $switch->discipline = $data['subject'];
        $switch->discipline_text = $data['subject_dd']['subject_text'];
        $switch->creator = $data['author'];
        $switch->description = $data['description'];
        $switch->collections = $data['collection'];
        $switch->source = $this->get_external_repository_manager_connector()->get_datastream_content_url($meta->pid, 'DS1');

        return SWITCH_object_meta::content_to_foxml($content, $meta, $switch);
    }

}

?>