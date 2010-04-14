<?php
/**
 * @package application.ovis.ovis.component
 */
require_once dirname(__FILE__).'/../ovis_manager.class.php';
require_once dirname(__FILE__).'/../ovis_manager_component.class.php';

/**
 * Ovis component which allows the user to browse the ovis application
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class OvisManagerBrowserComponent extends OvisManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseOvis')));

		$this->display_header($trail);


		$this->display_footer();
	}

}
?>