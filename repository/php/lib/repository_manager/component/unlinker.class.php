<?php
/**
 * $Id: publication_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object publication from the publication overview.
 */
class RepositoryManagerUnlinkerComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($id))
        {
            $failures = 0;
            
            $object = $this->retrieve_content_object($id);
            // TODO: Roles & Rights.
            if ($object->get_owner_id() == $this->get_user_id())
            {
                $versions = $object->get_content_object_versions();
                
                foreach ($versions as $version)
                {
                    if (! $version->delete_links())
                    {
                        $failures ++;
                    }
                }
            }
            else
            {
                $failures ++;
            }
            
            // TODO: SCARA - Structurize + cleanup (possible) failures
            

            if ($failures)
            {
                if ($failures >= 1)
                {
                    $message = 'SelectedObjectNotUnlinked';
                }
                else
                {
                    $message = 'NotAllVersionsUnlinked';
                }
            }
            else
            {
                $message = 'SelectedObjectUnlinked';
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_unlinker');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>