<?php
namespace repository\content_object\handbook;

use repository\ComplexBuilderComponent;

require_once dirname(__FILE__) . '/../handbook_builder.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class HandbookBuilderMoverComponent extends HandbookBuilder
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