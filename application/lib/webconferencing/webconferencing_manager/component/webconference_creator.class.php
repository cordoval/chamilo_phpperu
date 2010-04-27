<?php
/**
 * $Id: webconference_creator.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component
 */
require_once dirname(__FILE__) . '/../webconferencing_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/webconference_form.class.php';

/**
 * Component to create a new webconference object
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferenceCreatorComponent extends WebconferencingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES)), Translation :: get('BrowseWebconferences')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateWebconference')));
        
        $webconference = new Webconference();
        $webconference->set_user_id($this->get_user_id());
        $form = new WebconferenceForm(WebconferenceForm :: TYPE_CREATE, $webconference, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_webconference();
            $this->redirect($success ? Translation :: get('WebconferenceCreated') : Translation :: get('WebconferenceNotCreated'), ! $success, array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES));
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