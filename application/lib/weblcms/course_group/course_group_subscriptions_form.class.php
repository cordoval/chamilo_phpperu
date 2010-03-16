<?php
/**
 * $Id: course_group_subscriptions_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course_group
 */
require_once dirname(__FILE__) . '/course_group.class.php';

class CourseGroupSubscriptionsForm extends FormValidator
{

    private $parent;
    private $course_group;
    private $form_type;

    function CourseGroupSubscriptionsForm($course_group, $action, $parent)
    {
        parent :: __construct('course_settings', 'post', $action);
        $this->course_group = $course_group;
        $this->parent = $parent;
        $this->wdm = WeblcmsDataManager :: get_instance();

        $this->build_basic_form();
    }

    function build_basic_form()
    {
        $subscribed_users = $this->wdm->retrieve_course_group_users($this->course_group);

        $relation_conditions = array();
        $relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->parent->get_course()->get_id());
        $relation_condition = new AndCondition($relation_conditions);

        $all_users = $this->wdm->retrieve_course_user_relations($relation_condition);

        $udm = UserDataManager :: get_instance();

        while ($user = $all_users->next_result())
        {
            $id = $user->get_user();
            $name = $udm->retrieve_user($id)->get_fullname();
            $all[$id] = $name;
        }

        while ($sub = $subscribed_users->next_result())
        {
            $id = $sub->get_id();
            $subs[$id] = $id;
        }
        $this->subs = $subs;

        $this->addElement('advmultiselect', 'users', Translation :: get('SelectGroupUsers'), $all, array('style' => 'width:300px; height: 250px;'));
        $this->setDefaults(array('users' => $subs));






//        $url = Path :: get(WEB_PATH) . 'application/lib/weblcms/xml_feeds/xml_course_user_feed.php';
//
//        $course_group_users = $this->wdm->retrieve_course_group_users($this->course_group);
//        $defaults = array();
//        $current = array();
//
//        if ($course_group_users)
//        {
//	        while($course_group_user = $course_group_users->next_result())
//	        {
//	        	$current[$course_group_user->get_id()] = array('id' => $course_group_user->get_id(), 'title' => htmlspecialchars($course_group_user->get_fullname()), 'description' => htmlspecialchars($course_group_user->get_username()), 'classes' => 'type type_user');
//	        	$defaults[$course_group_user->get_id()] = array('title' => $course_group_user->get_fullname(), 'description' => $course_group_user->get_username(), 'class' => 'user');
//	        }
//        }
//
//        $locale = array();
//        $locale['Display'] = Translation :: get('SubscribeUsers');
//        $locale['Searching'] = Translation :: get('Searching');
//        $locale['NoResults'] = Translation :: get('NoResults');
//        $locale['Error'] = Translation :: get('Error');
//
//        $elem = $this->addElement('element_finder', 'moderators', Translation :: get('SubscribeUsers'), $url, $locale, $current, array('load_elements' => true));
//		$elem->setDefaults($defaults);



        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Subscribe'), array('class' => 'positive subscribe'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
    }

    function update_course_group_subscriptions()
    {
        $values = $this->exportValues();
        $subs = $this->subs;

        foreach ($values['users'] as $value)
        {
            if (! array_key_exists($value, $subs))
            {
                $creation[] = $value;
            }
            else
            {
                unset($subs[$value]);
            }
        }

        if (($this->course_group->get_max_number_of_members() > 0) && (count($values['users']) > $this->course_group->get_max_number_of_members()))
        {
            return false;
        }

        $succes = true;

        if (count($subs) > 0)
        {
            $succes = $this->course_group->unsubscribe_users($subs);
        }

        if (count($creation) > 0)
        {
            $succes &= $this->course_group->subscribe_users($creation);
        }

        return $succes;
    }
}
?>