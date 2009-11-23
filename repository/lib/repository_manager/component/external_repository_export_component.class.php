<?php
/**
 * $Id: external_repository_export_component.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
require_once dirname(__FILE__) . '/metadata_component.class.php';

class RepositoryManagerExternalRepositoryExportComponent extends RepositoryManagerMetadataComponent
{
    const PARAM_EXPORT_ID = 'ext_rep_id';
    
    private $already_required_types = array();

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
        $export_id = Request :: get(self :: PARAM_EXPORT_ID);
        
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
}

?>