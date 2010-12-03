<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Translation;

require_once dirname(__FILE__) . '/../../../../forms/fedora_confirm_form.class.php';

/**
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUnigeConfirmForm extends FedoraConfirmForm {

    function get_licences($key=false) {
        $connector = $this->get_connector();
        $licences = $connector->retrieve_licenses();
        $lang = FedoraExternalRepositoryManagerConnector::get_full_language();
        $result = array();
        foreach ($licences as $lkey => $licence) {
            $text = isset($licence[$lang]) ? $licence[$lang] : $licence['english'];
            $text = '<a href="' . $lkey . '">' . $text . '</a>';
            $result[$lkey] = $text;
        }
        if ($key !== false) {
            return isset($result[$key]) ? $result[$key] : '';
        } else {
            return $result;
        }
    }

    function get_access_rights($key=false) {
        $connector = $this->get_connector();
        $result = $connector->retrieve_rights($key);
        return $result;
    }

    function get_edit_rights($key=false) {
        $connector = $this->get_connector();
        $result = $connector->retrieve_rights($key);
        return $result;
    }

    function get_collections($id=false) {
        $connector = $this->get_connector();
        $result = $connector->retrieve_collections($id);
        return $result;
    }

}

?>