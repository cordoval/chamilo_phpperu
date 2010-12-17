<?php
/**
 * $Id: user_group_finder.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.formvalidator.Element
 */

/**
 * AJAX-based tree search and multiselect element. Use at your own risk.
 * @author Tim De Pauw
 */
class User_Group_finder extends ElementFinder
{

    public function User_Group_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array (), $options = array())
    {
        parent ::ElementFinder($elementName, $elementLabel, $search_url, $locale, $default_values, $options);
    }

    public function get_value()
    {
        $results = array();
//        $results['user'] = array();
//        $results['group'] = array();
//        $results['platform'] = array();

        $values = $this->get_active_elements();

        // Process the array values so we end up with a 2-dimensional array (users and groups)

        foreach ($values as $value)
        {
            $value = explode('_', $value['id']);

            if (!isset($results[$value[0]]) || !is_array($results[$value[0]]))
            {
                $results[$value[0]] = array();
            }

            $results[$value[0]][] = $value[1];
        }
        
        return $results;
    }
}
?>