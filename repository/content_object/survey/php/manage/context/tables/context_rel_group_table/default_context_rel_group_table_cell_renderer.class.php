<?php namespace repository\content_object\survey;

class DefaultSurveyContextRelGroupTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultSurveyContextRelGroupTableCellRenderer()
    {
    }

    function render_cell($column, $context_rel_group)
    {
        
        switch ($column->get_name())
        {
            case SurveyContextRelGroup :: PROPERTY_CREATED :
                return $context_rel_group->get_created();
            case Group :: PROPERTY_NAME :
                return $context_rel_group->get_optional_property(Group :: PROPERTY_NAME);
            case Group :: PROPERTY_DESCRIPTION :
                return $context_rel_group->get_optional_property(Group :: PROPERTY_DESCRIPTION);
        
        }
    
    }

    function render_id_cell($context_rel_group)
    {
        return $context_rel_group->get_context_id() . '|' . $context_rel_group->get_group_id();
    }

}
?>