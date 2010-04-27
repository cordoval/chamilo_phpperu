<?php
/**
 * $Id: editor.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_manager_component.class.php';
require_once dirname(__FILE__) . '/../../profile_publication_form.class.php';

class ProfilerManagerEditorComponent extends ProfilerManagerComponent
{
    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        
        $user = $this->get_user();
        
        $id = Request :: get(ProfilerManager :: PARAM_PROFILE_ID);
        
        if ($id)
        {
            $profile_publication = $this->retrieve_profile_publication($id);

        	if (!$user->is_platform_admin() && $user->get_id() != $profile_publication->get_publisher())
	        {
    	        Display :: not_allowed();
        	    exit();
        	}
            
            $content_object = $profile_publication->get_publication_object();
            
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_VIEW_PUBLICATION, ProfilerManager :: PARAM_PROFILE_ID => $id)), $content_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(ProfilerManager :: PARAM_PROFILE_ID => $id)), Translation :: get('Edit')));
            $trail->add_help('profiler general');
            
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
                        $this->redirect('url', Translation :: get(($success ? 'ProfilePublicationUpdated' : 'ProfilePublicationNotUpdated')), ($success ? false : true), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
                    }
                    else
                    {
                        $this->display_header($trail);
                        echo ContentObjectDisplay :: factory($profile_publication->get_publication_object())->get_full_html();
                        $publication_form->display();
                        $this->display_footer();
                        exit();
                    }
                }
                else
                {
                    $this->redirect(Translation :: get(($success ? 'ProfilePublicationUpdated' : 'ProfilePublicationNotUpdated')), ($success ? false : true), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
                }
            }
            else
            {
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCalendarEventPublicationSelected')));
        }
    }
}
?>