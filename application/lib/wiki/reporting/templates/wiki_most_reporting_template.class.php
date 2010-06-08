<?php
/**
 * $Id: wiki_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */

require_once dirname(__FILE__) . '/../blocks/wiki_most_visited_page_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/wiki_most_edited_page_reporting_block.class.php';

class WikiMostReportingTemplate extends ReportingTemplate
{

    function WikiMostReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->set_template_parameters();
        $this->add_reporting_block(new WikiMostVisitedPageReportingBlock($this));
        $this->add_reporting_block(new WikiMostEditedPageReportingBlock($this));
    }

    function set_template_parameters()
    {
        $publication_id = Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION);
        $this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, $publication_id);
    }

    function display_context()
    {

    }

    function get_application()
    {
        return WikiManager :: APPLICATION_NAME;
    }
}
?>