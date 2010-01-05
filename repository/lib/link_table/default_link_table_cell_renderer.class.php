<?php
/**
 * $Id: default_link_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.link_table
 */

/**
 * TODO: Add comment
 */
class DefaultLinkTableCellRenderer implements ObjectTableCellRenderer
{
	private $type;
	
    /**
     * Constructor
     */
    function DefaultLinkTableCellRenderer($type)
    {
    	$this->type = $type;
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ContentObjectPublicationAttributes :: PROPERTY_APPLICATION :
                return Utilities :: underscores_to_camelcase_with_spaces($object->get_application());
            case ContentObjectPublicationAttributes :: PROPERTY_LOCATION :
                $application = $object->get_application();
                
                if ($application == 'weblcms')
                {
                    $location = $object->get_location();
                    $codes = explode("&gt;", $location);
                    $course_id = trim($codes[0]);
                    $tool = trim($codes[1]);
                    
                    $wdm = WeblcmsDataManager :: get_instance();
                    $course = $wdm->retrieve_course($course_id);
                    return $course->get_name() . ' > ' . $tool;
                }
                
                return $object->get_location();
            case ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE :
                return date('Y-m-d, H:i', $object->get_publication_date());
            case ContentObject:: PROPERTY_DESCRIPTION :
            	if($this->type == LinkBrowserTable :: TYPE_PARENTS)
            	{
            		$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_parent()); 	
            	}
            	
            	if($this->type == LinkBrowserTable :: TYPE_CHILDREN)
            	{
            		$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_ref()); 
            	}
            	
            	return Utilities :: truncate_string($object->get_description(), 50);
            case ContentObject:: PROPERTY_TITLE :
        		if($this->type == LinkBrowserTable :: TYPE_PARENTS)
            	{
            		$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_parent()); 	
            	}
            	
            	if($this->type == LinkBrowserTable :: TYPE_CHILDREN)
            	{
            		$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_ref()); 
            	}
            	
            	return Utilities :: truncate_string($object->get_title(), 50);
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