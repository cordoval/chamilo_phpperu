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
use repository\ContentObjectImportForm;
use common\libraries\Application;
use repository\RepositoryManager;



require_once dirname(__FILE__) . '/../../import/handbook_import_form.class.php';

/**
 * Component to create a new handbook_publication object
 */
class HandbookManagerHandbookImporterComponent extends HandbookManager
{
     /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $import_form = new HandbookImportForm('import', 'post', $this->get_url(), $this->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID), $this->get_user());

        if ($import_form->validate())
        {
//            $success = $import_form->import_content_object();
            $success = $import_form->import_metadata();
             $success &= $import_form->import_content_object();

            $messages = array();
            $errors = array();
            if ($success)
            {
                $messages[] = Translation :: get('ContentObjectImported');
            }
            else
            {
                $errors[] = Translation :: get('ContentObjectNotImported');
            }

            $messages = array_merge($messages, $import_form->get_messages());
            $warnings = $import_form->get_warnings();
            $errors = array_merge($errors, $import_form->get_errors());
            $log = $import_form->get_log();
//            $parameters = array(Application::PARAM_ACTION => RepositoryManager::ACTION_BROWSE_CONTENT_OBJECTS);
//            $parameters[RepositoryManager::PARAM_MESSAGE] = implode('<br/>', $messages);
//            $parameters[RepositoryManager::PARAM_WARNING_MESSAGE] = implode('<br/>', $warnings);
//            $parameters[RepositoryManager::PARAM_ERROR_MESSAGE] = implode('<br/>', $errors);
//
//            $this->simple_redirect($parameters);
            $this->display_header($trail, false, true);
                echo implode('/n', $messages);
                echo '</br>';
                echo implode('/n', $warnings);
                echo '</br>';
                echo implode('/n', $errors);
                echo '</br>';
                foreach ($log as $l)
                {
                    echo $l;
                    echo '</br>';
                }
                $this->display_footer();
                
        }
        else
        {
            $this->display_header($trail, false, true);
            $import_form->display();
            $this->display_footer();
        }

    }

   





}
?>