<?php
/**
 * $Id: grapher.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/laika_grapher_filter_form.class.php';

class LaikaManagerGrapherComponent extends LaikaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RenderGraphs')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: RIGHT_VIEW, LaikaRights :: LOCATION_GRAPHER, LaikaRights :: TYPE_LAIKA_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail);
        
        $form = new LaikaGrapherFilterForm($this, $this->get_url());
        
        if ($form->validate())
        {
            echo $form->render_graphs();
        }
        else
        {
            echo $form->display();
        }
        
        $this->display_footer();
    }
}
?>