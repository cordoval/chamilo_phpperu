<?php
namespace repository\content_object\wiki;

use repository\ComplexDisplaySupport;

/**
 * A class implements the <code>WikiComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a WikiComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface WikiComplexDisplaySupport extends ComplexDisplaySupport
{

    /**
     * Returns the name of the wiki page statistics template
     *
     * @return string
     */
    function get_wiki_page_statistics_reporting_template_name();

    /**
     * Returns the name of the wiki statistics template
     *
     * @return string
     */
    function get_wiki_statistics_reporting_template_name();

    /**
     * Retrieve the publication context for this complex display
     */
    function get_publication();
}
?>