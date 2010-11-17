<?php
namespace application\context_linker;

use common\libraries\Utilities;
/**
 * ContextLinker component which allows the user to browse the context_linker application
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerBrowserComponent extends ContextLinkerManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseContextLinker')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_context_links_url() . '">' . Translation :: get('BrowseObjects', array('OBJECT' => Translation :: get('ContextLink')), Utilities :: COMMON_LIBRARIES) . '</a>';

		$this->display_footer();
	}

}
?>