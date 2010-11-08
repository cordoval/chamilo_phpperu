<?php

namespace application\profiler;

use common\libraries\Request;
use common\libraries\Display;
use repository\ContentObjectForm;
use common\libraries\Application;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\ContentObjectDisplay;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: editor.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
class ProfilerManagerEditorComponent extends ProfilerManager
{

    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();


        $id = Request :: get(ProfilerManager :: PARAM_PROFILE_ID);

        if ($id)
        {
            $profile_publication = $this->retrieve_profile_publication($id);

            if (!$user->is_platform_admin() && $user->get_id() != $profile_publication->get_publisher())
            {
                //Display :: not_allowed();
                //  exit();
            }

            $content_object = $profile_publication->get_publication_object();

            if (!ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT, $id, ProfilerRights::TYPE_PUBLICATION))
            {
                Display :: not_allowed();
                exit();
            }

            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_EDIT_PUBLICATION, ProfilerManager :: PARAM_PROFILE_ID => $profile_publication->get_id())));
            if ($form->validate())
            {
                $success = $form->update_content_object();
                if ($form->is_version())
                {
                    $profile_publication->set_content_object($content_object->get_latest_version());
                    $profile_publication->update();
                }

                $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, 0);
                $cats = ProfilerDataManager :: get_instance()->retrieve_categories($condition);

                if ($cats->size() > 0)
                {
                    $publication_form = new ProfilePublicationForm(ProfilePublicationForm :: TYPE_SINGLE, $profile_publication->get_publication_object(), $this->get_user(), $this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_EDIT_PUBLICATION, ProfilerManager :: PARAM_PROFILE_ID => $profile_publication->get_id(), 'validated' => '1')));
                    $publication_form->set_profile_publication($profile_publication);

                    if ($publication_form->validate())
                    {
                        $success = $publication_form->update_content_object_publication();
                        $message = $success ? Translation :: get ('ObjectUpdated', array('OBJECT' => Translation :: get('ProfilePublication') , Utilities :: COMMON_LIBRARIES)) : Translation :: get ('ObjectNotUpdated', array('OBJECT' => Translation :: get('ProfilePublication') , Utilities :: COMMON_LIBRARIES));
                        $this->redirect('url', $message, ($success ? false : true), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
                    }
                    else
                    {
                        $this->display_header();
                        echo ContentObjectDisplay :: factory($profile_publication->get_publication_object())->get_full_html();
                        $publication_form->display();
                        $this->display_footer();
                        exit();
                    }
                }
                else
                {
                     $message = $success ? Translation :: get ('ObjectUpdated', array('OBJECT' => Translation :: get('ProfilePublication') , Utilities :: COMMON_LIBRARIES)) : Translation :: get ('ObjectNotUpdated', array('OBJECT' => Translation :: get('ProfilePublication') , Utilities :: COMMON_LIBRARIES));
                     $this->redirect($message , ($success ? false : true), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
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
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null , Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('profiler_editor');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('ProfilerManagerBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_VIEW_PUBLICATION, ProfilerManager :: PARAM_PROFILE_ID => Request :: get(self :: PARAM_PROFILE_ID))), Translation :: get('ProfilerManagerViewerComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PROFILE_ID);
    }

}

?>