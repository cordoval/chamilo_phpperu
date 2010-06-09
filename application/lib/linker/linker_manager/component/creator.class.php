<?php
/**
 * $Id: creator.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.linker_manager.component
 */
require_once dirname(__FILE__) . '/../linker_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/link_form.class.php';

class LinkerManagerCreatorComponent extends LinkerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS)), Translation :: get('Links')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLink')));
        
        $link = new Link();
        $form = new LinkForm(LinkForm :: TYPE_CREATE, $link, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_link();
            $this->redirect($success ? Translation :: get('LinkCreated') : Translation :: get('LinkNotCreated'), ! $success, array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS));
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