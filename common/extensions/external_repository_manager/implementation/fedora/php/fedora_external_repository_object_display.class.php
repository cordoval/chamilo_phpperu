<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\FileUtil;
use common\libraries\Theme;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Filesystem;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;
use repository\ExternalSetting;

/**
 * Provides a readonly interface that display thumbail, metadata and datastream.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay {

    function get_display_properties() {
        $properties = parent::get_display_properties();
        $object = $this->get_object();
        if ($value = $this->get_licence($object)) {
            $properties[Translation::get('License')] = $value;
        }
        if ($value = $object->get_creator()) {
            $properties[Translation::get('Creator')] = $value;
        }
        if ($value = $object->get_author()) {
            $properties[Translation::get('Author')] = $value;
        }
        if ($value = $object->get_subject_text()) {
            $properties[Translation::get('Subject')] = $value;
        }
        if ($value = $object->get_edit_rights()) {
            $properties[Translation::get('EditRights')] = Translation::get(ucfirst($value));
        }
        if ($value = $object->get_access_rights()) {
            $properties[Translation::get('Rights')] = Translation::get(ucfirst($value));
        }
        foreach ($properties as $key => $value) {
            $properties[$key] = $this->format_email($value);
        }

        return $properties;
    }

    function get_preview($is_thumbnail = false) {
        $object = $this->get_object();
        if ($object->has_datastream('THUMBNAIL')) {
            $ds = $object->get_datastreams('THUMBNAIL');
            $content = $object->get_datastream_content('THUMBNAIL');
            $ext = $ds->get_extention();
            $ext = $ext ? '.' . $ext : $ext;
            $filename = 'o' . $object->get_id() . $ext;
            $filename = str_replace(':', '_', $filename);
            $filename = Filesystem::create_safe_name($filename);

            $opath = 'fedora/thumb/' . $filename;
            $temp = Path::get_temp_path() . $opath;
            if (Filesystem::write_to_file($temp, $content)) {
                $image_url = Path::get(WEB_TEMP_PATH) . $opath;
                $html[] = '<img src="' . $image_url . '" style="max-width:400px;,max-height:400px;" alt="' . $ds->get_title() . '"/>';
                $html[] = '<div class="clear">&nbsp;</div>';
                $result = implode("\n", $html);
                return $result;
            }
        }
        return parent::get_preview($is_thumbnail);
    }

    function as_html() {
        $result = array();
        $result[] = parent::as_html();
        $result[] = $this->format_datastreams();
        return implode('', $result);
    }

    protected function format_datastreams() {
        $result = array();
        $object = $this->get_object();
        $dss = $object->get_datastreams();
        $system_streams = array();
        $data_streams = array();
        foreach ($dss as $ds) {
            if ($ds->is_system_datastream()) {
                $system_streams[] = $ds;
            } else {
                $data_streams[] = $ds;
            }
        }
        $result[] = '<h4>' . Translation::get('Datastreams') . '</h4>';
        $result[] = '<div class="category_form">';
        foreach ($data_streams as $ds) {
            if ($print = $this->format_datastream($ds)) {
                $result[] = $print;
            }
        }

        if ($this->display_system_datastreams($object)) {
            $split = true;
            foreach ($system_streams as $ds) {
                if ($split) {
                    $split = false;
                }
                if ($print = $this->format_datastream($ds)) {
                    $result[] = $print;
                }
            }
        }

        $result[] = '<div class="clear">&nbsp;</div>';
        $result[] = '</div>';
        return implode('', $result);
    }

    protected function format_datastream(fedora_fs_datastream $ds) {

        $title = $ds->get_title();

        $image_url = Theme::get_image_path(FedoraExternalRepositoryManager::get_namespace(FedoraExternalRepositoryManager::REPOSITORY_TYPE)) . '/types/datastream.png';

        $result = array();
        $result[] = '<div class="create_block" style="width:130px; height:52px; background-image: url(' . $image_url . ');">';
        $result[] = '<div style="height:50%;">' . $title . '</div>';
        $result[] = '<br/>';

        $bar = new Toolbar();

        if (!$ds->is_system_datastream()) {
            $import_url = $this->get_import_datastream_url($ds->get_dsID());
            $item = new ToolbarItem(Translation::get('Import'), Theme::get_common_image_path() . 'action_import.png', $import_url, ToolbarItem::DISPLAY_ICON, false, 'labeled');
            $bar->add_item($item);
        }

        $view_url = $this->get_view_datastream_content_url($ds->get_dsID());
        $item = new ToolbarItem(Translation::get('Download'), Theme::get_common_image_path() . 'action_download.png', $view_url, ToolbarItem::DISPLAY_ICON, false, 'labeled');
        $bar->add_item($item);

        $result[] = $bar->as_html();

        $result[] = '</div>';
        return implode('', $result);
    }

    protected function get_import_datastream_url($dsID = false) {
        $object = $this->get_object();
        $parameters = array();
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = FedoraExternalRepositoryManager::ACTION_IMPORT_EXTERNAL_REPOSITORY;
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY] = $object->get_external_repository_id();
        $parameters[FedoraExternalRepositoryManager::PARAM_RENDERER] = Request::get(FedoraExternalRepositoryManager::PARAM_RENDERER);
        $parameters[Application::PARAM_APPLICATION] = Request::get(Application::PARAM_APPLICATION);
        $parameters[Application::PARAM_ACTION] = Request::get(Application::PARAM_ACTION);
        if ($dsID) {
            $parameters[FedoraExternalRepositoryManager::PARAM_DATASTREAM_ID] = $dsID;
        }
        $result = Redirect::get_url($parameters);
        return $result;
    }

    protected function get_view_datastream_content_url($dsID) {
        $object = $this->get_object();
        $parameters = array();
        $parameters[Application::PARAM_APPLICATION] = Request::get(Application::PARAM_APPLICATION);
        $parameters[Application::PARAM_ACTION] = Request::get(Application::PARAM_ACTION);
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = FedoraExternalRepositoryManager::ACTION_DOWNLOAD_EXTERNAL_REPOSITORY;
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        $parameters[FedoraExternalRepositoryManager::PARAM_EXTERNAL_REPOSITORY] = $object->get_external_repository_id();
        $parameters[FedoraExternalRepositoryManager::PARAM_RENDERER] = Request::get(FedoraExternalRepositoryManager::PARAM_RENDERER);
        $parameters[FedoraExternalRepositoryManager::PARAM_DATASTREAM_ID] = $dsID;
        $result = Redirect::get_url($parameters);
        return $result;
    }

    protected function format_email($text) {
        if ($this->is_email($text)) {
            $result = '<a href="mailto:' . $text . '">' . $text . '</a>';
        } else {
            $result = $text;
        }
        return $result;
    }

    protected function is_email($text) {
        $pattern = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
        return preg_match($pattern, trim($text)) ? true : false;
    }

    protected function get_licence($object) {
        $value = $object->get_license();
        $text = $object->get_license_text();
        $text = $text ? $text : $value;
        if (!empty($value) && substr($value, 0, 4) == 'http') {
            $result = '<a href="' . $value . '" target="_blank">' . $text . '</a>';
        } else if ($text) {
            $result = $text;
        } else {
            $result = '';
        }
        return $result;
    }

    protected function display_system_datastreams(FedoraExternalRepositoryObject $object) {
        return ExternalSetting::get('ViewSystemDatastreams', $object->get_external_repository_id());
    }

}

?>