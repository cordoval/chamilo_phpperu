<?php
/**
 * $Id: buddy_list_category_editor.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListCategoryEditorComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(UserManager :: PARAM_BUDDYLIST_CATEGORY);
        if ($id)
        {
            $category = UserDataManager :: get_instance()->retrieve_buddy_list_categories(new EqualityCondition('id', $id))->next_result();
            $form = new BuddyListCategoryForm(BuddyListCategoryForm :: TYPE_EDIT, $this->get_url(array(UserManager :: PARAM_BUDDYLIST_CATEGORY => $id)), $category, $this->get_user(), $this);
            
            if ($form->validate())
            {
                $success = $form->update_category();
                $this->redirect(Translation :: get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
            }
            else
            {
                $trail = BreadcrumbTrail :: get_instance();
                $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('MyAccount')));
                $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_BUDDYLIST_CATEGORY => $id)), Translation :: get('UpdateBuddyListCategory')));
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
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCategorySelected')));
        }
    }
}
?>