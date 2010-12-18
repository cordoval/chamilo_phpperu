<?php
namespace repository\content_object\survey_page;

use repository\ComplexBuilderComponent;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Translation;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
require_once dirname(__FILE__) . '/browser/survey_page_browser_table_cell_renderer.class.php';

class SurveyPageBuilderBrowserComponent extends SurveyPageBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }

    function get_additional_links()
    {
        $link = array();
        $link['type'] = 'merge';
        $link['url'] = $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_MERGE_SURVEY_PAGE));
        $link['title'] = Translation :: get('MergeSurveyPage');
        $links[] = $link;

        $link = array();
        $link['type'] = 'select';
        $link['url'] = $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER));
        $link['title'] = Translation :: get('SelectQuestions');
        $links[] = $link;

        return $links;
    }

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new SurveyPageBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
    }
}
?>