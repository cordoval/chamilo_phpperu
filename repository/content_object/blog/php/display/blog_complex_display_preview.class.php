<?php
namespace repository\content_object\blog;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

class BlogComplexDisplayPreview extends ComplexDisplayPreview implements BlogComplexDisplaySupport
{

    function run()
    {
        ComplexDisplay :: launch(Blog :: get_type_name(), $this);
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