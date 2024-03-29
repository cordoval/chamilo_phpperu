<?php
namespace application\laika;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\WebApplication;
use common\libraries\Application;
/**
 * $Id: taker.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/inc/laika_wizard.class.php';

class LaikaManagerTakerComponent extends LaikaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(
                Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('TakeLaika')));

        if (! LaikaRights :: is_allowed(LaikaRights :: RIGHT_VIEW, LaikaRights :: LOCATION_TAKER, LaikaRights :: TYPE_LAIKA_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $laika_wizard = new LaikaWizard($this);
        $laika_wizard->run();
    }
}
?>