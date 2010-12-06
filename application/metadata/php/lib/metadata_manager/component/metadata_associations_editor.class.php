<?php
namespace application\metadata;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use admin\AdminManager;
/**
 * Component to edit an existing metadata_attribute_nesting object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataAssociationsEditorComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       $metadata_property_type = $this->retrieve_metadata_property_type(Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE));
      

        $form = new MetadataAssociationsForm(MetadataAssociationsForm :: TYPE_EDIT, $metadata_property_type, $this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id())), $this->get_user(), $this);

        if($form->validate())
        {
            $success = $form->update_associations();
            $this->redirect($success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('MetadataAttributeNesting')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataAttributeNesting')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
        }
        else
        {
            $trail = new BreadcrumbTrail();

            $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
            $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
            $trail->add(new BreadCrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES)), Translation :: get('BrowseObjects', array('OBJECTS' => Translation :: get('MetadataPropertyTypes')), Utilities :: COMMON_LIBRARIES)));
            $trail->add(new BreadCrumb($this->get_url(), Translation :: get('EditAssociations')));

            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>