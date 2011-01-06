<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Handbook component which allows the user to browse the handbook bookmarks
 * @author Nathalie Blocry
 */
class HandbookManagerBookmarksBrowserComponent extends HandbookManager
{

	function run()
	{
            //TODO:implement
		$this->display_header();

		
		$this->display_footer();
	}

}
?>