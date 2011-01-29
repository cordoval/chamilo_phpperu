<?php


namespace application\handbook;
use common\libraries\Request;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use user\UserDataManager;
use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\content_object\handbook_item\HandbookItem;
use repository\ComplexContentObjectItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\content_object\link\Link;
use common\libraries\EqualityCondition;
use repository\content_object\youtube\Youtube;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\handbook\Handbook;
use common\extensions\repo_viewer\RepoViewerInterface;
use repository\content_object\document\Document;
use common\libraries\Filesystem;
use common\libraries\Path;

require_once dirname(__FILE__) . '/../../export/cpo/cpo_export.class.php';
/**
 * Component to create a new handbook_publication object
 */
class HandbookManagerHandbookExporterComponent extends HandbookManager
{
     /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);

        $rdm = RepositoryDataManager::get_instance();
        $handbook = $rdm->retrieve_content_object($handbook_id);

        $objects = array($handbook);
        $exporter = new HandbookCpoExport($objects);

        $path = $exporter->export_content_object();

        Filesystem :: copy_file($path, Path :: get(SYS_TEMP_PATH) . $this->get_user_id() . '/content_objects.cpo', true);
        $webpath = Path :: get(WEB_TEMP_PATH) . $this->get_user_id() . '/content_objects.cpo';

        $this->display_header();
        $this->display_message('<a href="' . $webpath . '">' . Translation :: get('Download', null, Utilities :: COMMON_LIBRARIES) . '</a>');
        $this->display_footer();

    }

   





}
?>