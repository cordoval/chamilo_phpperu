<?php
require_once dirname(__FILE__) . '/../cba_manager.class.php';
require_once dirname(__FILE__) . '/../cba_manager_component.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaMoverComponent extends CbaManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {  	
        $ids = Request :: get(CbaManager :: PARAM_CRITERIA);
        if (! empty($ids))
        {
	        if (!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
        }
        
        $criteria = $this->retrieve_criteria($ids[0]);
        $parent = $criteria->get_parent_id();
        
        $form = $this->build_move_form($parent, $ids);
        if ($form->validate())
        {
			foreach ($ids as $id)
            {
            	$criteria = $this->retrieve_criteria($id);
            	$new_category_id = $this->move_criterias_to_category($form, $ids, $criteria);	
            }
            $this->redirect(Translation :: get('CriteriasMoved'), false, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA, 'category' => $new_category_id));      	
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA)), Translation :: get('BrowseCriteria')));
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_MOVE_CRITERIA, CbaManager :: PARAM_CRITERIA => $id)), Translation :: get('MoveCriteria')));
            
            $this->display_header($trail, true);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_move_form($exclude_category, $ids)
    {
        $url = $this->get_url(array(CbaManager :: PARAM_CRITERIA => $ids));
        $form = new FormValidator('criteria_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, $exclude_category);
        
        $form->addElement('select', Criteria :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        //$conditions[] = new NotCondition(new EqualityCondition(CriteriaCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(CriteriaCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_criteria_categories($condition);
        while ($criteria = $cdm->next_result())
        {
            $this->categories[$criteria->get_id()] = str_repeat('--', $level) . ' ' . $criteria->get_name();
            $this->retrieve_categories_recursive($criteria->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_criterias_to_category($form, $ids, $criteria)
    {    	
        $category = $form->exportValue(Criteria :: PROPERTY_PARENT_ID);
        if (! is_array($ids))
            $ids = array($ids);
        
        $condition = new InCondition(Criteria :: PROPERTY_ID, $ids);
        $cdm = CbaDataManager :: get_instance()->retrieve_criterias($condition);        
        $criteria->move($category);

        return $category;
    }
    
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}	
}
?>