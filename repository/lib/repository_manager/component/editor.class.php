<?php
/**
 * $Id: editor.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class RepositoryManagerEditorComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if ($id)
        {
            $object = $this->retrieve_content_object($id);
            // TODO: Roles & Rights.
            if ($object->get_owner_id() != $this->get_user_id())
            {
                $this->not_allowed();
            }
            elseif (! $object->is_latest_version())
            {
                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CATEGORY_ID] = $object->get_parent_id();
                
                $this->redirect(Translation :: get('EditNotAllowed'), true, $parameters);
            }
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id)));
            if ($form->validate())
            {
                $success = $form->update_content_object();
                $category_id = $object->get_parent_id();
                
                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CATEGORY_ID] = $category_id;
                
                $this->redirect(Translation :: get($success == ContentObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), ($success == ContentObjectForm :: RESULT_SUCCESS ? false : true), $parameters);
            }
            else
            {
                $this->display_header(null, false, true);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>