<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\WebApplication;
use common\libraries\Header;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Redirect;
use common\libraries\Translation;
use common\libraries\Display;

use admin\AdminManager;

// required table classes
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/evaluation_formats_browser/evaluation_formats_browser_table.class.php';

class GradebookManagerAdminEvaluationFormatsBrowserComponent extends GradebookManager
{

    function run()
    {
        Header :: set_section('admin');
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(
                AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(
                AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER,
                DynamicTabsRenderer :: PARAM_SELECTED_TAB => GradebookManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Gradebook')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseEvaluationFormats')));

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $this->display_header($trail);
        $this->get_table_html();
        $this->display_footer();
    }

    function get_table_html()
    {
        $parameters = $this->get_parameters();
        $parameters[GradebookManager :: PARAM_ACTION] = GradebookManager :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS;
        $table = new EvaluationFormatsBrowserTable($this, $parameters);
        echo $table->as_html();
    }

}

?>