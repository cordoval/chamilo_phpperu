<?php

require_once dirname(__FILE__) . '/../../publication.class.php';

/**
 * Default cell renderer for the publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultInternshipOrganizerPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param InternshipOrganizerPublication $publication - The publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $publication)
    {
        $content_object = $publication->get_content_object();
        
        switch ($column->get_name())
        {
            
        	case InternshipOrganizerPublication :: PROPERTY_NAME :
                
                return $publication->get_name();
        	case ContentObject :: PROPERTY_TITLE :
                
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($content_object->get_description(), 200);
            case InternshipOrganizerPublication :: PROPERTY_FROM_DATE :
                return $this->get_date($publication->get_from_date());
            case InternshipOrganizerPublication :: PROPERTY_TO_DATE :
                return $this->get_date($publication->get_to_date());
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