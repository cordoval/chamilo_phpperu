<?php
/**
 * $Id: course_validator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.validator
 */
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_user_path() . 'lib/data_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/category_manager/course_category.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course_user_relation.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course_group/course_group.class.php';

/**
 * Description of course_validatorclass
 * The purpose of this class is to validate the given Course/CourseUserRelation-properties:
 * -To check if all the required properties are there
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * Each validator also generates an error message if something goes wrong,
 * together with an error source to keep track of what was happening when something went wrong.
 * This is especially useful during large batch assignments, so you can easily see which entry produces errors.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
class CourseValidator extends Validator
{
    private $udm;
    private $wdm;

    function CourseValidator()
    {
        $this->udm = DatabaseUserDataManager :: get_instance();
        $this->wdm = DatabaseWeblcmsDataManager :: get_instance();
    }

    private function get_required_course_property_names()
    {
        return array(Course :: PROPERTY_CATEGORY, Course :: PROPERTY_SHOW_SCORE, Course :: PROPERTY_VISUAL);
    }

    private function get_required_course_rel_user_property_names()
    {
        return array(CourseUserRelation :: PROPERTY_COURSE, CourseUserRelation :: PROPERTY_USER, CourseUserRelation :: PROPERTY_STATUS, CourseUserRelation :: PROPERTY_COURSE_GROUP, CourseUserRelation :: PROPERTY_TUTOR);
    }

    private function get_required_course_group_property_names()
    {
        return array(CourseGroup :: PROPERTY_COURSE_CODE, CourseGroup :: PROPERTY_NAME, CourseGroup :: PROPERTY_SELF_REG, CourseGroup :: PROPERTY_SELF_UNREG);
    }

    function validate_retrieve(&$courseProperties)
    {
        $this->errorSource = Translation :: get('ErrorRetreivingCourse');
        
        if ($courseProperties[visual_code] == null)
        {
            $this->errorMessage = Translation :: get('CoursenameIsRequired');
            return false;
        }
        
        return true;
    }

    function validate_create(&$courseProperties)
    {
        $this->errorSource = Translation :: get('ErrorCreatingCourse') . ': ' . $courseProperties[Course :: PROPERTY_VISUAL];
        
        if (! $this->validate_properties($courseProperties, $this->get_required_course_property_names()))
            return false;
        
        if (! $this->validate_property_names($courseProperties, Course :: get_default_property_names()))
            return false;
        
        if (! $this->wdm->is_visual_code_available($courseProperties[Course :: PROPERTY_VISUAL]))
        {
            $this->errorMessage = Translation :: get('VisualCodeIsAlreadyUsed');
            return false; //visual code is required and must be different from existing values
        }
        
        $var = $this->get_category_id($courseProperties[Course :: PROPERTY_CATEGORY]);
        if ($var === false) //checks type and contents
        {
            $this->errorMessage = Translation :: get('CourseCategory') . ' ' . $courseProperties[Course :: PROPERTY_CATEGORY] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_CATEGORY] = $var;
        }
        
        $var = $this->get_person_id($courseProperties[Course :: PROPERTY_TITULAR]);
        if ($var === false)
        {
            $this->errorMessage = Translation :: get('CourseTitular') . ' ' . $courseProperties[Course :: PROPERTY_TITULAR] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_TITULAR] = $var;
        }
        
        if (! $this->check_quota($courseProperties))
            return false;
        
        return true;
    }

    function validate_update(&$courseProperties)
    {
        $this->errorSource = Translation :: get('ErrorUpdatingCourse') . ': ' . $courseProperties[Course :: PROPERTY_VISUAL];
        
        if (! $this->validate_properties($courseProperties, $this->get_required_course_property_names()))
            return false;
        
        if (! $this->validate_property_names($courseProperties, Course :: get_default_property_names()))
            return false;
        
        $var = $this->get_course_id($courseProperties[Course :: PROPERTY_VISUAL]);
        if ($var === false)
        {
            $this->errorMessage = Translation :: get('CourseVisualCode') . ' ' . $courseProperties[Course :: PROPERTY_VISUAL] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_ID] = $var;
        }
        
        $var = $this->get_person_id($courseProperties[Course :: PROPERTY_TITULAR]);
        if ($var === false)
        {
            $this->errorMessage = Translation :: get('CourseTitular') . ' ' . $courseProperties[Course :: PROPERTY_TITULAR] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_TITULAR] = $var;
        }
        
        if (! $this->check_quota($courseProperties))
            return false;
        
        return true;
    }

    function validate_delete(&$courseProperties)
    {
        $this->errorSource = Translation :: get('ErrorDeletingCourse') . ': ' . $courseProperties[Course :: PROPERTY_VISUAL];
        
        if (! $this->validate_property_names($courseProperties, Course :: get_default_property_names()))
            return false;
        
        $var = $this->get_course_id($courseProperties[Course :: PROPERTY_VISUAL]);
        if ($var === false)
        {
            $this->errorMessage = Translation :: get('CourseVisualCode') . ' ' . $courseProperties[Course :: PROPERTY_VISUAL] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_ID] = $var;
        }
        
        return true;
    }

    function validate_subscribe_user(&$input_course_rel_user)
    {
        $this->errorSource = Translation :: get('ErrorSubscribingUser') . ' ' . $input_course_rel_user[user_id] . ' ' . Translation :: get('toCourse') . ' ' . $input_course_rel_user[CourseUserRelation :: PROPERTY_COURSE_GROUP];
        
        if (! $this->validate_properties($input_course_rel_user, $this->get_required_course_rel_user_property_names()))
            return false;
        
        if (! $this->validate_property_names($input_course_rel_user, CourseUserRelation :: get_default_property_names()))
            return false;
        
        $var = $this->get_person_id($input_course_rel_user[user_id]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseUser') . ' ' . $input_course_rel_user[user_id] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[user_id] = $var;
        
        $var = $this->get_person_id($input_course_rel_user[tutor_id]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseTitular') . ' ' . $input_course_rel_user[tutor_id] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[tutor_id] = $var;
        
        $var = $this->get_course_group_id($input_course_rel_user[CourseUserRelation :: PROPERTY_COURSE_GROUP]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseGroup') . ' ' . $input_course_rel_user[CourseUserRelation :: PROPERTY_COURSE_GROUP] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[CourseUserRelation :: PROPERTY_COURSE_GROUP] = $var;
        
        $var = $this->get_course_id($input_course_rel_user[course_code]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('Course') . ' ' . $input_course_rel_user[course_code] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[course_code] = $var;
        
        return $this->validate_subscribe($input_course_rel_user[course_code]);
    }

    function validate_unsubscribe_user(&$input_course_rel_user)
    {
        $this->errorSource = Translation :: get('ErrorSubscribingUser') . ' ' . $input_course_rel_user[user_id] . ' ' . Translation :: get('toCourse') . ' ' . $input_course_rel_user[CourseUserRelation :: PROPERTY_COURSE_GROUP];
        
        if (! $this->validate_properties($input_course_rel_user, $this->get_required_course_rel_user_property_names()))
            return false;
        
        if (! $this->validate_property_names($input_course_rel_user, CourseUserRelation :: get_default_property_names()))
            return false;
        
        $var = $this->get_person_id($input_course_rel_user[user_id]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseUser') . ' ' . $input_course_rel_user[user_id] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[user_id] = $var;
        
        $var = $this->get_person_id($input_course_rel_user[tutor_id]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseTitular') . ' ' . $input_course_rel_user[tutor_id] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[tutor_id] = $var;
        
        $var = $this->get_course_id($input_course_rel_user[course_code]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('Course') . ' ' . $input_course_rel_user[course_code] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_rel_user[course_code] = $var2;
        
        return $this->validate_unsubscribe($input_course_rel_user[course_code]);
    
    }

    function validate_subscribe_group(&$input_course_group)
    {
        $this->errorSource = Translation :: get('ErrorSubscribingGroup') . ' ' . $input_course_group[CourseGroup :: PROPERTY_NAME] . ' ' . Translation :: get('toCourse') . ' ' . $input_course_group[CourseGroup :: PROPERTY_COURSE_CODE];
        
        if (! $this->validate_properties($input_course_group, $this->get_required_course_group_property_names()))
            return false;
        
        if (! $this->validate_property_names($input_course_group, CourseGroup :: get_default_property_names()))
            return false;
        
        $var = $this->get_course_id($input_course_group[course_code]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('Course') . ' ' . $input_course_group[course_code] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_group[course_code] = $var;
        
        return $this->validate_subscribe($input_course_group[course_code]);
    }

    function validate_unsubscribe_group(&$input_course_group)
    {
        $this->errorSource = Translation :: get('ErrorUnsubscribingGroup') . ' ' . $input_course_group[CourseGroup :: PROPERTY_NAME] . ' ' . Translation :: get('toCourse') . ' ' . $input_course_group[CourseGroup :: PROPERTY_COURSE_CODE];
        
        if (! $this->validate_properties($input_course_group, $this->get_required_course_group_property_names()))
            return false;
        
        if (! $this->validate_property_names($input_course_group, CourseGroup :: get_default_property_names()))
            return false;
        
        $var = $this->get_course_id($input_course_group[course_code]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('Course') . ' ' . $input_course_group[course_code] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_group[course_code] = $var;
        
        $var = $this->get_course_group_id($input_course_group[name]);
        if ($var == false)
        {
            $this->errorMessage = Translation :: get('CourseGroup') . ' ' . $input_course_rel_user[course_code] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course_group[id] = $var;
        
        return $this->validate_unsubscribe($input_course_group[course_code]);
    }

    private function validate_subscribe(&$course_code)
    {
        $course = $this->wdm->retrieve_course($course_code);
        if (! empty($course))
        {
            $subscribe = $course->get_default_property('subscribe');
            if ($subscribe == 1) //allowed to subscribe
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    
    }

    private function validate_unsubscribe(&$course_code)
    {
        $course = $this->wdm->retrieve_course($course_code);
        
        if (! empty($course))
        {
            $unsubscribe = $course->get_default_property('unsubscribe');
            if ($unsubscribe == 1) //allowed to unsubscribe
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    
    }

    function validate_get_user_courses(&$input_user)
    {
        if (empty($input_user[USER :: PROPERTY_USERNAME]))
            return false;
        
        $user = $this->get_person_id($input_user[USER :: PROPERTY_USERNAME]);
        if ($user === false)
            return false;
        else
            $input_user[USER :: PROPERTY_USER_ID] = $user;
        
        return true;
    }

    function validate_get_course_users(&$input_course)
    {
        if (empty($input_course[Course :: PROPERTY_VISUAL]))
            return false;
        
        $course = $this->get_course_id($input_course[Course :: PROPERTY_VISUAL]);
        if ($course === false)
            return false;
        else
            $input_course[Course :: PROPERTY_ID] = $course;
        
        return true;
    }

    private function get_person_id($person_name)
    {
        $user = $this->udm->retrieve_user_by_username($person_name);
        if (! empty($user))
        {
            return $user->get_id();
        }
        else
        {
            return false;
        }
    }

    private function get_course_id($visual_code)
    {
        $course = $this->wdm->retrieve_course_by_visual_code($visual_code);
        if (! empty($course))
        {
            return $course->get_default_property(Course :: PROPERTY_ID);
        }
        else
        {
            return false;
        }
    }

    private function get_course_group_id($group_name)
    {
        $group = $this->wdm->retrieve_course_group_by_name($group_name);
        if (! empty($group))
        {
            return $group->get_default_property(CourseGroup :: PROPERTY_ID);
        }
        else
        {
            return false;
        }
    }

    private function get_category_id($category_name)
    {
        $categories = $this->wdm->retrieve_course_categories(new EqualityCondition(CourseCategory :: PROPERTY_NAME, $category_name))->as_array();
        if (! empty($categories[0]))
            return $categories[0]->get_id();
        else
            return false;
    }

    private function check_quota($courseProperties)
    {
        if ($courseProperties[Course :: PROPERTY_DISK_QUOTA] < 0)
            return false;
        
        return true;
    }

    private function check_dates($courseProperties)
    {
        if ($courseProperties[Course :: PROPERTY_LAST_EDIT] > time() || $courseProperties[Course :: PROPERTY_LAST_VISIT] > time() || $courseProperties[Course :: PROPERTY_CREATION_DATE] > time() || $courseProperties[Course :: PROPERTY_EXPIRATION_DATE] < time())
            return false;
        
        return true;
    }

}
?>