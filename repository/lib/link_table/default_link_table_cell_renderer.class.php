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
	protected $type;
	private $browser;
	
    /**
     * Constructor
     */
    function DefaultLinkTableCellRenderer($browser, $type)
    {
    	$this->type = $type;
    	$this->browser = $browser;
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param ContentObject $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $object)
    {
    	if($this->type == LinkBrowserTable :: TYPE_PARENTS)
       	{
       		$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_parent()); 	
       	}
            
        if($this->type == LinkBrowserTable :: TYPE_CHILDREN)
        {
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_ref());
            if($object->get_type() == 'portfolio_item' || $object->get_type() == 'learning_path_item')
            {
            	$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_reference());
            } 
        }
            	
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
            case ContentObject :: PROPERTY_DESCRIPTION :
            	return Utilities :: truncate_string($object->get_description(), 50);
            case ContentObject :: PROPERTY_TITLE :
            	$url = $this->browser->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS,
            										 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id()));	
            	return '<a href="' . $url . '">' . Utilities :: truncate_string($object->get_title(), 50) . '</a>';
            case ContentObject :: PROPERTY_TYPE :
            	return $object->get_icon();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
     	if($this->type == LinkBrowserTable :: TYPE_PUBLICATIONS)
        {
        	$link_id = $object->get_application() . '|' . $object->get_id();
        }
        else
        {
        	$link_id = $object->get_id();
        }
        
        return $link_id;
    }
}
?>