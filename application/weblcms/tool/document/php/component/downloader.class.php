<?php
namespace application\weblcms\tool\document;

use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use common\libraries\Display;
use common\libraries\Request;

/**
 * $Id: document_downloader.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
require_once dirname(__FILE__) . '/../document_tool.class.php';

class DocumentToolDownloaderComponent extends DocumentTool
{
    private $action_bar;

    function run()
    {

        $dm = WeblcmsDataManager :: get_instance();
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $publication = $dm->retrieve_content_object_publication($publication_id);
        $document = $publication->get_content_object();
        $document->send_as_download();
        return '';
    }

}

?>