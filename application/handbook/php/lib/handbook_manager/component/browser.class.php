<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
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
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Browse', array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_handbook_publications_url() . '">' . Translation :: get('Browse' , array('OBJECT' => Translation::get('HandbookPublications')), Utilities::COMMON_LIBRARIES) . '</a>';

		$this->display_footer();
	}

}
?>