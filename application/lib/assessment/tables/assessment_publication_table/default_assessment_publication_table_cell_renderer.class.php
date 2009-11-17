<?php
/**
 * $Id: default_assessment_publication_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.assessment_publication_table
 */

require_once dirname(__FILE__) . '/../../assessment_publication.class.php';

/**
 * Default cell renderer for the assessment_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultAssessmentPublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultAssessmentPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param AssessmentPublication $assessment_publication - The assessment_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $assessment_publication)
    {
        $content_object = $assessment_publication->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                
                if ($assessment_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $content_object->get_title() . '</span>';
                }
                
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
                
                if ($assessment_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $description . '</span>';
                }
                
                return $description;
            case ContentObject :: PROPERTY_TYPE :
                $type = Translation :: get($content_object->get_type());
                if ($type == 'assessment')
                {
                    $type = $content_object->get_assessment_type();
                }
                
                if ($assessment_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $type . '</span>';
                }
                
                return $type;
            case AssessmentPublication :: PROPERTY_FROM_DATE :
                return $assessment_publication->get_from_date();
            case AssessmentPublication :: PROPERTY_TO_DATE :
                return $assessment_publication->get_to_date();
            case AssessmentPublication :: PROPERTY_PUBLISHER :
                return $assessment_publication->get_publisher();
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