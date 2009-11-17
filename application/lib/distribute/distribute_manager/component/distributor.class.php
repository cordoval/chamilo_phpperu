<?php
/**
 * $Id: distributor.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.distribute.distribute_manager.component
 */
require_once dirname(__FILE__) . '/../distribute_manager.class.php';
require_once dirname(__FILE__) . '/../distribute_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/distribute/distributor/announcement_distributor.class.php';

class DistributeManagerDistributorComponent extends DistributeManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => DistributeManager :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS)), Translation :: get('Distribute')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Compose')));
        $trail->add_help('distribute general');
        
        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'announcement');
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'ContentObject: ';
            $publisher = new AnnouncementDistributor($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header($trail);
        //echo $publisher;
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>