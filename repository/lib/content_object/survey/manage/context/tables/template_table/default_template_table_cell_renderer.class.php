<?php

class DefaultSurveyTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyTemplateTableCellRenderer()
    {
    }

    
    function render_cell($column, $context_template)
    {
        
    	$property_name = str_replace(' ','_' ,$column->get_name());
    	
    	if($property_name == SurveyTemplate::PROPERTY_USER_ID){
    		return $context_template->get_default_property($property_name);
    	}else{
    		return $context_template->get_additional_property($property_name);
    	}
    	
    	
    	
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>