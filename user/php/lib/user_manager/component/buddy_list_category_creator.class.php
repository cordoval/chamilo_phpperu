<?php
namespace user;

use common\libraries\Application;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: buddy_list_category_creator.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListCategoryCreatorComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        $category = new BuddyListCategory();

        $form = new BuddyListCategoryForm(BuddyListCategoryForm :: TYPE_CREATE, $this->get_url(), $category, $user, $this);

        if ($form->validate())
        {
            $success = $form->create_category();
            $this->redirect(Translation :: get($success ? 'ObjectsCreated' : 'ObjectsNotCreated', array(
                    'OBJECT' => Translation :: get('BuddyListCategory')), Utilities :: COMMON_LIBRARIES), ($success ? false : true), array(
                    Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(
                    Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('MyAccount')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddObjects', array(
                    'OBJECT' => Translation :: get('BuddyListCategory')), Utilities :: COMMON_LIBRARIES)));
            $trail->add_help('user general');

            $this->display_header();

            echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
            $actions = array('account', 'buddy_view');
            foreach ($actions as $action)
            {
                echo '<li><a';
                if ($action == 'buddy_view')
                {
                    echo ' class="current"';
                }
                echo ' href="' . $this->get_url(array(UserManager :: PARAM_ACTION => $action)) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title')) . '</a></li>';
            }
            echo '</ul><div class="tabbed-pane-content"><br />';

            $form->display();
            echo '</div></div>';

            $this->display_footer();
        }
    }
}
?>