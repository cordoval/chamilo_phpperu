<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\Export;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerResultsExporterComponent extends PhrasesManager
{

    function run()
    {
        if (Request :: get('tid'))
        {
            $id = Request :: get('tid');
            $url = $this->get_results_exporter_url($id);
            $type = 'user_phrases';
            $export_form = new PhrasesResultsExportForm($url);
        }
        if ($export_form->validate())
        {
            $values = $export_form->exportValues();
            $filetype = $values['filetype'];
            $this->export($type, $id, $filetype);
        }
        else
        {
            $this->display_header(null, true);
            echo $export_form->toHtml();
            $this->display_footer();
        }
    }

    function export($type, $id, $filetype)
    {

        $results_exporter = ResultsExport :: factory($filetype);

        $data = $results_exporter->export_results($type, $id);
        $exporter = Export :: factory($filetype, $data);
        $exporter->set_filename('export_' . $type . $id);
        $exporter->send_to_browser();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_results_exporter');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array('tid');
    }

}
?>