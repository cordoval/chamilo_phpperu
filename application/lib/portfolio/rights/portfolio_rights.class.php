<?php

/**
 * class to handle the different rights in the portfolio application
 *
 * following logic will be used to set rights for the portfolio application:
 * 1) rights for user_id 1 = rights for anonymous users & every platform-user
 * 2) rights for user_id 0 = rights for every platform-user
 * 3) no rights = right for portfolio(-item) owner only
 * 4) rights defined for specific users and/or groups
 *
 * rights can be inherited from parent item or set for item specifically.
 * if rights are set for item specifically they are NOT inherited from the parent item.
 *
 * Top-level portfolio's can inherit default rights
 *
 * @author Nathalie Blocry
 */
require_once dirname(__FILE__) . '/portfolio_group_right_location.class.php';
require_once dirname(__FILE__) . '/portfolio_location.class.php';
require_once dirname(__FILE__) . '/portfolio_user_right_location.class.php';



class PortfolioRights {

    const ANONYMOUS_USERS_ID = '1';
    const ALL_USERS_ID ='0';
    const DEFAULT_LOCATION_TREE_IDENTIFIER = 0;

    const VIEW_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const VIEW_FEEDBACK_RIGHT = '3';
    const GIVE_FEEDBACK_RIGHT = '4';
    const SET_PERMISSIONS_RIGHT = '5';

    const TYPE_PORTFOLIO_FOLDER = 1;
    const TYPE_PORTFOLIO_SUB_FOLDER = 2;
    const TYPE_PORTFOLIO_ITEM = 3;
    const TYPE_ROOT = 0;

    
    const RADIO_OPTION_SET_SPECIFIC = 'set';
    const RADIO_OPTION_DEFAULT = 'SystemDefaults';
    const RADIO_OPTION_INHERIT = 'inheritFromParent';
    const RADIO_OPTION_ANONYMOUS = 'AnonymousUsers';
    const RADIO_OPTION_ALLUSERS =  'SystemUsers';
    const RADIO_OPTION_ME = 'OnlyMe';
    const RADIO_OPTION_GROUPS_USERS = '1';

    const GROUP_RIGHTS = 'group';
    const USER_RIGHTS = 'user';
    const SESSION_RIGHTS = 'portfolio_rights';



    /**
     * create a portfolio_tree for a specific user. The portfolio-tree is a tree of locations that represent
     * the structure of the portfolio and can be used to have items inherit rights
     *  @param $user_id: id of the user that owns the portfolio to identify it (tree-identifier)
     * @return true or false
     */
    static function create_portfolio_root($user_id)
    {
        return self::create_subtree_root_location($user_id, true);
    }

