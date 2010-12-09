<?php
namespace application\phrases;

use common\libraries\Request;
use repository\ContentObjectForm;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */
require_once dirname(__FILE__) . '/../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/phrases_publication_form.class.php';

/**
 * Component to edit an existing phrases_publication object
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManagerUpdaterComponent extends PhrasesManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);

        if (isset($publication))
        {
            $phrases_publication = $this->retrieve_phrases_publication($publication);

            if (! $phrases_publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed(null, false);
            }

            $content_object = $phrases_publication->get_publication_object();

            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(
                    Application :: PARAM_ACTION => PhrasesManager :: ACTION_EDIT_PHRASES_PUBLICATION,
                    PhrasesManager :: PARAM_PHRASES_PUBLICATION => $publication)));

            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }

                if ($form->is_version())
                {
                    $phrases_publication->set_content_object($content_object->get_latest_version());
                    $phrases_publication->update();
                }

                $publication_form = new PhrasesPublicationForm(PhrasesPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(
                        PhrasesManager :: PARAM_PHRASES_PUBLICATION => $publication,
                        'validated' => 1)));
                $publication_form->set_publication($phrases_publication);

                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $object = Translation :: get('ContentObject', null, 'repository');
                    $message = $success ? Translation :: get('ObjectUpdated', array(
                            'OBJECT' => $object), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array(
                            'OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

                    $this->redirect($message, ! $success, array(
                            Application :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS), array(
                            PhrasesManager :: PARAM_PHRASES_PUBLICATION));
                }
                else
                {
                    $this->display_header();
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->redirect(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES), true, array(
                    PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_updater');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }
}
?>