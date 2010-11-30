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
 * Component to create a new metadata_property_attribute_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypeCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
            $metadata_property_attribute_type = new MetadataPropertyAttributeType();
            $form = new MetadataPropertyAttributeTypeForm(MetadataPropertyAttributeTypeForm :: TYPE_CREATE, $metadata_property_attribute_type, $this->get_url(), $this->get_user());

            if($form->validate())
            {
                    $success = $form->create_metadata_property_attribute_type();
                    $this->redirect(Translation :: get($success ? 'ObjectCreated' : 'ObjectnotCreated', array('OBJECT' => Translation :: get('MetadataPropertyAttributeType')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
            }
            else
            {
                $trail = new BreadcrumbTrail();

                $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
                $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
                $trail->add(new BreadCrumb($this->get_url(), Translation :: get('CreateObject', array('OBJECT' => Translation :: get('MetadataNamespace')), Utilities :: COMMON_LIBRARIES)));

                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
	}
}
?>