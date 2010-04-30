<?php
/**
 * $Id: buddy_list_item_creator.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListItemCreatorComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        $item = new BuddyListItem();
        $form = new BuddyListItemForm($user, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_items();
            $this->redirect(Translation :: get($success ? 'BuddyListItemsCreated' : 'BuddyListItemsNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('MyAccount')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddBuddies')));
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