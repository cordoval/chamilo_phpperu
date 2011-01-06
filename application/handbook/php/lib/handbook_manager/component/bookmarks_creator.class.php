<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Handbook component which allows the user to create a bookmark for a handbook-topic
 * @author Nathalie Blocry
 */
class HandbookManagerBookmarksCreatorComponent extends HandbookManager
{

	function run()
	{
		//TODO: implement
		$this->display_header();

		
		$this->display_footer();
	}

}
?>