    /**
     * get the root of a specific user's portfolio-tree.
     * @param $user_id: id of the user the portfolio belongs to, this is the tree_identifier
     * @return returns the id of the root location or "false" when no root location is found
     */
    static function get_portfolio_root_id($user_id)
    {
        return self::get_root_id($user_id);
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
        $success &= self::delete_rights_on_location($location_id, $rightType);

       //2. add required rights
       if ($chosenoption == self::RADIO_OPTION_ALLUSERS)
        {
            //inherit = false, only user-right for user_id 0, no group-rights
            $rdm = PortfolioDataManager::get_instance();
            $purl = new PortfolioUserRightLocation();
            $purl->set_location_id($location_id);
            $purl->set_right_id($rightType);
            $purl->set_user_id(self::ALL_USERS_ID);
            $success &= $rdm->create_user_right_location($purl);
        }
        elseif ($chosenoption == self::RADIO_OPTION_ANONYMOUS)
        {
            //inherit = false, only user-right for user_id 1, no group-rights
            $rdm = PortfolioDataManager::get_instance();
            $purl = new PortfolioUserRightLocation();
            $purl->set_location_id($location_id);
            $purl->set_right_id($rightType);
            $purl->set_user_id(self::ANONYMOUS_USERS_ID);
            $success &= $rdm->create_user_right_location($purl);
        }
        elseif ($chosenoption == self::RADIO_OPTION_ME)
        {
            //inherit = false, no user-rights, no group-rights
        }
        else
        {
            //inherit = false, specific user-rights, specific group-rights

            if((is_array($groups)) && (count($groups) > 0))
            {
                foreach ($groups as $group)
                {
                    $rdm = PortfolioDataManager::get_instance();
                    $pgrl = new PortfolioGroupRightLocation();
                    $pgrl->set_location_id($location_id);
                    $pgrl->set_right_id($rightType);
                    $pgrl->set_group_id($group);
                    $success &= $rdm->create_group_right_location($pgrl);
                   
                }
            }
             if((is_array($users)) && (count($users) > 0))
            {
                foreach ($users as $user_id)
                {
                    $rdm = PortfolioDataManager::get_instance();
                    $purl = new PortfolioUserRightLocation();
                    $purl->set_location_id($location_id);
                    $purl->set_right_id($rightType);
                    $purl->set_user_id($user_id);
                    $success &= $rdm->create_user_right_location($purl);
                   
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
        if($location)
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
                    while ($uright = $user_rights->next_result())
                    {
                          $rights[$uright->get_right_id()][self::USER_RIGHTS][]= $uright->get_user_id();
                    }
                }

                $group_rights = $rights[self::GROUP_RIGHTS];
                if(isset($group_rights))
                {
                    while ($gright = $group_rights->next_result())
                    {
                          $rights[$gright->get_right_id()][self::GROUP_RIGHTS][]= $gright->get_group_id();
                    }
                }
                //VIEW RIGHTS
                if(in_array(self::ANONYMOUS_USERS_ID, $rights[self::VIEW_RIGHT][self::USER_RIGHTS]))
                {
                    $view_option = self::RADIO_OPTION_ANONYMOUS;
                    //right set for anonymous user (1) --> right set for everybody
                }
                else if(in_array(self::ALL_USERS_ID, $rights[self::VIEW_RIGHT][self::USER_RIGHTS]))
                {
                    $view_option = self::RADIO_OPTION_ALLUSERS;
                    //right set for all users (0) --> right set for everybody logged in
                }
                else if((count($rights[self::VIEW_RIGHT][self::USER_RIGHTS]) >0) || (count($rights[PortfolioPublicationForm::RIGHT_VIEW][self::GROUP_RIGHTS]) >0))
                {
                    $view_option = self::RADIO_OPTION_GROUPS_USERS;
                    //right set for groups or users
                }
                else
                {
                    $view_option = self::RADIO_OPTION_ME;
                    //no rights set --> right set for owner but nobody else
                }
                //EDIT RIGHTS
                if(in_array(self::ANONYMOUS_USERS_ID, $rights[self::EDIT_RIGHT][self::USER_RIGHTS]))
                {
                    $edit_option = self::RADIO_OPTION_ANONYMOUS;
                    //right set for anonymous user --> right set for everybody
                }
                else if (in_array(self::ALL_USERS_ID, $rights[self::EDIT_RIGHT][self::USER_RIGHTS]))
                {
                    $edit_option = self::RADIO_OPTION_ALLUSERS;
                    //right set for all users (0) --> right set for everybody logged in
                }
                else if((count($rights[self::EDIT_RIGHT][self::USER_RIGHTS]) >0) || (count($rights[PortfolioPublicationForm::RIGHT_VIEW][self::GROUP_RIGHTS]) >0))
                {
                    $edit_option = self::RADIO_OPTION_GROUPS_USERS;
                    //right set for groups or users
                }
                else
                {
                    $edit_option =  self::RADIO_OPTION_ME;
                    //no rights set --> right set for owner but nobody else
                }
                //FEEDBACK VIEW RIGHTS
                if(in_array(self::ANONYMOUS_USERS_ID, $rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]))
                {
                    $fbv_option = self::RADIO_OPTION_ANONYMOUS;
                }
                else if (in_array(self::ALL_USERS_ID, $rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]))
                {
                    $fbv_option = self::RADIO_OPTION_ALLUSERS;
                    //right set for all users (0) --> right set for everybody logged in
                }
                else if((count($rights[self::VIEW_FEEDBACK_RIGHT][self::USER_RIGHTS]) >0) || (count($rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK][self::GROUP_RIGHTS]) >0))
                {
                    $fbv_option = self::RADIO_OPTION_GROUPS_USERS;
                }
                else
                {
                    $fbv_option =  self::RADIO_OPTION_ME;
                    //no rights set --> right set for owner but nobody else
                }
                //FEEDBACK GIVE RIGHTS
                $nr_fbg = count($rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]);
                if(in_array(self::ANONYMOUS_USERS_ID, $rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]))
                {
                    $fbg_option = self::RADIO_OPTION_ANONYMOUS;
                }
                else if (in_array(self::ALL_USERS_ID, $rights[self::GIVE_FEEDBACK_RIGHT][self::USER_RIGHTS]))
                {
                    $fbg_option = self::RADIO_OPTION_ALLUSERS;
                    //right set for all users (0) --> right set for everybody logged in
                }
                else if(($nr_fbg >0) || (count($rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK][self::GROUP_RIGHTS]) >0))
                {
                    $fbg_option = self::RADIO_OPTION_GROUPS_USERS;
                }
                else
                {
                    $fbg_option =  self::RADIO_OPTION_ME;
                    //no rights set --> right set for owner but nobody else
                }
            }
            $rights[PortfolioPublicationForm::INHERIT_OR_SET]['option']= $inherits;
            $rights[PortfolioPublicationForm::RIGHT_EDIT]['option']= $edit_option;
            $rights[PortfolioPublicationForm::RIGHT_VIEW]['option']= $view_option;
            $rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK]['option']= $fbv_option;
            $rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK]['option']= $fbg_option;
            return $rights;
        }
        else
        {
            return false;
        }

    }

    /**
    * implements the updating of different selected rights for a location
    * @param values: array with all the information on the rights to implement
    * @param location: location for wich to implement the rights
    */
    static function implement_update_rights($values, $location)
    {
        //for the moment updating the rights is the same as implementing them as old rights are deleted
        return self::implement_rights($values, $location);
    }

    static function delete_location_by_id($location_id)
    {
        $success=true;
        $success &= self::delete_rights_on_location($location_id);
        if($success)
        {
            $rdm = PortfolioDataManager::get_instance();
            $condition = new EqualityCondition(PortfolioLocation::PROPERTY_ID, $location_id);
            $success &= $rdm->delete(PortfolioLocation :: get_table_name(), $condition);

            if($success)
            {
                //delete locations for children
                $children_set = self::retrieve_locations_children($location_id);
                $types = array(PortfolioRights::TYPE_PORTFOLIO_ITEM, PortfolioRights::TYPE_PORTFOLIO_ITEM);
                while ($child = $children_set->next_result())
                {
                    $success &= self::delete_location_by_id($child->get_id());
                }
            }
        }

        return $success;

    }

    /**
     * delete a location
     * @param <type> $id = identifier
     * @param <type> $user_id = tree_identifier
     * @param <type> $object_type = object type
     * @return true or false
     */
    static function delete_location($id, $user_id, $object_type)
    {
        $success = true;
        $rdm = RepositoryDataManager :: get_instance();
        $location_id = self::get_location_id_by_identifier_from_portfolio_subtree($object_type, $id, $user_id);
        if($location_id)
        {
            $success = self::delete_location_by_id($location_id);
        }
        else
        {
            $success &= false;
        }

        
        return $success;
    }

    /**
     * delete all the rights for a specific location.
     * @param <type> $location_id
     * @param <type> $right_type optional. if null: delete all rights
     * @return <type> $success
     */
    static function delete_rights_on_location($location_id, $right_type = null)
    {
        $success = true;
        $rdm = PortfolioDataManager::get_instance();

        $condition_u = new EqualityCondition(PortfolioUserRightLocation::PROPERTY_LOCATION_ID, $location_id);
        if($right_type != null)
        {
            $conditions[] = $condition_u;
            $conditions[] =  new EqualityCondition(PortfolioUserRightLocation::PROPERTY_RIGHT_ID, $right_type);
            $condition_u = new AndCondition($conditions);
        }

        $success &= $rdm->delete(PortfolioUserRightLocation :: get_table_name(), $condition_u);

        $condition_g = new EqualityCondition(PortfolioGroupRightLocation::PROPERTY_LOCATION_ID, $location_id);
        if($right_type != null)
        {
            $conditions[] = $condition_g;
            $conditions[] =  new EqualityCondition(PortfolioGroupRightLocation::PROPERTY_RIGHT_ID, $right_type);
            $condition_g = new AndCondition($conditions);
        }
        $success &= $rdm->delete(PortfolioGroupRightLocation::get_table_name(), $condition_g);

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
            $success = true;
            if(array_key_exists(PortfolioPublicationForm::INHERIT_OR_SET.'_option', $values))
            {
                if(($values[PortfolioPublicationForm::INHERIT_OR_SET.'_option'] == self::RADIO_OPTION_DEFAULT)||($values[PortfolioPublicationForm::INHERIT_OR_SET.'_option'] == self::RADIO_OPTION_INHERIT))
                {
                    //if the selected option was "inherit or default", set the inherit flag of the location to "true"
                     $location->set_inherit(true);
                     //todo: delete the specific rights?
                }
                else
                {
                    $location->set_inherit(false);
                    //SET VIEWING RIGHTS
                    $view_option = null;
                    if(array_key_exists(PortfolioPublicationForm::RIGHT_VIEW.'_option', $values))
                    {
                        //option-value defined for viewing?
                       $view_option =  $values[PortfolioPublicationForm::RIGHT_VIEW.'_option'];
                    }
                    $view_user = null;
                    if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
                    {
                        //user-rights defined for viewing?
                        $view_user = $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']['user'];
                    }
                    $view_group = null;
                    if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
                    {
                        //group-rights defined for viewing?
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
             //TODO: Need to return error message somewhere?!?!?
       }
       return $success;
    }


   /**
    * implements the setting of default rights
    * @param values: array with all the information on the rights to implement
    */
    static function implement_default_rights($values)
    {
        $location = self::get_default_location();
        $success = true;
      
        //SET VIEWING RIGHTS
        $view_option = null;
        if(array_key_exists(PortfolioPublicationForm::RIGHT_VIEW.'_option', $values))
        {
            //option-value defined for viewing?
           $view_option =  $values[PortfolioPublicationForm::RIGHT_VIEW.'_option'];
        }
        $view_user = null;
        if(array_key_exists('user', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
        {
            //user-rights defined for viewing?
            $view_user = $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']['user'];
        }
        $view_group = null;
        if(array_key_exists('group', $values[PortfolioPublicationForm::RIGHT_VIEW.'_elements']))
        {
            //group-rights defined for viewing?
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

        $success &= $location->save();
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
      // TODO: should this update function be different then the set function? now everything is deleted and re-set every time
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
     * @param children: this item may contain children that need locations to (import from portfolio from repository)
     * @return location when location has been created or false
     *
     */
    static function create_location_in_portfolio_tree($name, $type, $identifier, $parent, $user_id, $inherit, $locked, $children)
    {
    	return self::create_location($type, $identifier, $inherit, $parent, $locked, $user_id, true, $children);

        
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
            //rights for this user on this location have already been checked and were stored on the session
             $my_rights = $_SESSION[self::SESSION_RIGHTS];
        }
        else
        {
            //rights for this user on this location have not been checked yet
            $my_rights=array();
        }

        if(in_array(self::TYPE_PORTFOLIO_FOLDER, $types))
        {
            $type = 'p';
        }
        else
        {
            $type='c';
        }

        if( !isset($my_rights[$type][$portfolio_identifier][$user_id]))
        {
           $rights_array = array();

           if(in_array(self::TYPE_PORTFOLIO_FOLDER, $types))
            {
                //if it is a portfolio on the top level, get the owner from the portfolio_publication table
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
                $location = self::get_portfolio_location($portfolio_identifier, $types, $owner_id);
                if(is_object($location))
                {//begin location
                    //CHECK2: LOCATION HAS LOCKED PARENT
                    $locked_parent = $location->get_locked_parent();
                    if(isset($locked_parent))
                    {
                        //RESULT2: YES --> rights on the locked parent are checked --> TO CHECK4
                        $location = $locked_parent;
                    }
                    //CHECK3: LOCATION INHERITS
                    if($location->inherits())
                    {
                        //RESULT3: YES --> a check is done untill no inheriting parent is found then TO CHECK4
                        $parents_set = $location->get_parents();
                        $found = false;
                       

                            while($found == false && $location = $parents_set->next_result())
                            {
                                if($location->inherits())
                                {
                                   $found = false;
                                }
                                else
                                {
                                    $found = true;
                                }
                             }
                        

                        //CHECK3b: IS LOCATION THE TREE's ROOT
                        if($location->get_parent() == 0)
                        {
                            //RESULT3b: YES: default rights apply
                            $location = self::get_default_location();
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


                        //5a:check if this user has been given a specific right individually
                        while ($right = $user_rights_set->next_result())
                        {

                            $ruid = $right->get_user_id();
                            if(($ruid == $user_id) || ($ruid == self::ANONYMOUS_USERS_ID) || (($ruid == self::ALL_USERS_ID) && ($user_id != 1)))
                            {
                                //a right for this specific user is set or a right for all users is set
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
                      

                        //5b: check if any of the groups a user belongs to has been given a specific right
                        if($view && ($edit || in_array(self::TYPE_PORTFOLIO_FOLDER, $types)) && $view_feedback && $give_feedback )
                        {
                           //user has already all rights so group rights do not need to be checked
                        }
                        else if($group_rights_set->size() > 0)
                        {
                            $udm = UserDataManager::get_instance();
                            $user = $udm->retrieve_user($user_id);
                            $user_groups_set = $user->get_groups();
                            if(!is_null($user_groups_set))
                            {
                                $groups_array = array();
                                //get all ids of the user's groups
                                while($group = $user_groups_set->next_result())
                                {
                                    $groups_array[] = $group->get_id();
                                }
                                while ($right = $group_rights_set->next_result())
                                {
                                    $grid = $right->get_group_id();
                                    if(in_array($grid, $groups_array))
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
                                 }
                            }
                        }
                        
                    }
                    else
                    {
                        //RESULT4: NO --> there were no rights defined for this location: only the owner has any rights
                        $view = false;
                        $edit = false; 
                        $view_feedback=false;
                        $give_feedback=false;

                    }

                }
                else
                {
                    //TODO error handling
                    echo 'no object';
                }
            }

            $rights_array[self::VIEW_RIGHT] = $view;
            $rights_array[self::EDIT_RIGHT] = $edit;
            $rights_array[self::VIEW_FEEDBACK_RIGHT] = $view_feedback;
            $rights_array[self::GIVE_FEEDBACK_RIGHT] = $give_feedback;

            $my_rights[$type][$portfolio_identifier][$user_id] = $rights_array;
            $_SESSION[self::SESSION_RIGHTS] = $my_rights;

        }
        else
        {
            //rights for this user on this location have already been checked this session and can be returned
            $rights_array =  $my_rights[$type][$portfolio_identifier][$user_id];

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
        $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $user_id);
        $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_IDENTIFIER, $portfolio_identifier);
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
                $typeconditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TYPE, $value);
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

        $location_set = self::retrieve_locations($condition, 0,1);
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

        $rights = array();
        $condition = new EqualityCondition(PortfolioUserRightLocation::PROPERTY_LOCATION_ID, $location_id);
        $user_rights_set = self::retrieve_user_right_locations($condition, $offset = null, $max_objects = null, $order_by = null);
        $group_rights_set = self::retrieve_group_right_locations($condition, $offset = null, $max_objects = null, $order_by = null);

        $rights[self::GROUP_RIGHTS] = $group_rights_set;
        $rights[self::USER_RIGHTS] = $user_rights_set;

        return $rights;

    }


        /**
         * retrieve the location by it's id
         * @param <type> $location_id
         * @return <type> portfolio location
         */
        static function retrieve_location($location_id)
        {
            $pdm = PortfolioDataManager::get_instance();
            $condition = new EqualityCondition(PortfolioLocation :: PROPERTY_ID, $location_id);
            return $pdm->retrieve_object(PortfolioLocation :: get_table_name(), $condition, array(), PortfolioLocation :: CLASS_NAME);
        }
  

        
        /**
         * get the root of the portfolio location tree for this user
         * @param <type> $user_id
         * @return <type> id of the root portfolio location
         */
        static function get_root_id($user_id)
        {
            $root = self :: get_root($user_id);
            if($root)
            {
                return $root->get_id();
            }
            else
            {
                return false;
            }
        }

        static function get_default_location()
        {
            $default_root = self::get_root(self::DEFAULT_LOCATION_TREE_IDENTIFIER);
            if($default_root == false)
            {
                $default_root = self::create_default_location();
            }
            return $default_root;
        }


        /**
         * get the portfolio location object for the root of this user's portfolio locations tree
         * @param <type> $user_id
         * @return Portfoliolocation or False
         */
        static function get_root($user_id)
        {
            $root_conditions = array();
            $root_conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_PARENT, 0);
            $root_conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $user_id);

            $root_condition = new AndCondition($root_conditions);

            $pdm = PortfolioDataManager::get_instance();
            $roots = $pdm->retrieve_locations($root_condition, null, 1);

            if (isset($roots) && $roots->size() > 0)
            {
                return $roots->next_result();
            }
            else
            {
                //TODO: differentiate between No root and more then one roots --> both are a problem!!!
                return false;
            }
        }

        /**
         * create the given location in the database
         * @param <type> $location
         * @return <type>
         */
        static function location_create($location)
        {
            //TODO: move to datamanager?!
            $pdm = PortfolioDataManager::get_instance();
            $success = $pdm->create_location($location);

                if($return_location && $success)
                {
                        return $location;
                }
                else
                {
                        return $success;
                }
        }


        /**
         *
         * @param <integer> $type = portfolio type
         * @param <integer> $identifier = complex content object or portfolio publication id
         * @param <bool> $inherit
         * @param <integer> $parent = id of the parent location. can be null, then the location is put under the root
         * @param <bool> $locked
         * @param <type> $user_id = user_id to identify the portfolio tree (owner of the portfolio's)
         * @param <bool> $return_location = return location (true) or only id (false)
         * @param <bool> $children: create location for child-objects too
         * @return <PortfolioLocation> location or success (true or false)
         */
        static function create_location($type, $identifier, $inherit, $parent, $locked, $user_id, $return_location, $children)
        {
            $success = true;
            if($parent == null)
            {
                //if no parent is set, location is created under tree root
                $parent = PortfolioRights::get_portfolio_root_id($user_id);
                if(!$parent)
                {
                    //if no root for this user exists, the root is created
                    $root = PortfolioRights::create_portfolio_root($user_id);
                    if($root)
                    {
                        $parent = PortfolioRights::get_portfolio_root_id($user_id);
                    }
                }

            }

            $location = new PortfolioLocation();
            $location->set_parent($parent);
            $location->set_type($type);
            $location->set_identifier($identifier);
            $location->set_inherit($inherit);
            $location->set_locked($locked);
            $location->set_tree_identifier($user_id);
            $success &= $location->create();

            if($children)
            {
                //the publication may be a portfolio with sub-items
                if($type == self::TYPE_PORTFOLIO_FOLDER)
                {
                    $children_set = PortfolioManager::get_portfolio_children($identifier, true, false);
                }
                else
                {
                    $children_set = PortfolioManager::get_portfolio_children($identifier, false, true);

                }
               
                if($children_set)
                {
                    $location_id = $location->get_id();
//                    $pdm = PortfolioDataManager::get_instance();
//                    $success &= $pdm->create_locations_for_children($children_set, $parent_location_id, $publication->get_owner());
                    while($complex_child = $children_set->next_result())
                    {
                        $object_id = PortfolioManager::get_co_id_from_complex_wrapper($complex_child->get_id(), $complex_child);
                        $rdm = RepositoryDataManager::get_instance();
                        $object = $rdm->retrieve_content_object($object_id);
                        $co_type = $object->get_type();
                        if($object->get_type() == Portfolio::get_type_name())
                        {
                            $type = self::TYPE_PORTFOLIO_SUB_FOLDER;
                            $children = true;
                        }
                        else
                        {
                            $type = self::TYPE_PORTFOLIO_ITEM;
                            $children = false;
                        }
                        
                           $success &= self::create_location($type, $complex_child->get_id(), true, $location_id, false, $user_id, false, $children);
                        
                        
                    }
                    
                    
                }
            }


            if($return_location && $success)
            {
                    return $location;
            }
            else
            {
                    return $success;
            }

        }


        /**
         * retrieve locations
         * @param <type> $condition
         * @param <type> $offset
         * @param <type> $max_objects
         * @param <type> $order_by
         * @return <type>
         */
         static function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
        {
             //TODO: Move to datamanager?
             $pdm = PortfolioDataManager::get_instance();
            return $pdm->retrieve_objects(PortfolioLocation::get_table_name(), $condition, $offset, $max_objects, $order_by, PortfolioLocation::CLASS_NAME);

        }

        static function retrieve_locations_children($location_id)
        {
           
            $condition = new EqualityCondition(PortfolioLocation :: PROPERTY_PARENT, $location_id);
            
            $pdm = PortfolioDataManager::get_instance();
            $children = $pdm->retrieve_locations($condition, null, 1);

            return $children;

        }


        static function retrieve_user_right_locations($condition, $offset , $max_objects , $order_by)
        {
            //TODO: move to data manager?
            $pdm = PortfolioDataManager::get_instance();
            return $pdm->retrieve_user_right_locations($condition, $offset, $max_objects , $order_by);
        }

        static function retrieve_group_right_locations($condition, $offset , $max_objects , $order_by)
        {
             //TODO: move to data manager?
            $pdm = PortfolioDataManager::get_instance();
            return $pdm->retrieve_group_right_locations($condition, $offset, $max_objects , $order_by);
        }




        //TODO: check if these functions are needed
        static function count_locations($condition)
        {
             $pdm = PortfolioDataManager::get_instance();
             return $pdm->count_objects(PortfolioLocation :: get_table_name(), $condition);

        }

    static function add_nested_values($location, $previous_visited, $number_of_elements = 1)
        {
            $pdm = PortfolioDataManager::get_instance();
            // Update all necessary left-values
            $conditions = array();
            $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $location->get_tree_identifier());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
            $condition = new AndCondition($conditions);

            $properties = array(PortfolioLocation :: PROPERTY_LEFT_VALUE => $pdm->escape_column_name(PortfolioLocation :: PROPERTY_LEFT_VALUE) . ' + ' . $pdm->quote($number_of_elements * 2));
            $res = $pdm->update_objects(PortfolioLocation :: get_table_name(), $properties, $condition);

            if (!$res)
            {
                return false;
            }

            // Update all necessary right-values
            $conditions = array();
            $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $location->get_tree_identifier());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
            $condition = new AndCondition($conditions);

            $properties = array(PortfolioLocation :: PROPERTY_RIGHT_VALUE => $pdm->escape_column_name(PortfolioLocation :: PROPERTY_RIGHT_VALUE) . ' + ' . $pdm->quote($number_of_elements * 2));
            $res = $pdm->update_objects(PortfolioLocation :: get_table_name(), $properties, $condition);

            if (!$res)
            {
                return false;
            }

            return true;
        }




    /**
     * @return True if creation is successfull or false
     */
        static function create_subtree_root_location($user_id)
        {
            $location = new PortfolioLocation();
            $location->set_type(self::TYPE_ROOT);
            $location->set_identifier(0);
            $location->set_inherit(false);
            $location->set_tree_identifier($user_id);
            $location->set_locked(false);
            $location->set_parent(0);
            return $location->create();
        }

    static function create_default_location()
    {
        return self::create_portfolio_root(self::DEFAULT_LOCATION_TREE_IDENTIFIER);
    }






        static function delete_location_nodes($location)
        {
            $pdm = PortfolioDataManager::get_instance();
            $conditions = array();
            $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $location->get_tree_identifier());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $location->get_left_value());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $location->get_right_value());
            $condition = new AndCondition($conditions);

            return $pdm->delete_objects(PortfolioLocation :: get_table_name(), $condition);
        }

        static function delete_nested_values($location)
        {
            $pdm = PortfolioDataManager::get_instance();
            $delta = $location->get_right_value() - $location->get_left_value() + 1;

            // Update all necessary nested-values
            $conditions = array();
            $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $location->get_tree_identifier());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_left_value());
            $condition = new AndCondition($conditions);

            $properties = array();
            $properties[PortfolioLocation :: PROPERTY_LEFT_VALUE] = $this->escape_column_name(PortfolioLocation :: PROPERTY_LEFT_VALUE) . ' - ' . $pdm->quote($delta);
            $properties[PortfolioLocation :: PROPERTY_RIGHT_VALUE] = $this->escape_column_name(PortfolioLocation :: PROPERTY_RIGHT_VALUE) . ' - ' . $pdm->quote($delta);
            $res = $pdm->update_objects(PortfolioLocation :: get_table_name(), $properties, $condition);

            if (!$res)
            {
                return false;
            }

            // Update some more nested-values
            $conditions = array();
            $conditions[] = new EqualityCondition(PortfolioLocation :: PROPERTY_TREE_IDENTIFIER, $location->get_tree_identifier());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $location->get_left_value());
            $conditions[] = new InequalityCondition(PortfolioLocation :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_right_value());
            $condition = new AndCondition($conditions);

            $properties = array(PortfolioLocation :: PROPERTY_RIGHT_VALUE => $pdm->escape_column_name(PortfolioLocation :: PROPERTY_RIGHT_VALUE) . ' - ' . $pdm->quote($delta));
            $res = $pdm->update_objects(PortfolioLocation :: get_table_name(), $properties, $condition);

            if (!$res)
            {
                return false;
            }

            return true;

        }



        

}
?>