<?php
namespace repository\content_object\glossary;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

class GlossaryComplexDisplayPreview extends ComplexDisplayPreview implements GlossaryComplexDisplaySupport
{

    function run()
    {
        ComplexDisplay :: launch(Glossary :: get_type_name(), $this);
    }

    /**
     * Preview mode, so always return true.
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }
}
?>