<?php
namespace repository\content_object\wiki;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

use common\libraries\Translation;

class WikiComplexDisplayPreview extends ComplexDisplayPreview implements WikiComplexDisplaySupport
{

    function run()
    {
        ComplexDisplay :: launch(Wiki :: get_type_name(), $this);
    }

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_wiki_page_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_wiki_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
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

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_wiki_publication()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }
}
?>