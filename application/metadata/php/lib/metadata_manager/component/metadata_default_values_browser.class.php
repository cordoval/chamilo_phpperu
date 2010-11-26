<?php
namespace application\metadata;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\ActionBarRenderer;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use admin\AdminManager;

require_once dirname(__FILE__) . '/../metadata_manager.class.php';
require_once dirname(__FILE__) . '/metadata_default_value_browser/metadata_default_value_browser_table.class.php';
/**
 * metadata component which allows the user to browse his metadata_default_values
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValuesBrowserComponent extends MetadataManager
{

    function run()
    {
        $metadata_property_type_id = Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE);
        if(!isset($metadata_property_type_id))
        {
            exit(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES));
        }
        
        $trail = new BreadcrumbTrail();

        $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
        $trail->add(new BreadCrumb($this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_PROPERTY_TYPES)), Translation :: get('BrowseObjects', array('OBJECTS' => Translation :: get('MetadataPropertyTypes')), Utilities :: COMMON_LIBRARIES)));
        $trail->add(new BreadCrumb($this->get_url(), Translation :: get('BrowseObjects', array('OBJECTS' => Translation :: get('MetadataDefaultValues')), Utilities :: COMMON_LIBRARIES)));

        $this->display_header($trail);

        $action_bar = $this->get_action_bar();

        $html[] = $action_bar->as_html();
        $html[] = $this->get_table( $metadata_property_type_id);

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table( $metadata_property_type_id)
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
//        $parameters['curriculum_courses_browser_column'] = '2';
        $parameters[Application :: PARAM_ACTION] =  MetadataManager::ACTION_BROWSE_METADATA_DEFAULT_VALUES;
        $parameters[MetadataManager :: PARAM_METADATA_PROPERTY_TYPE] = Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE);

        $condition = new EqualityCondition(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE, $metadata_property_type_id);
        $table = new MetadataDefaultValueBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('CreateObject', array('OBJECT' => Translation :: get('MetadataDefaultValue')), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_CREATE_METADATA_DEFAULT_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE))));
        $actions[] = new ToolbarItem(Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_IMPORT_METADATA_DEFAULT_VALUE, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE))));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }

}
?>