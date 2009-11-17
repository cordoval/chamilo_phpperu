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
        if (Request :: get(RepoViewer :: PARAM_ID))
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get(RepoViewer :: PARAM_ID));
            $toolbar_data = array();
            $toolbar_data[] = array('href' => $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => 'publicationcreator', RepoViewer :: PARAM_ID => $content_object->get_id()))), 'img' => Theme :: get_common_image_path() . 'action_publish.png', 'label' => Translation :: get('Publish'), 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            $toolbar_data[] = array('href' => $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => 'publicationcreator', RepoViewer :: PARAM_EDIT_ID => $content_object->get_id()))), //, RepoViewer :: PARAM_EDIT => 1))),
'img' => Theme :: get_common_image_path() . 'action_editpublish.png', 'label' => Translation :: get('EditAndPublish'), 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            $toolbar = Utilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
            return ContentObjectDisplay :: factory($content_object)->get_full_html() . $toolbar;
        }
    }
}
?>