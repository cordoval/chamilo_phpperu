<?php

class RepositoryManagerExternalRepositoryListObjectsComponent extends RepositoryManagerExternalRepositoryComponent
{
    
    public function run()
    {
        $co_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        
        $content_object = $this->get_content_object_from_params();
        $co_id          = isset($content_object) ? $content_object->get_id() : null;
        $export         = $this->get_external_repository_from_param();
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $co_id)), Translation :: get('ExternalRepository')));
        
        try
        {
            $trail->add(new Breadcrumb(null, Translation :: get('ExternalRepositoryBrowseObjects') . ' : ' . $export->get_title()));
        
            $objects_list = $this->get_external_repository_objects_list();
            $objects_list = $this->add_chamilo_infos($export, $objects_list);
            
            $this->display_header($trail, false, true);
            $form = new ExternalRepositoryObjectBrowserForm($objects_list, $export);
            $form->display();
        }
        catch(Exception $ex)
        {
            $this->display_header($trail, false, true);
            $this->display_error_message($ex->getMessage());
        }
        
        $this->display_footer();
    }
    
    
    public function get_external_repository_objects_list()
    {
        $export = $this->get_external_repository_from_param();
        if (isset($export) && $export->get_enabled() == 1)
        {
            $exporter = BaseExternalRepositoryConnector :: get_instance($export);
            
            $existing_objects = $exporter->get_objects_list_from_repository();
            
            return $existing_objects;
        }
        else
        {
            return null;
        }
    }
    
    
}
?>