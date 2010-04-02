<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/indicator_form.class.php';
/**
 * 
 * @author Nick Van Loocke
 */
class CbaManagerIndicatorCreatorComponent extends CbaManagerComponent
{

	function run()
	{		
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR)), Translation :: get('BrowseIndicator')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateIndicator')));
		$this->display_header($trail, false, true);
		
		$indicator = new Indicator();
		$indicator_criteria = new IndicatorCriteria();
		$indicator->set_owner_id($this->get_user_id());
		$form = new IndicatorForm(IndicatorForm :: TYPE_CREATOR_INDICATOR, $indicator, $indicator_criteria, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success_indicator = $form->create_indicator();
			$success_indicator_criteria = $form->create_indicator_criteria();
			if($success_indicator == $success_indicator_criteria)
				$success = 1;
				$new_category_id = $form->exportValue(Indicator :: PROPERTY_PARENT_ID);
			$this->redirect($success ? Translation :: get('IndicatorCreated') : Translation :: get('IndicatorNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR, 'category' => $new_category_id));
		}
		else
		{
			$form->display();
		}
		$this->display_footer();
	}
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}	
?>