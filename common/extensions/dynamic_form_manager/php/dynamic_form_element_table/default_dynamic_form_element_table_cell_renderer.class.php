<?php
namespace common\extensions\dynamic_form_manager;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: default_user_table_cell_renderer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */


class DefaultDynamicFormElementTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

    function render_cell($column, $dynamic_form_element)
    {
        switch ($column->get_name())
        {
            case DynamicFormElement :: PROPERTY_TYPE :
                return $dynamic_form_element->get_type_name($dynamic_form_element->get_type());
            case DynamicFormElement :: PROPERTY_NAME :
                return $dynamic_form_element->get_name();
            case DynamicFormElement :: PROPERTY_REQUIRED :
                if($dynamic_form_element->get_required())
                	return Translation :: get('ConfirmTrue', null, Utilities :: COMMON_LIBRARIES);
                else
                	return Translation :: get('ConfirmFalse', null, Utilities :: COMMON_LIBRARIES);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>