<?php
/**
 * $Id: deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $failures = 0;
            $delete_version = Request :: get(RepositoryManager :: PARAM_DELETE_VERSION);
            $permanent = Request :: get(RepositoryManager :: PARAM_DELETE_PERMANENTLY);
            $recycled = Request :: get(RepositoryManager :: PARAM_DELETE_RECYCLED);
            foreach ($ids as $object_id)
            {
                $object = $this->retrieve_content_object($object_id);
                // TODO: Roles & Rights.
                if ($object->get_owner_id() == $this->get_user_id())
                {
                    if ($delete_version)
                    {
                        if ($this->content_object_deletion_allowed($object, 'version'))
                        {
                            $object->delete_version();
                        }
                        else
                        {
                            $failures ++;
                        }
                    }
                    else
                    {
                        if ($this->content_object_deletion_allowed($object))
                        {
                            if ($permanent)
                            {
                                $versions = $object->get_content_object_versions();
                                foreach ($versions as $version)
                                {
                                    $version->delete();
                                }
                            }
                            elseif ($recycled)
                            { 
                                $versions = $object->get_content_object_versions();
                                foreach ($versions as $version)
                                {
                                    $version->recycle();
                                }
                                
                            }
                        }
                        else
                        {
                            $failures ++;
                        }
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            
            if ($delete_version)
            {
                if ($failures)
                {
                    $message = 'SelectedVersionNotDeleted';
                }
                else
                {
                    $message = 'SelectedVersionDeleted';
                }
            }
            else
            {
                if ($failures)
                {
                    if (count($ids) == 1)
                    {
                        $message = 'SelectedObjectNot' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                    }
                    else
                    {
                        $message = 'NotAllSelectedObjects' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                    }
                }
                else
                {
                    if (count($ids) == 1)
                    {
                        $message = 'SelectedObject' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                    }
                    else
                    {
                        $message = 'AllSelectedObjects' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                    }
                }
            }
            
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = ($permanent ? RepositoryManager :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS : RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS);
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, RepositoryManager :: PARAM_DELETE_VERSION, RepositoryManager :: PARAM_DELETE_PERMANENTLY, RepositoryManager :: PARAM_DELETE_RECYCLED);
    }
}
?>