<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/browser/survey_page_browser_table_cell_renderer.class.php';

class SurveyPageBuilderBrowserComponent extends SurveyPageBuilder
{

    function run()
    {
        
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new SurveyPageBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
    }

}

?>