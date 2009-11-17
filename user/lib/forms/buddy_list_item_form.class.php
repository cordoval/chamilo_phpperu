<?php
/**
 * $Id: buddy_list_item_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class BuddyListItemForm extends FormValidator
{
    private $parent;
    private $user;

    function BuddyListItemForm($user, $action)
    {
        parent :: __construct('buddy_list_item_form', 'post', $action);
        
        $this->user = $user;
        $this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        $cats[0] = Translation :: get('OtherBuddies');
        
        $condition = new EqualityCondition(BuddyListCategory :: PROPERTY_USER_ID, $this->user->get_id());
        $categories = UserDataManager :: get_instance()->retrieve_buddy_list_categories($condition);
        while ($cat = $categories->next_result())
            $cats[$cat->get_id()] = $cat->get_title();
        
        $this->addElement('select', 'category', Translation :: get('Category'), $cats);
        
        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        ;
        
        $locale = array();
        $locale['Display'] = Translation :: get('AddUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', 'users', null, $url, $locale, array(), array('load_elements' => true));
        $elem->excludeElements(array($this->user->get_id()));
        
        $exclude = array();
        
        $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $this->user->get_id());
        $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $this->user->get_id());
        $condition = new OrCondition($conditions);
        
        $buddies = UserDataManager :: get_instance()->retrieve_buddy_list_items($condition);
        
        $exclude[] = $this->user->get_id();
        
        while ($buddy = $buddies->next_result())
        {
            if ($buddy->get_buddy_id() != $this->user->get_id())
                $exclude[] = $buddy->get_buddy_id();
            else
                $exclude[] = $buddy->get_user_id();
        }
        
        $elem->excludeElements($exclude);
        
        // Submit button
        //$this->addElement('submit', 'user_settings', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_items()
    {
        $user = $this->user;
        $values = $this->exportValues();
        
        $succes = true;
        
        $users = $values['users'];
        foreach ($users as $us)
        {
            $buddy = new BuddyListItem();
            $buddy->set_user_id($user->get_id());
            $buddy->set_buddy_id($us);
            $buddy->set_status(BuddyListItem :: STATUS_REQUESTED);
            $buddy->set_category_id($values['category']);
            $succes &= $buddy->create();
        }
        
        return $succes;
    }

}
?>