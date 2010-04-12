<?php

require_once dirname(__FILE__) . '/../../survey_page.class.php';

class DefaultSurveyPageTableCellRenderer implements ObjectTableCellRenderer
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
        $content_object = $survey_page->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                 return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
                 return $description;
            default :
                return '&nbsp;';
        }
    }

    private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>