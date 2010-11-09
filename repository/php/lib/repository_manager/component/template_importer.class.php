<?php
namespace repository;
use common\libraries\AdministrationComponent;
use user\User;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;
/**
 * $Id: template_importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerTemplateImporterComponent extends RepositoryManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $extra_params = array();

        $user = new User();
        $user->set_id(0);

        $import_form = new ContentObjectImportForm('import', 'post', $this->get_url($extra_params), 0, $user, null, false);

        if ($import_form->validate())
        {
            $content_object = $import_form->import_content_object();

            if ($content_object === false)
            {
                $message = Translation :: get('ObjectNotImported', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectImported', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, ! isset($content_object), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES));
        }
        else
        {
            $this->display_header(null, false, false);
            $import_form->display();
            $this->display_footer();
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('template_importer');
    }
}
?>