<?php
/**
 * $Id: rights_editor_manager_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager
 */
class RightsEditorManagerComponent extends SubManagerComponent
{

    function get_location()
    {
        return $this->get_parent()->get_location();
    }

    function get_available_rights()
    {
        return $this->get_parent()->get_available_rights();
    }
}
?>