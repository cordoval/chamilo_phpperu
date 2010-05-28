<?php
/**
 * $Id: wiki_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */

require_once dirname(__FILE__) . '/../blocks/weblcms_wiki_most_visited_page_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_wiki_most_edited_page_reporting_block.class.php';

class WikiReportingTemplate extends ReportingTemplate
{

    function WikiReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->set_template_parameters();
        $this->add_reporting_block(new WeblcmsWikiMostVisitedPageReportingBlock($this));
        $this->add_reporting_block(new WeblcmsWikiMostEditedPageReportingBlock($this));
    }

    function set_template_parameters()
    {
        $publication_id = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);
        $this->set_parameter(WeblcmsManager :: PARAM_PUBLICATION, $publication_id);
    }

    function display_context()
    {

    }

    function get_application()
    {
        return WeblcmsManager :: APPLICATION_NAME;
    }
}
?>