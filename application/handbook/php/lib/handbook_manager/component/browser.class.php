<?php
/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Handbook component which allows the user to browse the handbook application
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookManagerBrowserComponent extends HandbookManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseHandbook')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_handbook_publications_url() . '">' . Translation :: get('BrowseHandbookPublications') . '</a>';

		$this->display_footer();
	}

}
?>