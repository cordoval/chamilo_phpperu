<?php
namespace application\metadata;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use admin\AdminManager;

/**
 * Component to edit an existing metadata_default_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValueUpdaterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $metadata_default_value = $this->retrieve_metadata_default_value(Request :: get(MetadataManager :: PARAM_METADATA_DEFAULT_VALUE));

        $metadata_property_type = $this->retrieve_metadata_property_type($metadata_default_value->get_property_type_id());

        $conditions = array();
        $conditions[] = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
        $allowed_metadata_property_attribute_types = $this->retrieve_allowed_metadata_property_attribute_types($conditions);

        foreach($allowed_metadata_property_attribute_types[$metadata_property_type->get_id()] as $id)
        {
            $metadata_property_attribute_types[$id] = $this->retrieve_metadata_property_attribute_type($id)->render_name();
        }
        $params[MetadataManager :: PARAM_METADATA_DEFAULT_VALUE] = Request :: get(MetadataManager :: PARAM_METADATA_DEFAULT_VALUE);
        $form = new MetadataDefaultValueForm(MetadataDefaultValueForm :: TYPE_EDIT, $metadata_default_value, $metadata_property_type, $metadata_property_attribute_types, $this->get_url($params));

        if($form->validate())
        {
                $success = $form->update_metadata_default_value();
                $this->redirect(Translation :: get($success ?'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_DEFAULT_VALUES, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $metadata_default_value->get_property_type_id()));
        }
        else
        {
                $trail = new BreadcrumbTrail();

                $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
                $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
                $trail->add(new BreadCrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES)), Translation :: get('BrowseObjects', array('OBJECTS' => Translation :: get('MetadataPropertyTypes')), Utilities :: COMMON_LIBRARIES)));
                $trail->add(new BreadCrumb($this->get_url(), Translation :: get('UpdateObject', array('OBJECT' => Translation :: get('MetadataDefaultValue')), Utilities :: COMMON_LIBRARIES)));

                $this->display_header($trail);
                
                $form->display();
                $this->display_footer();
        }
    }
}
?>