<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Handbook component which allows the user to browse the handbook application
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookManagerTopicPickerComponent extends HandbookManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Browse', array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_handbook_publications_url() . '">' . Translation :: get('Browse' , array('OBJECT' => Translation::get('HandbookPublications')), Utilities::COMMON_LIBRARIES) . '</a>';

//                var_dump($_SESSION);
//                var_dump($_POST);
//                var_dump($_GET);
//                var_dump($_REQUEST);
//
//                $table = new HandbookPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'handbook', Application :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE), $this->get_condition());
//                $table = new HandbookTopicContentObjectTable($this, $this->get_user(), $this->get_types(), $this->get_query(), $actions);
//                echo $table->as_html();
		$this->display_footer();
	}

}
?>