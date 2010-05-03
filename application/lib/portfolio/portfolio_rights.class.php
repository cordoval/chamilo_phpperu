<?php

/**
 * class to handle the different rights in the portfolio application
 *
 * @author nblocry
 */
class PortfolioRights {

    const VIEW_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const VIEW_FEEDBACK_RIGHT = '3';
    const GIVE_FEEDBACK_RIGHT = '4';
    const SET_PERMISSIONS_RIGHT = '5';

    const TYPE_PORTFOLIO_FOLDER = 'portfolio';
    const TYPE_PORTFOLIO_SUB_FOLDER = 'subportfolio';
    const TYPE_PORTFOLIO_ITEM = 'portfolio_item';

    
    const RADIO_OPTION_SET_SPECIFIC = 'set';
    const RADIO_OPTION_DEFAULT = 'SystemDefaults';
    const RADIO_OPTION_INHERIT = 'inheritFromParent';
    const RADIO_OPTION_ANONYMOUS = 'AnonymousUsers';
    const RADIO_OPTION_ALLUSERS =  'SystemUsers';
    const RADIO_OPTION_ME = 'OnlyMe';
    const RADIO_OPTION_GROUPS_USERS = '1';

    const PORTFOLIO_TREE_TYPE_NAME = 'portfolio_tree';

    const GROUP_RIGHTS = 'group';
    const USER_RIGHTS = 'user';
    const SESSION_RIGHTS = 'portfolio_rights';


    /**
     * create a portfolio_tree for a specific user. The portfolio-tree is a tree of locations that represent
     * the structure of the portfolio and can be used to have items inherit rights
     *  @param $user_id: id of the user that owns the portfolio to identify it (tree-identifier)
     */
    static function create_portfolio_root($user_id)
    {
        return RightsUtilities :: create_subtree_root_location(PortfolioManager::APPLICATION_NAME, $user_id, self::PORTFOLIO_TREE_TYPE_NAME, true);
    }
    /**
     * get the root of a specific user's portfolio-tree.
     * @param $user_id: id of the user the portfolio belongs to, this is the tree_identifier
     * @return returns the id of the root location or "false" when no root location is found
     */
    static function get_portfolio_root_id($user_id)
    {
        return RightsUtilities::get_root_id(PortfolioManager::APPLICATION_NAME, self::PORTFOLIO_TREE_TYPE_NAME, $user_id);
    }

     /**
     * sets the a right for a specific item
     * @param location: location of the item
     * @param rightType: right to be given 
     * @param chosenOption : option chosen for this right
     * @param groups : array of id's of specific groups who get this right
     * @param users : array of id's of specific users who get this right
     * @return success (true or false)
     */
    static function set_rights($location, $rightType, $groups, $users, $chosenoption = null)
    {
        $success = true;
        $location_id=$location->get_id();
        //1. delete current user-right-locations and group-right-locations
        $rdm = RightsDataManager::get_instance();
        $condition_u = array();
        $condition_u[] = new EqualityCondition(UserRightLocation::PROPERTY_LOCATION_ID, $location_id);
        $condition_u[] = new EqualityCondition(UserRightLocation::PROPERTY_RIGHT_ID, $rightType);
        $condition_user = new AndCondition($condition_u);
        $success &= $rdm->delete(UserRightLocation :: get_table_name(), $condition_user);

        $condition_g = array();
        $condition_g[] = new EqualityCondition(GroupRightLocation::PROPERTY_LOCATION_ID, $location_id);
        $condition_g[] = new EqualityCondition(GroupRightLocation::PROPERTY_RIGHT_ID, $rightType);
        $condition_group = new AndCondition($condition_g);
        $success &= $rdm->delete(GroupRightLocation::get_table_name(), $condition_group);

        

       //2. add required rights
       if ($chosenoption == self::RADIO_OPTION_ALLUSERS)
        {
            //inherit = false, no user-rights, no group-rights
        }
        elseif ($chosenoption == self::RADIO_OPTION_ANONYMOUS)
        {
            //inherit = false, only user-right for anonymous, no group-rights
            $success &= RightsUtilities::set_user_right_location_value($rightType, 1, $location_id, 1);
        }
        elseif ($chosenoption == self::RADIO_OPTION_ME)
        {
            //inherit = false, user-rights for owner, no group-rights
           
            $success &= RightsUtilities::set_user_right_location_value($rightType, $location->get_tree_identifier(), $location_id, 1);
        }
        else
        {
            //inherit = false, specific user-rights, specific group-rights      
            if((is_array($groups)) && (count($groups) > 0))
            {
                foreach ($groups as $group)
                {
                   $success &= RightsUtilities::set_group_right_location_value($rightType, $group, $location_id, 1);
                }
            }
             if((is_array($users)) && (count($users) > 0))
            {
                foreach ($users as $user_id)
                {
                   $success &= RightsUtilities::set_user_right_location_value($rightType, $user_id, $location_id, 1);
                }
            }
        }
        return $success;
    }

