<?php
namespace application\weblcms\tool\assessment;

use common\libraries\ObjectTable;

/**
 * $Id: survey_user_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_survey_publisher.survey_user_table
 */
require_once dirname(__FILE__) . '/survey_user_table_data_provider.class.php';
require_once dirname(__FILE__) . '/survey_user_table_column_model.class.php';
require_once dirname(__FILE__) . '/survey_user_table_cell_renderer.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class SurveyUserTable extends ObjectTable
{
    const DEFAULT_NAME = 'assessment_publication_table';

    /**
     * Constructor.
     * @param int $owner The id of the current user.
     * @param array $types The types of objects that can be published in current
     * location.
     * @param string $query The search query, or null if none.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     * @see PublicationCandidateTableCellRenderer::PublicationCandidateTableCellRenderer()
     */
    function __construct($parent, $owner, $pid)
    {
        $data_provider = new SurveyUserTableDataProvider($parent, $owner, $pid);
        $column_model = new SurveyUserTableColumnModel();
        $cell_renderer = new SurveyUserTableCellRenderer($parent);
        parent :: __construct($data_provider, SurveyUserTable :: DEFAULT_NAME, $column_model, $cell_renderer);
    }

    /**
     * You should not be concerned with this method. It is only public because
     * of technical limitations.
     */
    function get_objects($offset, $count, $order_column)
    {
        $objects = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        foreach ($objects as $object)
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $object->get_id();
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