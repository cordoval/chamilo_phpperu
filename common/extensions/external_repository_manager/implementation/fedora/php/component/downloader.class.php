<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Session;
use common\libraries\Filesystem;

use Exception;

include_once Path :: get_repository_path() . '/lib/import/content_object_import.class.php';

/**
 * Download an object's datastream from Fedora to the user desktop.
 * First download the datastream localy before sending it to the browser.
 * Needed to pass through Fedora's security using Chamilo's credentials.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerDownloaderComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if ($api = $this->create_api_component())
        {
            return $api->run();
        }

        $pid = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_ID);
        $dsID = Request :: get(self :: PARAM_DATASTREAM_ID);
        return $this->download_datastream($pid, $dsID);
    }

    function download_datastream($pid, $dsID = false)
    {
        $ds = $this->get_datastream($pid, $dsID);
        $dsID = $ds->get_dsID();
        $mime = $ds->get_mime_type();
        $ext = $ds->get_extention();
        $ext = $ext ? '.' . $ext : '';
        $title = '"' . $ds->get_title() . $ext . '"';

        $content = $this->retrieve_datastream_content($pid, $dsID);
        $path = Path :: get_temp_path() . '/f' . sha1(Session :: get_user_id() . time());

        Filesystem :: write_to_file($path, $content);
        try
        {
            Filesystem :: file_send_for_download($path, true, $title, $mime);
        }
        catch (Exception $e)
        {
            Filesystem :: remove($path);
            throw $e;
        }

        Filesystem :: remove($path);
    }

    protected function get_datastream($pid, $dsID = false)
    {
        $connector = $this->get_external_repository_manager_connector();
        $dss = $connector->retrieve_datastreams($pid);

        if ($dsID)
        {
            foreach ($dss as $ds)
            {
                if ($ds->get_dsID() == $dsID)
                {
                    return $ds;
                }
            }
        }
        foreach ($dss as $ds)
        {
            if (! $ds->is_system_datastream())
            {
                return $ds;
            }
        }
        return false;
    }

    protected function retrieve_datastream_content($pid, $dsID)
    {
        $connector = $this->get_external_repository_manager_connector();
        return $connector->retrieve_datastream_content($pid, $dsID);
    }

}

?>