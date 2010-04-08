<?php
require_once dirname(__FILE__) . '/peer_assessment_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_manager.class.php';

/**
 * Table to display a list of peer_assessment_publications
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'peer_assessment_publication_browser_table';

    /**
     * Constructor
     */
    function PeerAssessmentPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new PeerAssessmentPublicationBrowserTableColumnModel();
        $renderer = new PeerAssessmentPublicationBrowserTableCellRenderer($browser);
        $data_provider = new PeerAssessmentPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(PeerAssessmentManager :: PARAM_DELETE_SELECTED_PEER_ASSESSMENT_PUBLICATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $objects = $this->get_data_provider()->get_objects($offset, $count, $order_property);
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        foreach ($objects as $object)
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $this->get_cell_renderer()->render_id_cell($object);
                //$row[] = $object->get_id();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
            }
            $table_data[] = $row;
        }
        return $table_data;
    
    }
}
?>