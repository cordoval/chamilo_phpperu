<?php
namespace application\metadata;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use admin\AdminManager;
/**
 * Component to edit an existing metadata_namespace object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataNamespaceUpdaterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$metadata_namespace = $this->retrieve_metadata_namespace(Request :: get(MetadataManager :: PARAM_METADATA_NAMESPACE));
		$form = new MetadataNamespaceForm(MetadataNamespaceForm :: TYPE_EDIT, $metadata_namespace, $this->get_url(array(MetadataManager :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_metadata_namespace();
			$this->redirect( Translation :: get($success ?'ObjectUpdated':'ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataNamespace')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_NAMESPACES));
		}
		else
		{
                    $trail = new BreadcrumbTrail();

                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
                    $trail->add(new BreadCrumb($this->get_url(), Translation :: get('UpdateObject', array('OBJECT' => Translation :: get('Namespace')), Utilities :: COMMON_LIBRARIES)));

                    $this->display_header($trail);
                    $form->display();
                    $this->display_footer();
		}
	}
}
?>