<?php
namespace application\phrases;

use repository\RepositoryDataManager;

use common\libraries\Path;
use common\libraries\Utilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

abstract class ResultsExport
{
    const FILETYPE_CSV = 'csv';
    const FILETYPE_PDF = 'pdf';
    const FILETYPE_XML = 'xml';

    protected $rdm;

    function __construct()
    {
        $this->rdm = RepositoryDataManager :: get_instance();
    }

    function export_results($type, $id)
    {
        if ($type == 'phrases')
        {
            $data = $this->export_publication_id($id);
        }
        else
        {
            $data = $this->export_user_phrases_id($id);
        }
        return $data;
    }

    abstract function export_publication_id($id);

    abstract function export_user_phrases_id($id);

    function factory($type)
    {
        $file = dirname(__FILE__) . '/result_exporters/export_' . $type . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ResultsExporterFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';

            Display :: header();
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }

        require_once $file;

        $class = __NAMESPACE__ . '\\Results' . Utilities :: underscores_to_camelcase($type) . 'Export';
        return new $class();
    }
}
?>