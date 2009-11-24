<?php

require_once dirname(__FILE__) . '/external_repository_export_component.class.php';
require_once dirname(__FILE__) . '/../../export/external_export/base_external_exporter.class.php';

class RepositoryManagerExternalRepositoryExportListObjectsComponent extends RepositoryManagerExternalRepositoryExportComponent
{
    
    public function run()
    {
        $co_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        
        $content_object = $this->get_content_object_from_params();
        $co_id          = isset($content_object) ? $content_object->get_id() : null;
        $export         = $this->get_external_export_from_param();
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $co_id)), Translation :: translate('ExternalRepository')));
        $trail->add(new Breadcrumb(null, Translation :: translate('ExternalRepositoryBrowseObjects') . ' : ' . $export->get_title()));

        try
        {
            $objects_list = $this->get_external_repository_objects_list();
            $objects_list = $this->add_chamilo_infos($export, $objects_list);
            
            $this->display_header($trail, false, true);
            $form = new ExternalRepositoryObjectBrowserForm($objects_list);
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
        $export = $this->get_external_export_from_param();
        if (isset($export) && $export->get_enabled() == 1)
        {
            $exporter = BaseExternalExporter :: get_instance($export);
            
            $existing_objects = $exporter->get_objects_list_from_repository();
            
            return $existing_objects;
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Add the eventual metadata on each object if it exists already in Chamilo. 
     * Note: an object already exists if an object with the same identifier exists in Chamilo and in the repository
     * 
     * @param ExternalExport $export
     * @param array $objects_list
     * @return array
     */
    public function add_chamilo_infos($export, $objects_list)
    {
        $catalog_name = $export->get_catalog_name();
        
        foreach($objects_list as $key => $object)
        {
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]))
            {
                $content_object = ContentObjectMetadata :: get_by_catalog_entry_values($catalog_name, $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]);
                
                if(isset($content_object))
                {
                    //DebugUtilities::show($content_object);
                    
                    $object['content_object'] = $content_object;
                    
                    $objects_list[$key] = $object;
                }
            }
        }
        
        return $objects_list;
    }
    
}
?>