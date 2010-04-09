<?php
require_once dirname(__FILE__) . '/../peer_assessment_display.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_display_component.class.php';
require_once dirname(__FILE__) . '/peer_assessment_page_table/peer_assessment_page_table.class.php';
require_once 'HTML/Table.php';
/**
 * author: Nick Van Loocke
 */

class PeerAssessmentDisplayPeerAssessmentViewerComponent extends PeerAssessmentDisplayComponent
{
   
	private $action_bar;

    function run()
    {
        $dm = RepositoryDataManager :: get_instance();

        $this->action_bar = $this->get_parent()->get_toolbar($this, $this->get_root_lo()->get_id(), $this->get_root_lo(), null);
        echo '<div id="trailbox2" style="padding:0px;">' . $this->get_parent()->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';

        if ($this->get_root_lo() != null)
        {
            echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . $this->get_root_lo()->get_title() . '</div><hr style="height:1px;color:#4271B5;width:100%;">';
			$table = new PeerAssessmentPageTable($this, $this->get_root_lo()->get_id());
            echo $table->as_html() . '</div>';
        }
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            return new OrCondition($conditions);
        }
        return null;
    }
	
}
?>