<?php
namespace repository\content_object\wiki;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\ComplexDisplayComponent;
use repository\ComplexDisplay;
use repository\RepositoryDataManager;

/**
 * $Id: reporting_template_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class WikiDisplayReportingTemplateViewerComponent extends WikiDisplay
{

    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: REPORTING_TEMPLATE_VIEWER_COMPONENT, $this);
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_PAGE_STATISTICS :
                $browser->set_template_name($this->get_parent()->get_wiki_page_statistics_reporting_template_name());
                break;
            case self :: ACTION_STATISTICS :
                $browser->set_template_name($this->get_parent()->get_wiki_statistics_reporting_template_name());
                break;
            case self :: ACTION_ACCESS_DETAILS :
                $browser->set_template_name('publication_detail_reporting_template');
                break;
        }

        $browser->run();
    }

    function display_header()
    {
        $complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_wiki_page_id);
        $wiki_page = $complex_wiki_page->get_ref_object();

        parent :: display_header($complex_wiki_page);

        $html = array();
        $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('Statistics') . ' ' . $wiki_page->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From', null , Utilities :: COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';
        echo implode("\n", $html);
    }
}
?>