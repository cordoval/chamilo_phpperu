<?php
/**
 * $Id: metadata_editor.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
require_once dirname(__FILE__) . '/metadata_component.class.php';

/**
 * Repository manager component to edit the metadata of a learning object.
 */
class RepositoryManagerMetadataEditorComponent extends RepositoryManagerMetadataComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository metadata');
        
        if ($this->check_content_object_from_params())
        {
            $content_object = $this->get_content_object_from_params();
            
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_METADATA, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), Translation :: get('Metadata')));
            
            $metadata_type = $this->get_metadata_type();
            
            $form = null;
            $mapper = null;
            switch ($metadata_type)
            {
                case self :: METADATA_FORMAT_LOM :
                    
                    $mapper = new IeeeLomMapper($content_object);
                    $form = new MetadataLomEditForm($content_object->get_id(), $mapper, $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $this->get_catalogs());
                    break;
                
            /*
                 * Implementation of another Metadata type than LOM 
                 * could be done here
                 */
            }
            
            $this->display_header($trail, false, true);
            
            if (isset($form))
            {
                $form->build_editing_form();
                
                if ($form->must_save())
                {
                    if (isset($mapper))
                    {
                        $this->render_action_bar($content_object->get_id());
                        
                        if (! $mapper->save_submitted_values($form->getSubmitValues()))
                        {
                            $this->display_error_message($mapper->get_errors_as_html());
                        }
                        else
                        {
                            $this->display_message(Translation :: get('MetadataSaved'));
                        }
                        
                        $form->set_constant_values($mapper->get_constant_values(), true);
                        $form->display();
                    }
                    else
                    {
                        $this->display_error_message(Translation :: get('MetadataMapperNotFound'));
                    }
                }
                else
                {
                    $this->render_action_bar($content_object->get_id());
                    $form->display();
                }
            
            }
        }
        else
        {
            throw new Exception(Translation :: get('InvalidURLException'));
        }
        
        $this->display_footer();
    }

    /**
     * Displays an action bar
     */
    function render_action_bar($id)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem('XML', Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array('go' => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECT_METADATA, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id))));
        
        $external_repositories = ExternalExport :: retrieve_external_export();
        if (count($external_repositories) > 0)
        {
            $action_bar->add_common_action(new ToolbarItem('ExternalRepository', Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array('go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id))));
        }
        
        echo $action_bar->as_html();
    }

}
?>