<?php
/**
 * $Id: template_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerTemplateDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $lo_id)
            {
                $lo = $this->retrieve_content_object($lo_id);
                
                if (! $lo->delete())
				/*{
					//$failures++;
				}*/
				
				$or_conditions = array();
                $or_conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $lo_id, ComplexContentObjectItem :: get_table_name());
                $or_conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $lo_id);
                
                $condition = new OrCondition($or_conditions);
                $clois = $this->retrieve_complex_content_object_items($condition);
                while ($cloi = $clois->next_result())
                {
                    $cloi->delete();
                }
            }
            
            if ($failures > 0)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectNotDeleted';
                }
                else
                {
                    $message = 'NotAllSelectedObjectsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectDeleted';
                }
                else
                {
                    $message = 'AllSelectedObjectsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures > 0), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('RepositoryManagerTemplateBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_template_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>