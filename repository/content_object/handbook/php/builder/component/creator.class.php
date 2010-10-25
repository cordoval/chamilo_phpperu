<?php
namespace repository\content_object\handbook;

use HandbookBuilder;
use repository\ComplexBuilderComponent;
require_once dirname(__FILE__) . '/../handbook_builder.class.php';

/**
 */
class HandbookBuilderCreatorComponent extends HandbookBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>