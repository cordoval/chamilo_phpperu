<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class SurveyPageBuilderViewerComponent extends SurveyPageBuilder
{
    function run()
    {
        $viewer = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}

?>