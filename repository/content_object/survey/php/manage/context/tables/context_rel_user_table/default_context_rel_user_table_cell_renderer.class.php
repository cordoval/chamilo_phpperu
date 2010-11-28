<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyContextRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    function __construct()
    {
    }

    function render_cell($column, $context_rel_user)
    {
                
        switch ($column->get_name())
        {
            
            case User :: PROPERTY_LASTNAME :
                return $context_rel_user->get_optional_property(User :: PROPERTY_LASTNAME);
            case User :: PROPERTY_FIRSTNAME :
                return $context_rel_user->get_optional_property(User :: PROPERTY_FIRSTNAME);
            case User :: PROPERTY_USERNAME :
                return $context_rel_user->get_optional_property(User :: PROPERTY_USERNAME);
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $context_rel_user->get_optional_property(User :: PROPERTY_EMAIL) . '">' . $context_rel_user->get_optional_property(User :: PROPERTY_EMAIL) . '</a><br/>';
        }
    
    }

    function render_id_cell($context_rel_user)
    {
		return $context_rel_user->get_context_id() . '|' . $context_rel_user->get_user_id();
    }

}
?>