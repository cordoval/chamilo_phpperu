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
 */
/**
 * Component to edit an existing metadata_property_attribute_type object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypeUpdaterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $metadata_property_attribute_type = $this->retrieve_metadata_property_attribute_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE));
        $form = new MetadataPropertyAttributeTypeForm(MetadataPropertyAttributeTypeForm :: TYPE_EDIT, $metadata_property_attribute_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE => $metadata_property_attribute_type->get_id())), $this->get_user());

        if($form->validate())
        {
                $success = $form->update_metadata_property_attribute_type();
                $this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataPropertyAttributeType')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
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