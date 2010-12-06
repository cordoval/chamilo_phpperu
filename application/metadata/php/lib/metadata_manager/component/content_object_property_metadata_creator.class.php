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
 * Component to create a new content_object_property_metadata object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadataCreatorComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $content_object_property_metadata = new ContentObjectPropertyMetadata();
        $form = new ContentObjectPropertyMetadataForm(ContentObjectPropertyMetadataForm :: TYPE_CREATE, $content_object_property_metadata, $this->get_url(), $this->get_user(), $this);

        if($form->validate())
        {
                $success = $form->create_content_object_property_metadata();
                $this->redirect($success ? Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('ContentObjectpropertyMetadata')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
        }
        else
        {
            $trail = new BreadcrumbTrail();

            $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
            $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
            $trail->add(new BreadCrumb($this->get_url(), Translation :: get('CreateObject', array('OBJECT' => Translation :: get('ContentObjectMetadata')), Utilities :: COMMON_LIBRARIES)));

            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>