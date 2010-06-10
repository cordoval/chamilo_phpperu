<?php
/**
 * $Id: informer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../../laika_scale.class.php';

class LaikaManagerInformerComponent extends LaikaManager
{
    private $attempt;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Scales')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_INFORMER, 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $ordering = array();
        $ordering[] = new ObjectTableOrder(LaikaScale :: PROPERTY_CLUSTER_ID);
        $ordering[] = new ObjectTableOrder(LaikaScale :: PROPERTY_TITLE);
        
        $scales = $this->retrieve_laika_scales(null, null, null, $ordering);
        
        $data = array();
        
        while ($scale = $scales->next_result())
        {
            $scale_row = array();
            $scale_row[] = $scale->get_cluster()->get_title();
            $scale_row[] = $scale->get_title();
            $scale_row[] = $scale->get_description();
            
            $data[] = $scale_row;
        }
        
        $table = new SortableTableFromArray($data);
        $table->set_additional_parameters($this->get_parameters());
        $table->set_header(0, Translation :: get('Cluster'), false);
        $table->set_header(1, Translation :: get('Scale'), false);
        $table->set_header(2, Translation :: get('Description'), false);
        
        $this->display_header($trail);
        echo $table->as_html();
        $this->display_footer();
    }
}
?>