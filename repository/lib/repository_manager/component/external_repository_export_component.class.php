<?php
/**
 * $Id: external_repository_export_component.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
require_once dirname(__FILE__) . '/metadata_component.class.php';

class RepositoryManagerExternalRepositoryExportComponent extends RepositoryManagerMetadataComponent
{
    //const PARAM_EXPORT_ID = 'ext_rep_id';
    
    private $already_required_types = array();
    private $header_is_displayed = false;

    function get_catalogs()
    {
        $catalogs = array();
        
        $catalogs[ExternalExport :: CATALOG_EXPORT_LIST] = ExternalExport :: retrieve_external_export();
        
        return $catalogs;
    }

    /**
     * Check wether a learning object can be retrieved by using the URL params
     * @return boolean
     */
    function check_content_object_from_params()
    {
        $content_object = $this->get_content_object_from_params();
        if (isset($content_object))
        {
            $this->check_user_can_access_content_object($content_object, true);
            
            return true;
        }
        else
        {
            return false;
        }
    }

    function get_content_object_from_params()
    {
        /*
	     * Check if the learning object is given in the URL params  
	     */
        $lo_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        
        if (isset($lo_id) && is_numeric($lo_id))
        {
            /*
	         * Check if the learning object does exist 
	         */
            $dm = RepositoryDataManager :: get_instance();
            return $dm->retrieve_content_object($lo_id);
        }
        else
        {
            return null;
        }
    }

    /**
     * @return ExternalExport
     */
    function get_external_export_from_param()
    {
        $export_id = Request :: get(RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        
        if (isset($export_id) && strlen($export_id) > 0)
        {
            $export = new ExternalExport();
            $export->set_id($export_id);
            $export = $export->get_typed_export_object();
            
            return $export;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if a user has the right to export the learning object to an external repository
     * 
     * @param $content_object ContentObject
     * @param $with_error_display boolean Indicates wether the 'not allowed' form must be displayed when a user doesn't have the required access rights
     * @return boolean
     */
    protected function check_user_can_access_content_object($content_object, $with_error_display = false)
    {
        if ($content_object->get_owner_id() != $this->get_user_id() && ! $this->get_parent()->has_right($content_object, $this->get_user_id(), RepositoryRights :: REUSE_RIGHT))
        {
            if ($with_error_display)
            {
                $trail = new BreadcrumbTrail(false);
                $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
                
                $this->not_allowed($trail);
            }
            
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see repository/lib/repository_manager/RepositoryManagerComponent#display_header($breadcrumbtrail, $display_search, $display_menu, $helpitem)
     */
    public function display_header($breadcrumbtrail, $display_search = false, $display_menu = true, $helpitem = null)
    {
        if ($this->header_is_displayed === false)
        {
            parent :: display_header($breadcrumbtrail, $display_search, $display_menu, $helpitem);
            $this->header_is_displayed = true;
        }
    }
    
    /**
     * Add the eventual metadata on each object retrieved from a repository if they already exist in Chamilo. 
     * Note: an object is considered as existing if an object with the same identifier exists in Chamilo and in the repository
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
                //$content_object = ContentObjectMetadata :: get_by_catalog_entry_values($catalog_name, $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]);
                
                /*
                 * Try to get the content object reference by looking in the synchronization table
                 * if the external_uid has already been synchronized with the repository
                 */
                $eesi = ExternalExportSyncInfo :: get_by_external_uid_and_repository($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID], $export->get_id());
                
                if(isset($eesi))
                {
                    //DebugUtilities::show($content_object);
                    
                    $content_object = ContentObject :: get_by_id($eesi->get_content_object_id());
                    
                    $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY] = $content_object;
                    
//                    /*
//                     * Get the last synchronization date with the repository if exists
//                     */
//                    $eesi = ExternalExportSyncInfo :: get_by_content_object_and_repository($content_object->get_id(), $export->get_id());
    	            
//    	            if(isset($eesi))
//    	            {
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
//    	            }
//    	            else
//    	            {
//    	                $object[BaseExternalExporter :: SYNC_STATE] = BaseExternalExporter :: SYNC_NEVER_SYNCHRONIZED;
//    	            }
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