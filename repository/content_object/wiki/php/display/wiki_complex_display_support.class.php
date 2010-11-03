<?php
namespace repository\content_object\wiki;
/**
 * A class implements the <code>WikiComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a WikiComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface WikiComplexDisplaySupport
{
    function get_page_statistics_reporting_template_name();

    function get_statistics_reporting_template_name();
}
?>