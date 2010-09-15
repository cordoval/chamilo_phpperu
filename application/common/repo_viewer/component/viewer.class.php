<?php
/**
 * $Id: viewer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component
 */

/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to preview a learning object in the learning object repo_viewer.
 */
class RepoViewerViewerComponent extends RepoViewer
{
    /*
	 * Inherited
	 */
    function run()
    {
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

	        $this->display_header();
            echo ContentObjectDisplay :: factory($content_object)->get_full_html();
            echo $toolbar->as_html();
            echo '<div class="clear"></div>';
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
   		$breadcrumbtrail->add_help('repo_viewer_viewer');
   		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER)), Translation :: get('RepoViewerBrowserComponent')));
    }
   
    function get_additional_parameters()
    {
    	return array(RepoViewer :: PARAM_ID);
    }
}
?>