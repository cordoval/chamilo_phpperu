<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Translation;

require_once dirname(__FILE__) . '/../../../../forms/fedora_edit_form.class.php';

/**
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUnigeEditForm extends FedoraEditForm {

    function __construct($application, $parameters, $data=false) {
        parent::__construct($application, $parameters, $data);
    }

    public function set_external_repository_object(FedoraExternalRepositoryObject $external_repository_object) {
        parent::set_external_repository_object($external_repository_object);

        $defaults[FedoraExternalRepositoryObject::PROPERTY_LICENSE] = $external_repository_object->get_license();
        $defaults[FedoraExternalRepositoryObject::PROPERTY_ACCESS_RIGHTS] = $external_repository_object->get_access_rights();
        $defaults[FedoraExternalRepositoryObject::PROPERTY_EDIT_RIGHTS] = $external_repository_object->get_edit_rights();
        $defaults[FedoraExternalRepositoryObject::PROPERTY_SUBJECT] = $external_repository_object->get_subject();
        $defaults['subject_dd[subject_text]'] = $external_repository_object->get_subject_text();
        parent::setDefaults($defaults);
    }

    function has_disciplines() {
        return true;
    }

    private $_disciplines = false;

    function get_disciplines($editor_key, $editor_text, $dropdown) {
        if ($this->_disciplines) {
            return $this->_disciplines;
        }

        $connector = $this->get_connector();
        $disciplines = $connector->retrieve_disciplines();
        $lang = FedoraExternalRepositoryManagerConnector::get_full_language();

        foreach ($disciplines as $key => &$discipline) {
            $discipline['pid'] = $key;
            $discipline['title'] = $discipline[$lang];
            $discipline['class'] = 'category';
            $discipline['url'] = '#';
            $onclick = '';
            $onclick .= "document.getElementsByName('$editor_key').item(0).value = '$key';";
            $onclick .= "document.getElementsByName('$editor_text').item(0).value = '{$discipline[$lang]}';";
            $onclick .= "toggle_dropdown('$dropdown');";
            $onclick .= 'return false;';
            $discipline['onclick'] = $onclick;
        }

        foreach ($disciplines as $discipline) {
            $parent = $discipline['parent'];
            $pid = $discipline['pid'];
            $title = $discipline['title'];
            $disciplines[$parent]['sub'][$pid] = $discipline;
        }

        foreach ($disciplines as &$discipline) {
            if (isset($discipline['sub'])) {
                $children = $discipline['sub'];
                asort($children);
                $discipline['sub'] = $children;
            } else {
                $discipline['sub'] = null;
            }
        }
        $this->_disciplines = array();
        foreach ($disciplines as $discipline) {
            if (empty($discipline['parent'])) {
                //$discipline['url'] = 'ww';
                $this->_disciplines[] = $discipline;
            }
        }

        return $this->_disciplines;
    }

    function get_licenses() {
        $connector = $this->get_connector();
        $licenses = $connector->retrieve_licenses();
        $lang = FedoraExternalRepositoryManagerConnector::get_full_language();
        $result = array();
        foreach ($licenses as $key => $license) {
            if (isset($license[$lang])) {
                $result[$key] = $license[$lang];
            } else {
                $result[$key] = $license['english'];
            }
        }
        return $result;
    }

    function get_rights() {
        $connector = $this->get_connector();
        $result = $connector->retrieve_rights();
        return $result;
    }

    function get_access_rights() {
        return $this->get_rights();
    }

    function get_edit_rights() {
        return $this->get_rights();
    }

    function get_collections() {
        $connector = $this->get_connector();
        $result = $connector->retrieve_collections();
        return $result;
    }

}

?>