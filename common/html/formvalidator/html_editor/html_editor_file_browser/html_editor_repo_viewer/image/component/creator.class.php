<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/creator.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to create a new learning object before publishing it.
 */
class HtmlEditorImageRepoViewerCreatorComponent extends RepoViewerCreatorComponent
{    
    /**
     * Gets the form to create the learning object.
     * @return string A HTML-representation of the form.
     */
    protected function get_creation_form($type)
    {
        $default_lo = $this->get_default_content_object($type);
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $default_lo, 'create', 'post', $this->get_url(array_merge(array('type' => $type), $this->get_parameters())), null, array(), true, 'image');

        $def = $this->get_creation_defaults();
        if ($def)
        {
            $form->setParentDefaults($def);
        }

        return $this->handle_form($form, 0);
    }

    /**
     * Gets the editing form
     */
    protected function get_editing_form($content_object_id, $params = array())
    {
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array_merge($this->get_parameters(), array_merge($params, array(RepoViewer :: PARAM_EDIT_ID => $content_object_id)))), null, array(), true, 'image');
        return $this->handle_form($form, 1);
    }
}
?>