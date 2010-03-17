<?php
require_once dirname(__FILE__) . '/../cba_manager.class.php';
require_once dirname(__FILE__) . '/../cba_manager_component.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerIndicatorMoverComponent extends CbaManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {  	
        $ids = Request :: get(CbaManager :: PARAM_INDICATOR);
        if (! empty($ids))
        {
	        if (!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
        }
        
        $indicator = $this->retrieve_indicator($ids[0]);
        $parent = $indicator->get_parent_id();
        
        $form = $this->build_move_form($parent, $ids);
        if ($form->validate())
        {
            $new_category_id = $this->move_indicators_to_category($form, $ids, $indicator);
            $this->redirect(Translation :: get('IndicatorsMoved'), false, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR, 'category' => $new_category_id));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR)), Translation :: get('BrowseIndicator')));
            $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_MOVE_INDICATOR, CbaManager :: PARAM_INDICATOR => $id)), Translation :: get('MoveIndicator')));
            
            $this->display_header($trail, true);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_move_form($exclude_category, $ids)
    {
        $url = $this->get_url(array(CbaManager :: PARAM_INDICATOR => $ids));
        $form = new FormValidator('indicator_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        $this->retrieve_categories_recursive(0, $exclude_category);
        
        $form->addElement('select', Indicator :: PROPERTY_PARENT_ID, Translation :: get('SelectCategory'), $this->categories);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(IndicatorCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(IndicatorCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cdm = CbaDataManager :: get_instance()->retrieve_indicator_categories($condition);
        while ($indicator = $cdm->next_result())
        {
            $this->categories[$indicator->get_id()] = str_repeat('--', $level) . ' ' . $indicator->get_name();
            $this->retrieve_categories_recursive($indicator->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_indicators_to_category($form, $ids, $indicator)
    {    	
        $category = $form->exportValue(Indicator :: PROPERTY_PARENT_ID);
        if (! is_array($ids))
            $ids = array($ids);
        
        $condition = new InCondition(Indicator :: PROPERTY_ID, $ids);
        $cdm = CbaDataManager :: get_instance()->retrieve_indicators($condition);        
        $indicator->move($category);

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