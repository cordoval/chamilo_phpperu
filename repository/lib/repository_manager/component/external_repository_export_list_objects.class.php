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
                    
                    $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY] = $content_object;
                    
                    /*
                     * Get the last synchronization date with the repository if exists
                     */
                    $eesi = ExternalExportSyncInfo :: get_by_content_object_and_repository($content_object->get_id(), $export->get_id());
    	            
    	            if(isset($eesi))
    	            {
    	                $object[BaseExternalExporter :: SYNC_INFO] = $eesi;
    	                
    	                /*
    	                 * Compare dates between the repository and Chamilo
    	                 */
    	                $repository_datetime          = strtotime($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_MODIFICATION_DATE]);
    	                $chamilo_synchronized         = strtotime(date('Y-m-d H:i:s', strtotime($object[BaseExternalExporter :: SYNC_INFO]->get_utc_synchronized() . 'z')));
    	                $synchronized_object_datetime = strtotime($object[BaseExternalExporter :: SYNC_INFO]->get_synchronized_object_datetime());
    	                
    	                //DebugUtilities::show($repository_utc_datetime . '==' . $chamilo_utc_synchronized);
    	                
    	                /*
    	                 * Last modification date of the object in Chamilo 
    	                 */
    	                $current_object_date = $content_object->get_modification_date();
    	                if(!isset($current_object_date))
    	                {
    	                    $current_object_date = $content_object->get_creation_date();
    	                }
    	                
    	                
    	                if($current_object_date > $synchronized_object_datetime)
    	                {
    	                    /*
    	                     * Object has been modified after last export
    	                     */
    	                    $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_NEWER_IN_CHAMILO;
    	                }
    	                elseif($current_object_date == $synchronized_object_datetime)
    	                {
    	                    /*
    	                     * Object has not been modified after export
    	                     */
    	                    $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_IDENTICAL;
    	                    
    	                    /*
    	                     * Check if the object has been modified in the repository side
    	                     */
    	                    if($chamilo_synchronized < $repository_datetime)
    	                    {
    	                        $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_OLDER_IN_CHAMILO;
    	                    }
    	                }
    	                else
    	                {
    	                    /*
    	                     * ($current_object_date < $synchronized_object_datetime)
    	                     * 
    	                     * This should never happen --> it is a bug --> throw an Exception to detect it
    	                     */
    	                    throw new Exception('RepositoryManagerExternalRepositoryExportListObjectsComponent error: The current object date is smaller than its date of synchronization');
    	                }
    	            }
    	            else
    	            {
    	                $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_NEVER_SYNCHRONIZED;
    	            }
                }
                else
                {
                    $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_NEVER_SYNCHRONIZED;
                }
            }
            
            $objects_list[$key] = $object;
        }
        
        return $objects_list;
    }
    
}
?>