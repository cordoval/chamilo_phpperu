<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ComplexBuilderParentChangerComponent extends ComplexBuilderComponent
{
	const PARAM_NEW_PARENT = 'new_parent';
	
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        
        $root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        $cloi_ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
        $parent_cloi = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
        
        $parameters = array(ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $parent_cloi, ComplexBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_ids, 'publish' => Request :: get('publish'));
        
        $rdm = $this->rdm = RepositoryDataManager :: get_instance();
        
    	if (! empty($cloi_ids))
        {
            if (! is_array($cloi_ids))
            {
                $cloi_ids = array($cloi_ids);
            }
            
            $parents = $this->get_possible_parents($root_lo, $parent_cloi);
            
            $form = new FormValidator('move', 'post', $this->get_url($parameters));
            $form->addElement('select', self :: PARAM_NEW_PARENT, Translation :: get('NewParent'), $parents);
            $form->addElement('submit', 'submit', Translation :: get('Move')); 
            if ($form->validate()) 
            {
            	$selected_parent = $form->exportValue(self :: PARAM_NEW_PARENT);
            	if($selected_parent == 0)
            	{
            		$parent = $root_lo;
            	}
            	else
            	{
            		$parent = $rdm->retrieve_complex_content_object_item($selected_parent);
            		$parent = $parent->get_ref();
            	}
            	
            	$failures = 0;
            	$size = 0;
            	
            	if( (!$parent_cloi && $parent != $root_lo) || $parent_cloi != $selected_parent)
            	{
					$clois = $rdm->retrieve_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_ID, $cloi_ids));
					$size = $clois->size();
					$old_parent = 0;
					
					while($cloi = $clois->next_result())
					{
						if(!$old_parent)
						{
							$old_parent = $cloi->get_parent();	
						}
						
						$cloi->set_parent($parent);
						$cloi->set_display_order($rdm->select_next_display_order($parent));
						$cloi->update();
					}
					
					$this->fix_display_order_values($old_parent);
            	}
            	
				if($failures == 0)
				{
					if($size > 1)
					{
						$message = 'ObjectMoved';
					}
					else
					{
						$message = 'ObjectsMoved';
					}
				}
				else
				{
					if($size > 1)
					{
						$message = 'ObjectNotMoved';
					}
					else
					{
						$message = 'ObjectsNotMoved';
					}
				}
            	
				$parameters[ComplexBuilder :: PARAM_BUILDER_ACTION] = ComplexBuilder :: ACTION_BROWSE_CLO;
				$parameters[ComplexBuilder :: PARAM_SELECTED_CLOI_ID] = null;
            	$this->redirect($message, ($failures > 0), $parameters);
            	
            }
            else
            {
            	$menu_trail = $this->get_clo_breadcrumbs();
            	$trail->merge($menu_trail); 
            	$trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('Move'))); 
                $this->display_header($trail); 
                echo $form->display();
                $this->display_footer();
            }
        }
        else
        {
        	$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    
    }
    
    private function get_possible_parents($root_lo, $parent_cloi)
    {
    	$rdm = $this->rdm;
    	$root = $rdm->retrieve_content_object($root_lo);
    	
    	if(!$parent_cloi)
    	{
    		$current = ' (' . Translation :: get('Current') . ')';
    	}
    	
    	$parents = array(0 => $root->get_title() . $current);
		$parents = $this->get_children_from_content_object($root_lo, $parent_cloi, $parents);    	
    	
    	return $parents;
    }
    
    private function get_children_from_content_object($content_object, $current_parent, $parents, $level = 1)
    {
    	$rdm = $this->rdm;
    	$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $content_object);
    	$children = $rdm->retrieve_complex_content_object_items($condition);
    	
    	while($child = $children->next_result())
    	{
    		$ref_id = $child->get_ref();
    		$ref_object = $rdm->retrieve_content_object($ref_id);
    		
    		if(!$ref_object->is_complex_content_object())
    		{
    			continue;
    		}
    		
    		if($child->get_id() == $current_parent)
    		{
    			$current = ' (' . Translation :: get('Current') . ')';
    		}
    		else
    		{
    			$current = '';
    		}
    		
    		$parents[$child->get_id()] = str_repeat('--', $level) . ' ' . $ref_object->get_title() . $current;
    		
    		$parents = $this->get_children_from_content_object($ref_id, $current_parent, $parents, $level + 1);
    	}
    	
    	return $parents;
    }
    
    private function fix_display_order_values($parent_id)
    {
    	$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent_id);
        $clois = $this->rdm->retrieve_complex_content_object_items($condition, array(new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER)));
        
        $i = 1;
        
        while ($cloi = $clois->next_result())
        {
            $cloi->set_display_order($i);
            $cloi->update();
            $i++;
        }
    }
}

?>