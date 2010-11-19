<?php
namespace repository\content_object\assessment;

use common\extensions\repo_viewer;

use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Translation;
use repository\ComplexBuilderComponent;
use repository\ComplexBuilder;
use repository\ContentObject;
use common\libraries\Utilities;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */

require_once dirname(__FILE__) . '/browser/assessment_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/assessment_browser_table_column_model.class.php';

class AssessmentBuilderBrowserComponent extends AssessmentBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }

    function get_additional_links()
    {
        $link['type'] = 'merge';
        $link['url'] = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => AssessmentBuilder :: ACTION_MERGE_ASSESSMENT));
        $link['title'] = Translation :: get('MergeAssessment');
        $links[] = $link;

        $link['type'] = 'select';
        $link['url'] = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER));
        $link['title'] = Translation :: get('SelectQuestions');

        $links[] = $link;

        return $links;
    }

    function get_complex_content_object_table_column_model()
    {
        return new AssessmentBrowserTableColumnModel($this);
    }

    function get_complex_content_object_table_cell_renderer()
    {
        return new AssessmentBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition());
    }
}

?>