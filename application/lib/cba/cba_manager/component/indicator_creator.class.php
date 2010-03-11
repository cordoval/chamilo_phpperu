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
		$form = new IndicatorForm(IndicatorForm :: TYPE_CREATOR_INDICATOR, $indicator, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_indicator();
			$this->redirect($success ? Translation :: get('IndicatorCreated') : Translation :: get('IndicatorNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR));
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