<?php
/**
 * $Id: user_view.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 *  @author Sven Vanpoucke
 */

class UserView extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties of all user_views.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_USER_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * Returns the name of this user_view.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this user_view.
     * @return String The description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the name of this user_view.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the description of this user_view.
     * @param String $description the description.
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function create($values)
    {
        //dump($values);
        $gdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(self :: PROPERTY_NAME, $this->get_name());
        $views = $gdm->count_user_views($condition);
        if ($views > 0)
        {
            $this->add_error(Translation :: get('UserViewNameNotUnique'));
        	return false;
        }

        $success = $gdm->create_user_view($this);

        $registrations = $gdm->get_registered_types();
        foreach ($registrations as $registration)
        {
            $uvrlo = new UserViewRelContentObject();
            $uvrlo->set_view_id($this->get_id());
            $uvrlo->set_content_object_type($registration);

            if (in_array($registration, $values))
                $uvrlo->set_visibility(1);
            else
                $uvrlo->set_visibility(0);

            $uvrlo->create();
        }

        return $success;
    }
    
    function update($values)
    {
    	$gdm = RepositoryDataManager :: get_instance();
    	$conditions[] = new EqualityCondition(self :: PROPERTY_NAME, $this->get_name());
    	$conditions[] = new EqualityCondition(self :: PROPERTY_NAME, $this->get_name());
    	$condition = new AndCondition($conditions);
    	
        $views = $gdm->count_user_views($condition);
        if ($views > 0)
        {
            $this->add_error(Translation :: get('UserViewNameNotUnique'));
        	return false;
        }
        
        return parent :: update();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>