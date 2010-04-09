<?php
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/peer_assessment_page_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/peer_assessment/peer_assessment_display.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 * 
 * author: Nick Van Loocke
 */
class PeerAssessmentPageTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    private $table_actions;
    private $browser;
    private $dm;
    private $pid;
    private $cid;

    /**
     * Constructor.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     */
    function PeerAssessmentPageTableCellRenderer($browser)
    {
        $this->table_actions = array();
        $this->browser = $browser;
        $this->dm = RepositoryDataManager :: get_instance();

    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === PeerAssessmentPageTableColumnModel :: get_action_column())
        {
            return $this->get_actions($publication);
        }

        $this->pid = Request :: get('pid');

        $peer_assessment_page = $this->get_publication_from_clo_item($publication);
        $this->cid = $publication->get_id();

        if ($publication->get_additional_property('is_homepage') == 1)
        {
            $homepage = ' (' . Translation :: get('homepage') . ')';
        }

        if (isset($peer_assessment_page))
        {
            if ($property = $column->get_title())
            {
                switch ($property)
                {
                    case 'Title' :
                        return '<a href="' . $this->browser->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->pid, 'selected_cloi' => $this->cid)) . '">' . htmlspecialchars($peer_assessment_page->get_title()) . '</a>' . $homepage;
                    //default:
                    //return '<a href="' . $this->browser->get_url(array(Tool :: PARAM_ACTION => PeerAssessmentTool :: ACTION_VIEW_PEER_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id() )) . '">' . htmlspecialchars($peer_assessment_page->get_title()) . '</a>';
                    case '[=PeerAssessment=Title=]' :
                        return '<a href="' . $this->browser->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->pid, 'selected_cloi' => $this->cid)) . '">' . htmlspecialchars($peer_assessment_page->get_title()) . '</a>' . $homepage;
                    case 'versions' :
                        return $peer_assessment_page->get_version_count();
                    case 'Description' :
                        $description = str_ireplace(']]', '', str_ireplace('[[', '', str_ireplace('=', '', $peer_assessment_page->get_description())));
                        return Utilities :: truncate_string($description, 50);
                }
            }
        }

        return parent :: render_cell($column, $peer_assessment_page);
    }

    function get_actions($publication)
    {
        //if(!PeerAssessmentTool ::is_peer_assessment_locked($publication->get_parent()))


        if ($this->browser->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $actions[] = array('href' => $this->browser->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DELETE, 'selected_cloi' => $publication->get_id(), Tool :: PARAM_PUBLICATION_ID => $this->pid)), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }

        if ($this->browser->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $actions[] = array('href' => $this->browser->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_UPDATE, 'selected_cloi' => $publication->get_id(), Tool :: PARAM_PUBLICATION_ID => $this->pid)), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');

            if (($publication->get_additional_property('is_homepage') == 0))
            {
                $actions[] = array('href' => $this->browser->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_SET_AS_HOMEPAGE, 'selected_cloi' => $this->cid, 'pid' => Request :: get('pid'))), 'label' => Translation :: get('SetAsHomepage'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
            }
            else
            {
                $actions[] = array('href' => '', 'label' => Translation :: get('SetAsHomepage'), 'img' => Theme :: get_common_image_path() . 'action_home_na.png');
            }
        }
        /*else
        {
            $actions[] = array(
			'href' => '',
			'label' => Translation :: get('Locked'),
			'img' => Theme :: get_common_image_path().'action_delete_na.png'
			);

			$actions[] = array(
			'href' => '',
			'label' => Translation :: get('Locked'),
			'img' => Theme :: get_common_image_path().'action_edit_na.png'
			);

            $actions[] = array(
            'href' => '',
            'label' => Translation :: get('Locked'),
            'img' => Theme :: get_common_image_path().'action_home_na.png'
            );
        }*/

        if (count($actions) > 0)
            return Utilities :: build_toolbar($actions);
    }

    /**
     * Gets the links to publish or edit and publish a learning object.
     * @param ContentObject $peer_assessment_page The learning object for which the
     * links should be returned.
     * @return string A HTML-representation of the links.
     */
    private function get_publish_links($peer_assessment_page)
    {
        $toolbar_data = array();
        $table_actions = $this->table_actions;

        foreach ($table_actions as $table_action)
        {
            $table_action['href'] = sprintf($table_action['href'], $peer_assessment_page->get_id());
            $toolbar_data[] = $table_action;
        }

        return Utilities :: build_toolbar($toolbar_data);
    }

    private function get_publication_from_clo_item($clo_item)
    {
        $publication = $this->dm->retrieve_content_objects(new EqualityCondition(ContentObject :: PROPERTY_ID, $clo_item->get_default_property(ComplexContentObjectItem :: PROPERTY_REF), ContentObject :: get_table_name()))->as_array();
        return $publication[0];
    }
}
?>