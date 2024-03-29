<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\DatetimeUtilities;
use common\libraries\Translation;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'tables/evaluation_browser_table/default_evaluation_browser_table_cell_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_format/evaluation_format.class.php';

class EvaluationBrowserTableCellRenderer extends DefaultEvaluationBrowserTableCellRenderer
{
    /**
     * The browser component
     */

    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $evaluation)
    {
        $optional_properties = $evaluation->get_optional_properties();
        $format = $this->browser->retrieve_evaluation_format($evaluation->get_format_id());
        $evaluation_format = EvaluationFormat :: factory($format->get_title());
        $evaluation_format->set_score($optional_properties['score']);
        switch ($column->get_name())
        {
            case Evaluation :: PROPERTY_EVALUATION_DATE :
                return DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $evaluation->get_evaluation_date());
            case 'user' :
                return $optional_properties['user'];
            case 'evaluator' :
                return $optional_properties['evaluator'];
            case GradeEvaluation :: PROPERTY_SCORE :
                return $evaluation_format->get_formatted_score();
            case GradeEvaluation :: PROPERTY_COMMENT :
                return $optional_properties['comment'];
        }
        if ($column === EvaluationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($evaluation);
        }

        return parent :: render_cell($column, $evaluation);
    }

    /**
     * Gets the action links to display
     * @param Format $evaluation_format The evaluation format for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($evaluation)
    {
        $toolbar = new Toolbar();
        if ($evaluation->get_evaluator_id() == $this->browser->get_user_id())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_evaluation_editing_url($evaluation), ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_evaluation_deleting_url($evaluation), ToolbarItem :: DISPLAY_ICON, true));
        }

        return $toolbar->as_html();
    }

}
