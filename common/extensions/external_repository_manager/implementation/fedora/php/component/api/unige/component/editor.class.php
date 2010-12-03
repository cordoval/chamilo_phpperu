<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

require_once dirname(__FILE__) . '/../forms/fedora_unige_edit_form.class.php';

/**
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUnigeEditorComponent extends FedoraExternalRepositoryManagerEditorComponent {

    function create_form($object_external_id) {
        $result = new FedoraUnigeEditForm($this, $_GET, array('edit' => $object_external_id));
        return $result;
    }

}

?>