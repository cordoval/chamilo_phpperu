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
 * Component to edit an existing content_object_property_metadata object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadataUpdaterComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$content_object_property_metadata = $this->retrieve_content_object_property_metadata(Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA));
		$form = new ContentObjectPropertyMetadataForm(ContentObjectPropertyMetadataForm :: TYPE_EDIT, $content_object_property_metadata, $this->get_url(array(MetadataManager :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id())), $this->get_user(),$this);

		if($form->validate())
		{
			$success = $form->update_content_object_property_metadata();
			$this->redirect($success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
		}
		else
		{
                    $trail = new BreadcrumbTrail();

                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
                    $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
                    $trail->add(new BreadCrumb($this->get_url(), Translation :: get('UpdateObjects', array('OBJECTS' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES)));

                    $this->display_header($trail);
                    $form->display();
                    $this->display_footer();
		}
	}
}
?>