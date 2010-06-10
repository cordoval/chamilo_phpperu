<?php
/**
 * $Id: viewer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/../repo_viewer_component.class.php';

/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to preview a learning object in the learning object repo_viewer.
 */
class RepoViewerViewerComponent extends RepoViewerComponent
{
    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();

        if (Request :: get(RepoViewer :: PARAM_ID))
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get(RepoViewer :: PARAM_ID));
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Publish'),
	        		Theme :: get_common_image_path() . 'action_publish.png',
	        		$this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $content_object->get_id())), false)
	        ));
	        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('EditAndPublish'),
	        		Theme :: get_common_image_path() . 'action_editpublish.png',
	        		$this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_CREATOR, RepoViewer :: PARAM_EDIT_ID => $content_object->get_id())))
	        ));
            
            $html[] = ContentObjectDisplay :: factory($content_object)->get_full_html();
            $html[] = $toolbar->as_html();
            $html[] = '<div class="clear"></div>';
        }

        return implode("\n", $html);
    }
}
?>