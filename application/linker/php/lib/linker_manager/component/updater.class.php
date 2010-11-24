<?php
namespace application\linker;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Request;
/**
 * $Id: updater.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.linker_manager.component
 */

class LinkerManagerUpdaterComponent extends LinkerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS)), Translation :: get('Links')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLink', null, Utilities::COMMON_LIBRARIES)));
        
        $link = $this->retrieve_link(Request :: get(LinkerManager :: PARAM_LINK_ID));
        $form = new LinkForm(LinkForm :: TYPE_EDIT, $link, $this->get_url(array(LinkerManager :: PARAM_LINK_ID => $link->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_link();
            $this->redirect($success ? Translation :: get('LinkUpdated', null, Utilities::COMMON_LIBRARIES) : Translation :: get('LinkNotUpdated', null, Utilities::COMMON_LIBRARIES), ! $success, array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS));
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