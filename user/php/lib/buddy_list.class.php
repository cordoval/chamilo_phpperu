<?php
/**
 * $Id: buddy_list.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */


/**
==============================================================================
 *	This is a buddy list for a user
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class BuddyList
{
    // The user to which the buddy list belongs
    private $user;
    
    // The parent object
    private $parent;

    function BuddyList($user, $parent)
    {
        $this->user = $user;
        $this->parent = $parent;
    }

    /**
     * Creates the buddy list in html code
     * @return String the html code of the buddy list
     */
    function to_html()
    {
        $categories = $this->retrieve_buddy_list_categories();
        $buddies = $this->retrieve_buddies();
        $requests = $this->retrieve_requests();
        $total = count($buddies, COUNT_RECURSIVE) + count($requests) - $categories->size();
        
        $html = array();
        $html[] = $this->display_buddy_list_header($total);
        
        while ($category = $categories->next_result())
        {
            $html[] = $this->display_buddy_list_category($category, $buddies[$category->get_id()]);
        }
        
        $category = new BuddyListCategory();
        $category->set_id(0);
        $category->set_title(Translation :: get('OtherBuddies'));
        $category->set_user_id($this->user->get_id());
        
        $html[] = $this->display_buddy_list_category($category, $buddies[$category->get_id()], false, true);
        
        $category = new BuddyListCategory();
        $category->set_title(Translation :: get('Requests'));
        $html[] = $this->display_buddy_list_category($category, $requests, true);
        
        $html[] = $this->display_buddy_list_footer();
        
        return implode("\n", $html);
    }

    /**
     * Displays the header of the buddy list
     * @return html code
     */
    function display_buddy_list_header($size)
    {
        $html = array();
        
        $html[] = '<div class="buddylist">';
        
        $html[] = '<div class="buddylist_header">';
        $html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_mini_user.png" alt="user" />';
        $html[] = '<span class="title">' . Translation :: get('MyBuddies') . ' (<span class="totalusers">' . $size . '</span>)</span>';
        $html[] = '</div>';
        
        $html[] = '<div class="buddylist_content">';
        $html[] = '<ul class="category_list">';
        
        return implode("\n", $html);
    }

    /**
     * Displays a category of the buddylist
     * @return html code
     */
    function display_buddy_list_category($category, $buddies, $is_request = false, $is_normal = false)
    {
        $html = array();
        
        $class = $is_request ? 'category_list_item_static' : 'category_list_item';
        
        if (count($buddies) == 0)
            $style = 'style="visibility:hidden;" ';
        
        $html[] = '<li id="' . $category->get_id() . '" class="' . $class . '"><img class="category_toggle" ' . $style . 'src="' . Theme :: get_common_image_path() . 'treemenu/bullet_toggle_minus.png" />';
        $html[] = '<div class="buddy_list_item_text">';
        $html[] = '<span class="title">' . $category->get_title() . ' (<span class="userscount">' . count($buddies) . '</span>)</span></div>';
        
        $html[] = '<div class="buddy_list_item_actions" style="position: relative;">';
        
        if (! $is_request && ! $is_normal)
        {
            $toolbar = new Toolbar();

            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->parent->get_update_buddylist_category_url($category->get_id()),
				 	ToolbarItem :: DISPLAY_ICON
			));
	       	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->parent->get_delete_buddylist_category_url($category->get_id()),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true,
				 	'delete_category'
			));
            
            //$toolbar_data[] = array('href' => , 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'class' => 'delete_category', 'id' => $category->get_id(), 'confirm' => true);

            $html[] = $toolbar->as_html();
        }
        
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        
        if (count($buddies) > 0)
        {
            $html[] = '<ul class="buddy_list">';
            foreach ($buddies as $buddy)
            {
                $html[] = $this->display_buddy($buddy, $is_request);
            }
            $html[] = '</ul>';
        }
        
        $html[] = '</li>';
        
        return implode("\n", $html);
    }

    /**
     * Displays a single buddy
     * @return html code
     */
    function display_buddy($buddy, $is_request = false)
    {
        $html = array();
        
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($buddy->get_buddy_id());
        
        $class = $is_request ? 'buddy_list_item_static' : 'buddy_list_item';
        
        $html[] = '<li id="' . $buddy->get_buddy_id() . '" class="' . $class . '"><img class="category_toggle" src="' . Theme :: get_common_image_path() . 'treemenu/user.png" />';
        $html[] = '<div class="buddy_list_item_text">' . $user->get_fullname() . '<span class="info">';
        
        if (! $is_request)
        {
            switch ($buddy->get_status())
            {
                case BuddyListItem :: STATUS_REQUESTED :
                    $html[] = '(' . Translation :: get('Requested') . ')';
                    break;
                case BuddyListItem :: STATUS_REJECTED :
                    $html[] = '(' . Translation :: get('Rejected') . ')';
                    break;
            }
        }
        
        $html[] = '</span></div>';
        
        $html[] = '<div class="buddy_list_item_actions">';
        
        
        $toolbar = new Toolbar();

        if (! $is_request)
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('DeleteUser'),
        			Theme :: get_common_image_path().'action_unsubscribe.png', 
					$this->parent->get_delete_buddylist_item_url($buddy->get_buddy_id()),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true,
				 	'delete_item'
				 	
			));
            //$toolbar_data[] = array('href' => , 'label' => Translation :: get('DeleteUser'), 'img' => Theme :: get_common_image_path() . 'action_unsubscribe.png', 'class' => 'delete_item', 'id' => $buddy->get_buddy_id(), 'confirm' => true);
        }
        else
        {
           	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Accept'),
        			Theme :: get_common_image_path().'action_setting_true.png', 
					$this->parent->get_change_buddylist_item_status_url($buddy->get_user_id(), BuddyListItem :: STATUS_NORMAL),
				 	ToolbarItem :: DISPLAY_ICON,
				 	false,
				 	'accept_buddy'
			));
           	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Reject'),
        			Theme :: get_common_image_path().'action_setting_false.png', 
					$this->parent->get_change_buddylist_item_status_url($buddy->get_user_id(), BuddyListItem :: STATUS_REJECTED),
				 	ToolbarItem :: DISPLAY_ICON,
				 	false,
				 	'reject_buddy'
			));
            //$toolbar_data[] = array('href' => , 'label' => Translation :: get('Accept'), 'img' => Theme :: get_common_image_path() . 'action_setting_true.png', 'class' => 'accept_buddy', 'id' => $buddy->get_user_id());
            //$toolbar_data[] = array('href' => $this->parent->get_change_buddylist_item_status_url($buddy->get_user_id(), BuddyListItem :: STATUS_REJECTED), 'label' => Translation :: get('Reject'), 'img' => Theme :: get_common_image_path() . 'action_setting_false.png', 'class' => 'reject_buddy', 'id' => $buddy->get_user_id());
        }
        
        $html[] = $toolbar->as_html();
        $html[] = '</div>';
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</li>';
        
        return implode("\n", $html);
    }

    /**
     * Displays the footer of the buddy list
     * @return html code
     */
    function display_buddy_list_footer()
    {
        $html = array();
        
        $html[] = '</ul></div>';
        $html[] = '<div class="buddylist_footer">';
        
        $html[] = '<div class="buddylist_footer_actions">';
        $toolbar = new Toolbar();
        
       	$toolbar->add_item(new ToolbarItem(
        	Translation :: get('AddCategory'),
        	Theme :: get_common_image_path().'action_add.png', 
			$this->parent->get_create_buddylist_category_url(),
			ToolbarItem :: DISPLAY_ICON
		));
       	
		$toolbar->add_item(new ToolbarItem(
        	Translation :: get('AddUser'),
        	Theme :: get_common_image_path().'action_subscribe.png', 
			$this->parent->get_create_buddylist_item_url(),
			ToolbarItem :: DISPLAY_ICON
		));        
        
        $html[] = $toolbar->as_html();
        $html[] = '</div></div>';
        
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/buddy_list.js' . '"></script>';
        
        return implode("\n", $html);
    }

    /**
     * Retrieves the categories for the buddy list
     * @return ResultSet of categories
     */
    function retrieve_buddy_list_categories()
    {
        $condition = new EqualityCondition(BuddyListCategory :: PROPERTY_USER_ID, $this->user->get_id());
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_buddy_list_categories($condition, null, null, new ObjectTableOrder(BuddyListCategory :: PROPERTY_TITLE));
    }

    /**
     * Retrieves the buddies for the buddy list
     * @return Array of buddies with category as index
     */
    function retrieve_buddies()
    {
        $condition = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $this->user->get_id());
        $udm = UserDataManager :: get_instance();
        $items = $udm->retrieve_buddy_list_items($condition, null, null, new ObjectTableOrder(BuddyListItem :: PROPERTY_CATEGORY_ID));
        
        while ($item = $items->next_result())
        {
            $buddies[$item->get_category_id()][] = $item;
        }
        
        return $buddies;
    }

    /**
     * Retrieves the requests from other buddies
     * @return Resultset of requests
     */
    function retrieve_requests()
    {
        $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $this->user->get_id());
        $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_STATUS, BuddyListItem :: STATUS_REQUESTED);
        $condition = new AndCondition($conditions);
        
        $udm = UserDataManager :: get_instance();
        $items = $udm->retrieve_buddy_list_items($condition);
        
        while ($item = $items->next_result())
        {
            $requests[] = $item;
        }
        
        return $requests;
    }
}

?>