    /**
     * this function returns all the rights set for a publication
     * @param $location = object for wich to return the rights
     * @return rights: array with all the rights
     */
    static function get_all_publication_rights($location)
    {
        $inherits = $location->get_inherit();
        $publisher = $location->get_tree_identifier();
        $rights = array();
        $view_option ='';
        $edit_option='';
        $fbv_option ='';
        $fbg_option ='';

        if(!$inherits)
        {
            $rights = self::get_rights_on_location($location->get_id());
            $user_rights = $rights[self::USER_RIGHTS];
            if(isset($user_rights))
            {
//                $rights[self::VIEW_RIGHT][self::USER_RIGHTS]= array();
//                $rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]= array();
//                $rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]= array();
//                $rights[self::EDIT_RIGHT][self::USER_RIGHTS]= array();
                while ($uright = $user_rights->next_result())
                {
                      $rights[$uright->get_right_id()][self::USER_RIGHTS][]= $uright->get_user_id();

                }
            }

            $group_rights = $rights[self::GROUP_RIGHTS];
            if(isset($group_rights))
            {
//                $rights[self::VIEW_RIGHT][self::GROUP_RIGHTS]= array();
//                $rights[self::VIEW_FEEDBACK_RIGHT][self::GROUP_RIGHTS]= array();
//                $rights[self::GIVE_FEEDBACK_RIGHT][self::GROUP_RIGHTS]= array();
//                $rights[self::EDIT_RIGHT][self::GROUP_RIGHTS]= array();
                while ($gright = $group_rights->next_result())
                {
                      $rights[$gright->get_right_id()][self::GROUP_RIGHTS][]= $gright->get_group_id();

                }
            }

            //VIEW RIGHTS
            if(in_array('1', $rights[self::VIEW_RIGHT][self::USER_RIGHTS]))
            {
                $view_option = self::RADIO_OPTION_ANONYMOUS;
                //right set for anonymous user --> right set for everybody
            }
            else if (in_array($publisher, $rights[self::VIEW_RIGHT][self::USER_RIGHTS]))
            {
                $view_option = self::RADIO_OPTION_ME;
                //right set for owner --> right set for nobody else
            }
            else if((count($rights[self::VIEW_RIGHT][self::USER_RIGHTS]) >0) && (count($rights[PortfolioPublicationForm::RIGHT_VIEW][self::GROUP_RIGHTS]) >0))
            {
                $view_option = self::RADIO_OPTION_GROUPS_USERS;
                //right set for groups or users
            }
            else
            {
                $view_option = self::RADIO_OPTION_ALLUSERS;
                //no rights set --> right set for all platform users
            }
            //EDIT RIGHTS
            if(in_array('1', $rights[self::EDIT_RIGHT][self::USER_RIGHTS]))
            {
                $edit_option = self::RADIO_OPTION_ANONYMOUS;
                //right set for anonymous user --> right set for everybody
            }
            else if (in_array($publisher, $rights[self::EDIT_RIGHT][self::USER_RIGHTS]))
            {
                $edit_option = self::RADIO_OPTION_ME;
                //right set for owner --> right set for nobody else
            }
            else if((count($rights[self::EDIT_RIGHT][self::USER_RIGHTS]) >0) && (count($rights[PortfolioPublicationForm::RIGHT_VIEW][self::GROUP_RIGHTS]) >0))
            {
                $edit_option = self::RADIO_OPTION_GROUPS_USERS;
                //right set for groups or users
            }
            else
            {
                $edit_option = self::RADIO_OPTION_ALLUSERS;
                //no rights set --> right set for all platform users
            }
            //FEEDBACK VIEW RIGHTS
            if(in_array('1', $rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]))
            {
                $fbv_option = self::RADIO_OPTION_ANONYMOUS;
            }
            else if (in_array($location->get_tree_identifier, $rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]))
            {
                $fbv_option = self::RADIO_OPTION_ME;
            }
            else if((count($rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]) >0) && (count($rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK][self::GROUP_RIGHTS]) >0))
            {
                $fbv_option = self::RADIO_OPTION_GROUPS_USERS;
            }
            else
            {
                $fbv_option = self::RADIO_OPTION_ALLUSERS;
            }
            //FEEDBACK GIVE RIGHTS
            if(in_array('1', $rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]))
            {
                $fbg_option = self::RADIO_OPTION_ANONYMOUS;
            }
            else if (in_array($publisher, $rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]))
            {
                $fbg_option = self::RADIO_OPTION_ME;
            }
            else if((count($rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]) >0) && (count($rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK][self::GROUP_RIGHTS]) >0))
            {
                $fbg_option = self::RADIO_OPTION_GROUPS_USERS;
            }
            else
            {
                $fbg_option = self::RADIO_OPTION_ALLUSERS;
            }

        }
        $rights[PortfolioPublicationForm::INHERIT_OR_SET]['option']= $inherits;
        $rights[PortfolioPublicationForm::RIGHT_EDIT]['option']= $edit_option;
        $rights[PortfolioPublicationForm::RIGHT_VIEW]['option']= $view_option;
        $rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK]['option']= $fbv_option;
        $rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK]['option']= $fbg_option;
        return $rights;

    }

    /**
    * implements the updating of different selected rights for a location
    * @param values: array with all the information on the rights to implement
    * @param location: location for wich to implement the rights
    */
    static function implement_update_rights($values, $location)
    {
        return self::implement_rights($values, $location);
    }

    static function delete_location($cid)
    {
        $success = true;
        $rdm = RepositoryDataManager :: get_instance();
        $item = $rdm->retrieve_complex_content_object_item($cid);
        $user_id = $item->get_user_id();
        $location_id = self::get_location_id_by_identifier_from_portfolio_subtree(array(self::TYPE_PORTFOLIO_ITEM, self::TYPE_PORTFOLIO_SUB_FOLDER), $cid, $user_id);
        $success &= self::delete_rights_on_location($location_id);


        $rdm = RightsDataManager::get_instance();
        $condition = new EqualityCondition(Location::PROPERTY_ID, $location_id);
        $success &= $rdm->delete(Location :: get_table_name(), $condition);

        return $success;

    }

    static function delete_rights_on_location($location_id)
    {
        $success = true;
        $rdm = RightsDataManager::get_instance();

        $condition_u = new EqualityCondition(UserRightLocation::PROPERTY_LOCATION_ID, $location_id);
        $success &= $rdm->delete(UserRightLocation :: get_table_name(), $condition_u);


        $condition_g = new EqualityCondition(GroupRightLocation::PROPERTY_LOCATION_ID, $location_id);
        $success &= $rdm->delete(GroupRightLocation::get_table_name(), $condition_g);

        return $success;

    }

   /**
    * implements the setting of different selected rights for a location
    * @param values: array with all the information on the rights to implement
    * @param location: location for wich to implement the rights
    */
    static function implement_rights($values, $location)
    {
        if(isset($location) && $location !=false)
        {
//            $set_inherit = true;
//            $set_fbv = true;
//            $set_fbg = true;
//            $set_edit = true;
//            $set_view= true;
            $success = true;
            if(array_key_exists(PortfolioPublicationForm::INHERIT_OR_SET.'_option', $values))
            {
                if(($values[PortfolioPublicationForm::INHERIT_OR_SET.'_option'] == self::RADIO_OPTION_DEFAULT)||($values[PortfolioPublicationForm::INHERIT_OR_SET.'_option'] == self::RADIO_OPTION_INHERIT))
                {
                     $location->set_inherit(true);
                }
                else
                {
                    $location->set_inherit(false);
                    //SET VIEWING RIGHTS
                    //option-value defined for viewing?
                    $view_option = null;
                    if(array_key_exists(PortfolioPublicationForm::RIGHT_VIEW.'_option', $values))
                    {
                       $view_option =  $values[PortfolioPublicationForm::RIGHT_VIEW.'_option'];
                    }
                    //user-rights defined for viewing?
                    $view_user = null;
                    if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
                    {
                        $view_user = $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']['user'];
                    }
                    //group-rights defined for viewing?
                    $view_group = null;
                    if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
                    {
                        $view_group = $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']['group'];
                    }
                    if(isset($view_option))
                    {
                         $success &=  PortfolioRights::set_rights($location, PortfolioRights::VIEW_RIGHT,  $view_group, $view_user , $view_option);
                    }
                    //SET EDITING RIGHTS
                    //option-value defined for editing?
                    $edit_option = null;
                    if(array_key_exists(PortfolioPublicationForm::RIGHT_EDIT.'_option', $values))
                    {
                       $edit_option =  $values[PortfolioPublicationForm::RIGHT_EDIT.'_option'];
                    }
                    //user-rights defined for editing?
                    $edit_user = null;
                    if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_EDIT.'_elements']))
                    {
                        $edit_user = $values[PortfolioPublicationForm::RIGHT_EDIT.'_elements']['user'];
                    }
                    //group-rights defined for editing?
                    $edit_group = null;
                    if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_EDIT.'_elements']))
                    {
                        $edit_group = $values[PortfolioPublicationForm::RIGHT_EDIT.'_elements']['group'];
                    }
                    if(isset($edit_option))
                    {
                         $success &=  PortfolioRights::set_rights($location, PortfolioRights::EDIT_RIGHT,  $edit_group, $edit_user , $edit_option);
                    }
                    //SET FB-GIVING RIGHTS
                    //option-value defined for fb_giving?
                    $fbg_option = null;
                    if(array_key_exists(PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_option', $values))
                    {
                       $fbg_option =  $values[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_option'];
                    }
                    //user-rights defined for fb_giving?
                    $fbg_user = null;
                    if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_elements']))
                    {
                        $fbg_user = $values[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_elements']['user'];
                    }
                    //group-rights defined for fb_giving?
                    $fbg_group = null;
                    if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_elements']))
                    {
                        $fbg_group = $values[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK.'_elements']['group'];
                    }
                    if(isset($fbg_option))
                    {
                         $success &=  PortfolioRights::set_rights($location, PortfolioRights::GIVE_FEEDBACK_RIGHT,  $fbg_group, $fbg_user , $fbg_option);
                    }
                    //SET FB-VIEWING RIGHTS
                    //option-value defined for fb_viewing?
                    $fbv_option = null;
                    if(array_key_exists(PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_option', $values))
                    {
                       $fbv_option =  $values[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_option'];
                    }
                    //user-rights defined for fb_viewing?
                    $fbv_user = null;
                    if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_elements']))
                    {
                        $fbv_user = $values[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_elements']['user'];
                    }
                    //group-rights defined for fb_viewing?
                    $fbv_group = null;
                    if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_elements']))
                    {
                        $fbv_group = $values[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK.'_elements']['group'];
                    }
                    if(isset($fbv_option))
                    {
                        $success &= PortfolioRights::set_rights($location, PortfolioRights::VIEW_FEEDBACK_RIGHT,  $fbv_group, $fbv_user , $fbv_option);
                    }
                }
                $success &= $location->save();

            }
       }
       else
       {
             $success = false;
             //the given location was not actually a location or could not be retrieved.
             //Need to set error message somewhere???
       }
       return $success;
    }

     /**
     * updates the rights for a specific item
     * @param location: location of the item
     * @param rightType: right to be given (for possible rights, see PortfolioRights class)
     * @param chosenOption : option chosen for this right
     * @param groups : specific groups who get this right
     * @param users : specific users who get this right
     * @return success (true or false)
     */
    static function update_rights($location, $rightType, $groups, $users, $chosenoption = null)
    {
      // TODO: implement this function
        return self::set_rights($location, $rightType, $groups, $users, $chosenoption);
    }


    /**
     * create a location for a portfolio or portfolio-item in the portfolio-tree
     * @param $type: type of object the location refers to, can be "portfolio" or "portfolio-item"
     * @param $identifier: id of the object the location refers to
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @param $name: name for the location
     *@param $parent: parent of the location
     * @param $inherit: true or false --> does the location inherit rights from it's parents
     * @param $locked: true of false --> can children override rights set for this location?
     * @return location when location has been created or false
     */
    static function create_location_in_portfolio_tree($name, $type, $identifier, $parent, $user_id, $inherit, $locked)
    {
    	return RightsUtilities::create_location($name, PortfolioManager::APPLICATION_NAME, $type, $identifier, $inherit, $parent, $locked, $user_id, 'portfolio_tree', true);
    }


    /**
     * this method wil return the id of the location of a certain portfolio-publication
     * or of a specific portfolio-item based on the id of the published item/portfolio
     * @param $types: type of object the location refers to, can be one type or an array of different types
     * @param $identifier: id of the object the location refers to
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @return location_id or false if no location has been found
     */
     static function get_location_id_by_identifier_from_portfolio_subtree($type, $identifier, $user_id)
     {
    	$location = self::get_portfolio_location($identifier, $type, $user_id);

        if($location != false)
        {
            return $location->get_id();
        }
        else
        {
            return false;
        }

    }

    /**
     * this function returns the right a given user has on an portfolio item
     * @param $user_id: id of user to check permissions for
     * @param $portfolio_identifier: portfolio-item to check permissions on
     * @param $types: possible types of portfolio-item (array)
     * @return array with true or false values on every right
     */
    static function get_rights($user_id, $portfolio_identifier, $types)
    {
        $view = false;
        $edit = false;
        $view_feedback=false;
        $give_feedback=false;

        if(isset($_SESSION[self::SESSION_RIGHTS]))
        {
                $my_rights = $_SESSION[self::SESSION_RIGHTS];
        }
        else
        {
            $my_rights=array();
        }

       
        if( !isset($my_rights[$portfolio_identifier][$user_id]))
        {
            //rights for this user on this location have not been checked yet
            $rights_array = array();

           if(in_array(self::TYPE_PORTFOLIO_FOLDER, $types))
            {
                //if it is a porfolio on the top level, get the owner from the portfolio_publication table
                $owner_id = PortfolioPublication::get_publication_owner($portfolio_identifier);
            }
            else
            {
                //if it is an item inside a portfolio or a sub-portfolio, get the owner from the complex_content_object_item table
                $owner_id = PortfolioPublication::get_item_owner($portfolio_identifier);
            }

            //CHECK1: USER IS OWNER or ADMIN
            if(($owner_id != $user_id))
            {
                $udm = UserDataManager::get_instance();
                $user = $udm->retrieve_user($user_id);
            }
            if(($owner_id == $user_id) || (is_object($user) && $user->is_platform_admin)  )
            {
                //result1: YES --> user has all rights automatically
                $view = true;
                $edit = true;
                $view_feedback=true;
                $give_feedback=true;
                $rights_array[self::SET_PERMISSIONS_RIGHT] = true;
            }
            else
            { //result1: NO --> user does not have all rights automatically
                $location = self::get_portfolio_location($portfolio_identifier, $type, $owner_id);
                if(is_object($location))
                {

                //CHECK2: LOCATION HAS LOCKED PARRENT
                $locked_parent = $location->get_locked_parent();
                if(isset($locked_parent))
                {
                    //RESULT2: YES --> rights on the locked parrent are checked and proceded to CHECK4
                    $location = $locked_parent;
                }
                //CHECK3: LOCATION INHERITS
                if($location->inherits())
                {
                    //RESULT3: YES --> a check is done untill no inheriting parent is found then proceded to CHECK4
                    $parents_set = $location->get_parents();
                    $found = false;
                    if($parents_set->size() > 0)
                    {
                        
                        while($found = false && $location = $parents_set->next_result())
                        {
                            if(!$location->inherits())
                            {
                               $found = true;
                            }
                         }
                    }
                }
                //CHECK4: ARE THERE ANY RIGHTS DEFINED FOR THIS LOCATION FOR ANYBODY
                $rights = self::get_rights_on_location($location->get_id());
                $group_rights_set = $rights[self::GROUP_RIGHTS];
                $user_rights_set = $rights[self::USER_RIGHTS];

                $everybody = false;

                if(($user_rights_set->size() > 0) || ($group_rights_set->size() > 0))
                {
                    //RESULT4: YES --> there were rights defined for this location
                    $user_viewing_rights = array();
                    $user_editing_rights = array();
                    $user_feedback_giving_rights = array();
                    $user_feedback_viewing_rights = array();

                    while ($right = $user_rights_set->next_result())
                    {
                        $ruid = $right->get_user_id();
                        if($ruid == $user_id)
                        {
                            $id = $right->get_right_id();
                            if($id == self::VIEW_RIGHT)
                            {
                                $view = true;
                            }
                            else if($id == self::EDIT_RIGHT)
                            {
                                $edit = true;
                            }
                            else if($id == self::VIEW_FEEDBACK_RIGHT)
                            {
                                $view_feedback=true;
                            }
                            else if($id == self::GIVE_FEEDBACK_RIGHT)
                            {
                                 $give_feedback=true;
                            }
                        }
                        else if ($ruid == $owner_id)
                        {
                            //right set for owner = right for nobody else
                            $id = $right->get_right_id();
                            if($id == self::VIEW_RIGHT)
                            {
                                $view = false;
                            }
                            else if($id == self::EDIT_RIGHT)
                            {
                                $edit = false;
                            }
                            else if($id == self::VIEW_FEEDBACK_RIGHT)
                            {
                                $view_feedback=false;
                            }
                            else if($id == self::GIVE_FEEDBACK_RIGHT)
                            {
                                 $give_feedback=false;
                            }
                        }
                        else if ($ruid == 1)
                        {
                            //right set for anonymous user = right set for everybody else
                            $id = $right->get_right_id();
                            if($id == self::VIEW_RIGHT)
                            {
                                $view = true;
                            }
                            else if($id == self::EDIT_RIGHT)
                            {
                                $edit = true;
                            }
                            else if($id == self::VIEW_FEEDBACK_RIGHT)
                            {
                                $view_feedback=true;
                            }
                            else if($id == self::GIVE_FEEDBACK_RIGHT)
                            {
                                 $give_feedback=true;
                            }
                        }

                    }

                    $udm = UserDataManager::get_instance();
                    $user = $udm->retrieve_user($user_id);
                    $user_groups_set = $user->get_groups();
                    if(! is_null($user_groups))
                    {
                        $groups_array = array();
                        while($group = $user_groups_set->next_result())
                        {
                            $groups_array[] = $group->get_id();
                        }
                    while ($right = $group_rights_set->next_result())
                    {
                        $id = $right->get_right_id();
                        $grid = $right->get_group_id();
                        if(in_array($grid, $groups_array))
                        {
                            if($id == self::VIEW_RIGHT)
                            {
                                $view = true;
                            }
                            else if($id == self::EDIT_RIGHT)
                            {
                                $edit = true;
                            }
                            else if($id == self::VIEW_FEEDBACK_RIGHT)
                            {
                                $view_feedback=true;
                            }
                            else if($id == self::GIVE_FEEDBACK_RIGHT)
                            {
                                 $give_feedback=true;
                            }
                        }
                    }

                    }
                      
                }
                else
                {
                    //RESULT4: NO --> there were no rights defined for this location: every user (logged in) has the viewing & feedback rights
                    $view = true;
                    $edit = false; //editing right can only be given to specific groups or users, not to everybody. if no rights are defined, nobody has the rights
                    $view_feedback=true;
                    $give_feedback=true;
                }

                }
                else
                    {
                    echo 'no object';
                }
        }
        ///check view rights
        $rights_array[self::VIEW_RIGHT] = $view;

        //check editing rights
        $rights_array[self::EDIT_RIGHT] = $edit;

        //check feedback viewing rights
        $rights_array[self::VIEW_FEEDBACK_RIGHT] = $view_feedback;

        //check feedback giving rights
        $rights_array[self::GIVE_FEEDBACK_RIGHT] = $give_feedback;

       $my_rights[$portfolio_identifier][$user_id] = $rights_array;
       $_SESSION[self::SESSION_RIGHTS] = $my_rights;

        }
        else
        {
            //rights for this user on this location have already been checked this session and can be returned
            $rights_array =  $my_rights[$portfolio_identifier][$user_id];

        }
        return $rights_array;
    }

  
    /**
     * this method returns the location that is linked to a specific portfolio item
     * @param $portfolio_identifier: the id of the portfolio-item
     * @param $type: the type of the portfolio-item this is an array so can be more then one type
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @return $location: object or false if no location has been found
     */
    static function get_portfolio_location($portfolio_identifier, $type, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, PortfolioManager::APPLICATION_NAME);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, self::PORTFOLIO_TREE_TYPE_NAME);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_IDENTIFIER, $user_id);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_IDENTIFIER, $portfolio_identifier);
        if($type!=null)
        {
            if(is_array($type))
            {
                $types = $type;
            }
            else
            {
                $types = array($type);
            }
            foreach ($types as $value)
            {
                $typeconditions[] = new EqualityCondition(Location :: PROPERTY_TYPE, $value);
            }
            if(count($typeconditions) > 1)
            {
                $conditions[] = new OrCondition($typeconditions);
            }
            else
            {
                $conditions[]= $typeconditions[0];
            }
        }
        $condition = new AndCondition($conditions);
        $rdm = RightsDataManager::get_instance();
        $location_set = $rdm->retrieve_locations($condition, 0,1);
        $nr_locations = $location_set->size();
        if(!($nr_locations > 0))
        {
            //TODO: no locations found --> error or imported from repository????
            
            $location = false;
        }
        else if($nr_locations == 1)
        {
            $location = $location_set->next_result();
        }
        else
        {
            //TODO: more then one location found --> error
            
            $location = false;
        }
        return $location;
    }

        /**
         * this function returns all the rights for a given location
         * @param location_id: the id of the location to check
         * @return array with arrays of group rights [group] and user rights [user]
         */
        static function get_rights_on_location($location_id)
        {
//            $group_rights = array();
//            $user_rights = array();
            $rights = array();
            $rdm = RightsDataManager::get_instance();
            $condition = new EqualityCondition(UserRightLocation::PROPERTY_LOCATION_ID, $location_id);
            $user_rights_set = $rdm->retrieve_user_right_locations($condition, $offset = null, $max_objects = null, $order_by = null);
            $group_rights_set = $rdm->retrieve_group_right_locations($condition, $offset = null, $max_objects = null, $order_by = null);

            $rights[self::GROUP_RIGHTS] = $group_rights_set;
            $rights[self::USER_RIGHTS] = $user_rights_set;

            return $rights;

        }

}
?>