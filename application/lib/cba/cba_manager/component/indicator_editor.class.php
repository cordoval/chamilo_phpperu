<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/indicator_form.class.php';

/**
 * Component to edit an existing competency object
 * @author Nick Van Loocke
 */
class CbaManagerIndicatorEditorComponent extends CbaManagerComponent
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
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR)), Translation :: get('BrowseIndicator')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateIndicator')));
		$this->display_header($trail, false, true);
		
		$indicator = $this->retrieve_indicator(Request :: get(CbaManager :: PARAM_INDICATOR));
		$indicator_criteria = $this->retrieve_indicator_criteria(Request :: get(CbaManager :: PARAM_INDICATOR));
		$form = new IndicatorForm(IndicatorForm :: TYPE_EDITOR_INDICATOR, $indicator, $indicator_criteria,  $this->get_url(array(CbaManager :: PARAM_INDICATOR => $indicator->get_id())), $this->get_user());

		if($form->validate())
		{
			$success_indicator = $form->update_indicator();
			$success_indicator_criteria = $form->update_indicator_criteria();
			if($success_indicator == $success_indicator_criteria)
				$success = 1;
			
			foreach ($ids as $id)
            {
				$indicator = $this->retrieve_indicator($id);
           		$new_category_id = $this->move_indicators_to_category($form, $ids, $indicator);	
            }
			
			$this->redirect($success ? Translation :: get('IndicatorUpdated') : Translation :: get('IndicatorNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR, 'category' => $new_category_id));// array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR));        
		}
		else
		{
			$form->display();
		}
		$this->display_footer();
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