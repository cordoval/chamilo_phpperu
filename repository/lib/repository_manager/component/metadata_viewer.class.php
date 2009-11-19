<?php
/**
 * $Id: metadata_viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
require_once dirname(__FILE__) . '/metadata_component.class.php';

class RepositoryManagerMetadataViewerComponent extends RepositoryManagerMetadataComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository metadata');
        
        if ($this->check_content_object_from_params())
        {
            $content_object = $this->get_content_object_from_params();
            
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id)), $content_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_METADATA, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id)), Translation :: get('Metadata')));
            
            $metadata_type = $this->get_metadata_type();
            
            $mapper = null;
            $form = null;
            switch ($metadata_type)
            {
                case self :: METADATA_FORMAT_LOM :
                    $mapper = new IeeeLomMapper($content_object);
                    $form = new MetadataLomExportForm($content_object, $mapper);
                    break;
                
            /*
                 * Implementation of another Metadata type than LOM 
                 * could be done here
                 */
            }
            
            if (isset($form))
            {
                $form->display_metadata();
            }
        }
        else
        {
            throw new Exception(Translation :: get('InvalidURLException'));
        }
    }

}
?>