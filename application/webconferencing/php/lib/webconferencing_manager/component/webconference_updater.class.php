<?php
/**
 * $Id: webconference_updater.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component
 */
require_once dirname(__FILE__) . '/../webconferencing_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/webconference_form.class.php';

/**
 * Component to edit an existing webconference object
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferenceUpdaterComponent extends WebconferencingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES)), Translation :: get('BrowseWebconferences')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateWebconference')));
        
        $webconference = $this->retrieve_webconference(Request :: get(WebconferencingManager :: PARAM_WEBCONFERENCE));
        $form = new WebconferenceForm(WebconferenceForm :: TYPE_EDIT, $webconference, $this->get_url(array(WebconferencingManager :: PARAM_WEBCONFERENCE => $webconference->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_webconference();
            $this->redirect($success ? Translation :: get('WebconferenceUpdated') : Translation :: get('WebconferenceNotUpdated'), ! $success, array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>