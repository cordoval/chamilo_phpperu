<?php
namespace application\metadata;

use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Redirect;
use common\libraries\DynamicTabsRenderer;
use admin\AdminManager;

/**
 * metadata component which allows the user to browse his metadata_namespaces
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataNamespacesBrowserComponent extends MetadataManager
{
    function run()
    {
        $trail = new BreadcrumbTrail();
        
        $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new BreadCrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MetadataManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Metadata')));
        $trail->add(new BreadCrumb($this->get_url(), Translation :: get('BrowseObjects', array('OBJECTS' => Translation :: get('MetadataNamespaces')), Utilities :: COMMON_LIBRARIES)));

        $this->display_header($trail);

        $html = array();

        $action_bar = $this->get_action_bar();

        $html[] = $action_bar->as_html();
        $html[] = $this->get_table();

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
//        $parameters['curriculum_courses_browser_column'] = '2';
        $parameters[Application :: PARAM_ACTION] =  MetadataManager::ACTION_BROWSE_METADATA_NAMESPACES;

        $table = new MetadataNamespaceBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_CREATE_METADATA_NAMESPACE)));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }
}
?>