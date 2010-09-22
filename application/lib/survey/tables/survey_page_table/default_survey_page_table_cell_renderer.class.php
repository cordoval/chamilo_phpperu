<?php

class DefaultSurveyPageTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyPageTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param SurveyPage $survey_page - The survey_page
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $survey_page)
    {
  	    	
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                 return $survey_page->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($survey_page->get_description(), 200);
                 return $description;
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