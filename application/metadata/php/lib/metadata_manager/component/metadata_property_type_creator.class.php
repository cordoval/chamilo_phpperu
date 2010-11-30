<?php 
namespace application\metadata;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use admin\AdminManager;

/**
 * Component to create a new metadata_property_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyTypeCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$metadata_property_type = new MetadataPropertyType();
		$form = new MetadataPropertyTypeForm(MetadataPropertyTypeForm :: TYPE_CREATE, $metadata_property_type, $this->get_url(), $this->get_user());

		if($form->validate())
		{
                    if($success = $form->create_metadata_property_type())
                    {
                        $this->redirect(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES), false,array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_ASSOCIATIONS, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $success->get_id()));
                    }
                    else
                    {
                        $this->redirect(Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES), true);
                    }
                }
                else
		{
                    $trail = new BreadcrumbTrail();

                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
                    $trail->add(new BreadCrumb($this->get_url(), Translation :: get('CreateObject', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES)));

                    $this->display_header($trail);
                    $form->display();
                    $this->display_footer();
		}
	}
}
?>