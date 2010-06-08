<?php
require_once dirname(__FILE__) . '/../cba_manager.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyMoverComponent extends CbaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {  	
        $ids = Request :: get(CbaManager :: PARAM_COMPETENCY);
        if (! empty($ids))
        {
	        if (!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
        }
        
        $competency = $this->retrieve_competency($ids[0]);
        $parent = $competency->get_parent_id();
        
        $form = $this->build_move_form($parent, $ids);
        if ($form->validate())
        {
        	foreach ($ids as $id)
            {
            	$competency = $this->retrieve_competency($id);
            	$new_category_id = $this->move_competencys_to_category($form, $ids, $competency);	
            }
            $this->redirect(Translation :: get('CompetencysMoved'), false, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY, 'category' => $new_category_id));    
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('BrowseCompetency')));
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_MOVE_COMPETENCY, CbaManager :: PARAM_COMPETENCY => $id)), Translation :: get('MoveCompetency')));
            
            $this->display_header($trail, true);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_move_form($exclude_category, $ids)
    {
        $url = $this->get_url(array(CbaManager :: PARAM_COMPETENCY => $ids));
        $form = new FormValidator('competency_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, $exclude_category);
        
		$select = $form->add_select(Competency :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
    	$competency = $this->retrieve_competency($ids[0]);
        $select->setSelected($competency->get_parent_id());
        $form->addRule(Competency :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
		$conditions[] = new EqualityCondition(CompetencyCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_competency_categories($condition);
        while ($competency = $cdm->next_result())
        {
            $this->categories[$competency->get_id()] = str_repeat('--', $level) . ' ' . $competency->get_name();
            $this->retrieve_categories_recursive($competency->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_competencys_to_category($form, $ids, $competency)
    {    	
        $category = $form->exportValue(Competency :: PROPERTY_PARENT_ID);
        if (! is_array($ids))
            $ids = array($ids);
        
        $condition = new InCondition(Competency :: PROPERTY_ID, $ids);
        $cdm = CbaDataManager :: get_instance()->retrieve_competencys($condition);        
        $competency->move($category);

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