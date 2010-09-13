<?php
/**
 * $Id: reverter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to revert a
 * learning object from the users repository to a previous state.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManagerReverterComponent extends RepositoryManager
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
            foreach ($ids as $object_id)
            {
                $object = $this->retrieve_content_object($object_id);
                // TODO: Roles & Rights.
                if ($object->get_owner_id() == $this->get_user_id())
                {
                    if ($this->content_object_revert_allowed($object))
                    {
                        $object->version();
                    }
                    else
                    {
                        $failures ++;
                    }
                
                }
                else
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                $message = 'SelectedObjectNotReverted';
            }
            else
            {
                $message = 'SelectedObjectReverted';
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_reverter');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>