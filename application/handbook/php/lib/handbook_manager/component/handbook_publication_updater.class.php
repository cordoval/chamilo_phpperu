<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;


require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_publication_form.class.php';

/**
 * Component to edit an existing handbook_publication object
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookPublicationUpdaterComponent extends HandbookManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE)), Translation :: get('Browse' , array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));
		$trail->add(new Breadcrumb($this->get_url(array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS)), Translation :: get('Browse', array('OBJECT' => Translation::get('HandbookPublications')), Utilities::COMMON_LIBRARIES)));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ObjectUpdate',  array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));

		$handbook_publication = $this->retrieve_handbook_publication(Request :: get(HandbookManager :: PARAM_HANDBOOK_PUBLICATION));
		$form = new HandbookPublicationForm(HandbookPublicationForm :: TYPE_EDIT, $handbook_publication, $this->get_url(array(HandbookManager :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_handbook_publication();
			$this->redirect($success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES), !$success, array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>