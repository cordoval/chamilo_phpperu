<?php
/**
 * $Id: taker.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_manager_component.class.php';
require_once dirname(__FILE__) . '/inc/laika_wizard.class.php';

class LaikaManagerTakerComponent extends LaikaManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('TakeLaika')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_TAKER, 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $laika_wizard = new LaikaWizard($this);
        $laika_wizard->run();
    }
}
?>