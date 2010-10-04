<?php
/**
 * $Id: mover.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to move learning objects between categories in
 * the repository.
 */
class RepositoryManagerMoverComponent extends RepositoryManager
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
            
            
            $object = $this->retrieve_content_object($ids[0]);
            $parent = $object->get_parent_id();
            
            $this->tree = array();
            if ($parent != 0)
                $this->tree[] = Translation :: get('Repository');
            
            $this->get_categories_for_select(0, $parent);
            $form = new FormValidator('move', 'post', $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $ids)));
            $form->addElement('select', RepositoryManager :: PARAM_DESTINATION_CONTENT_OBJECT_ID, Translation :: get('NewCategory'), $this->tree);
            $form->addElement('submit', 'submit', Translation :: get('Move')); 
            if ($form->validate()) 
            {
                $destination = $form->exportValue(RepositoryManager :: PARAM_DESTINATION_CONTENT_OBJECT_ID);
                $failures = 0;
                foreach ($ids as $id)
                {
                    $object = $this->retrieve_content_object($id);
                    $versions = $this->get_version_ids($object);
                    
                    foreach ($versions as $version)
                    {
                        $object = $this->retrieve_content_object($version);
                        // TODO: Roles & Rights.
                        if ($object->get_owner_id() != $this->get_user_id())
                        {
                            $failures ++;
                        }
                        elseif ($object->get_parent_id() != $destination)
                        {
                            if (! $object->move_allowed($destination))
                            {
                                $failures ++;
                            }
                            else
                            {
                                $object->move($destination);
                            }
                        }
                    }
                }
                
                // TODO: SCARA - Correctto reflect possible version errors
                if ($failures)
                {
                    if (count($ids) == 1)
                    {
                        $message = 'SelectedObjectNotMoved';
                    }
                    else
                    {
                        $message = 'NotAllSelectedObjectsMoved';
                    }
                }
                else
                {
                    if (count($ids) == 1)
                    {
                        $message = 'SelectedObjectMoved';
                    }
                    else
                    {
                        $message = 'AllSelectedObjectsMoved';
                    }
                }
                
                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CATEGORY_ID] = $object->get_parent_id();
                $this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);
            }
            else
            {
                //$renderer = clone $form->defaultRenderer();
                //$renderer->setElementTemplate('{label} {element} ');
                //$form->accept($renderer);
                
                /*if (count($ids) == 1)
                    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $ids[0])), $this->retrieve_content_object($ids[0])->get_title()));
                else
                    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS), Translation :: get('Objects'))));
                
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));*/ 
                $this->display_header(null, false, true); 
                echo $form->toHTML();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    /**
     * Get all categories from which a user can select a target category when
     * moving learning objects.
     * @param array $exclude An array of category-id's which should be excluded
     * from the resulting list.
     * @return array A list of possible categories from which a user can choose.
     * Can be used as input for a QuickForm select field.
     */
    
    private $level = 1;
    private $tree = array();

    private function get_categories_for_select($parent_id, $current_parent)
    {
        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
        //$conditions[] = new NotCondition(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $current_parent));
        
        $condition = new AndCondition($conditions);
        
        $categories = $this->retrieve_categories($condition);
        
        $tree = array();
        while ($cat = $categories->next_result())
        {
        	$this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
        	
        	if($current_parent == $cat->get_id())
        	{
        		$this->tree[$cat->get_id()] .= ' (' . Translation :: get('Current') . ')';
        	}
        	
            $this->level ++;
            $this->get_categories_for_select($cat->get_id(), $current_parent);
            $this->level --;
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_mover');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>