<?php
/**
 * $Id: external_repository_export.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerExternalRepositoryExportComponent extends RepositoryManagerExternalRepositoryComponent
{
    const PARAM_FORCE_EXPORT = 'force_export';

    function run()
    {
        if ($this->check_content_object_from_params())
        {
            $external_repository = $this->get_external_repository_from_param();
            if (isset($external_repository) && $external_repository->get_enabled() == 1)
            {
                /*
                 * Check if a custom component exists for the current export
                 */
                $repository_type  = strtolower($external_repository->get_type());
        	    $catalog_name     = strtolower($external_repository->get_catalog_name());
        	    
        	    $custom_component_class_name = null;
        	    
        	    $custom_component_file = Path :: get_repository_path() . 'lib/repository_manager/component/' . strtolower($catalog_name) . '_' . strtolower($repository_type) . '_external_repository_export.class.php'; 
        	    
        	    if(file_exists($custom_component_file))
        	    {
        	        //debug($custom_component_file);
        	        
        	        require_once $custom_component_file;
        	        $custom_component_class_name = Utilities :: underscores_to_camelcase($catalog_name . '_' . $repository_type) . 'ExternalRepositoryExport';
        	    }
                
                if(isset($custom_component_class_name))
                {
                    //debug($custom_component_class_name);
                    
                    $custom_component = RepositoryManagerComponent :: factory($custom_component_class_name, $this);
                    $custom_component->export($external_repository);
                }
                else
                {
                    $this->export($external_repository);
                }
            }
            else
            {
                throw new Exception('The external export is undefined');
            }
        }
        else
        {
            throw new Exception('The object to export is undefined');
        }
    }
    
    /**
     * This function contains the default export logic.
     * It is used if no custom component inheriting from 'RepositoryManagerExternalRepositoryExportComponent' exists for the export 
     *
     * @param $external_repository
     * @return void
     */
    function export($external_repository)
    {
        try
        {
            $content_object = $this->get_content_object_from_params();
            
            $trail = new BreadcrumbTrail(false);
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), Translation :: get('ExternalExport')));
            $trail->add(new Breadcrumb(null, $external_repository->get_title()));
            
            //do not put display_header(...) here, as it would block an eventual redirection made by the ->export() method
            //$this->display_header($trail, false, true);
            

            $form = ExternalRepositoryExportForm :: get_instance($content_object, $external_repository, $this->get_url(array(RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $external_repository->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), null);
            
            $force_export = Request :: get(self :: PARAM_FORCE_EXPORT);
            if (! $form->isSubmitted() && ! isset($force_export))
            {
                $this->display_header($trail, false, true);
                $form->display();
            }
            else
            {
                if ($form->validate())
                {
                    $connector = BaseExternalRepositoryConnector :: get_instance($external_repository);
                    
                    if ($connector->export($content_object))
                    {
                        $this->display_header($trail, false, true);
                        
                        $repository_uid = $connector->get_existing_repository_uid($content_object);
                        
                        $form->display_export_success($repository_uid);
                    }
                    else
                    {
                        throw new Exception('An error occured during the export');
                    }
                }
                else
                {
                    $this->display_header($trail, false, true);
                    $form->display();
                }
            }
        }
        catch (Exception $ex)
        {
            $this->display_header($trail, false, true);
            $this->display_error_message($ex->getMessage());
        }
        
        $this->display_footer();
    }
}
?>