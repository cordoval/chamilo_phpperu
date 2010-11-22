<?php
/**
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to check if a username is available
 */
class HTML_QuickForm_Rule_UsernameAvailable extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is available
     * @see HTML_QuickForm_Rule
     * @param string $username Wanted username
     * @param string $current_username
     * @return boolean True if username is available
     */
    function validate($username, $current_username = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(User :: PROPERTY_USERNAME, $username);

        if (! is_null($current_username))
        {
            $conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USERNAME, $current_username));
        }

        $condition = new AndCondition($conditions);
        $count = UserDataManager :: get_instance()->count_users($condition);

        return $count == 0;
    }
}
?>