<?php
/**
 * $Id: default_publication_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.publication_table
 */

/**
 * TODO: Add comment
 */
class DefaultPublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $content_object_publication)
    {
        switch ($column->get_name())
        {
            case ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_OBJECT :
                return $content_object_publication->get_publication_object_id();
            case ContentObjectPublicationAttributes :: PROPERTY_APPLICATION :
                return Utilities :: underscores_to_camelcase_with_spaces($content_object_publication->get_application());
            case ContentObjectPublicationAttributes :: PROPERTY_LOCATION :
                $application = $content_object_publication->get_application();
                
                if ($application == 'weblcms')
                {
                    $location = $content_object_publication->get_location();
                    $codes = explode("&gt;", $location);
                    $course_id = trim($codes[0]);
                    $tool = trim($codes[1]);
                    
                    $wdm = WeblcmsDataManager :: get_instance();
                    $course = $wdm->retrieve_course($course_id);
                    return $course->get_name() . ' > ' . $tool;
                }
                
                return $content_object_publication->get_location();
            case ContentObject :: PROPERTY_TITLE :

				/*$application = $content_object_publication->get_application();
				$url = 'run.php?application=' . Utilities :: camelcase_to_underscores($application);

				if($application == 'weblcms')
				{
					$location = $content_object_publication->get_location();
					$codes = explode("&gt;",$location);
					$course = trim($codes[0]);
					$tool = trim($codes[1]);

					if(stripos($tool, '_feedback'))
						$tool = substr($tool, 0, stripos($tool, '_feedback'));

					$url .= '&go=courseviewer&course=' . $course . '&tool=' . $tool . '&tool_action=view';
				}
				else
				{
					//$url .= '&go=view';
				}*/
				$url = $content_object_publication->get_url();
                $url = '<a href="' . $url . '">';
                
                $co = $content_object_publication->get_publication_object();
                return $url . $co->get_title() . '</a>';
            case ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE :
                return date('Y-m-d, H:i', $content_object_publication->get_publication_date());
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