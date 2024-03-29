<?php
namespace reporting;

use repository\ContentObject;

use user\User;
use user\UserDataManager;
use user\UserManager;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Validator;

use application\weblcms\CourseUserRelation;
use application\weblcms\WeblcmsManager;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\Course;
use application\weblcms\CourseCategory;
use application\weblcms\CourseGroup;

/**
 * $Id: reporting_validator.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.validator
 * @author Michael Kyndt
 */

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
class ReportingValidator extends Validator
{
    private $udm;
    private $wdm;

    function __construct()
    {
        $this->udm = UserDataManager :: get_instance();
        $this->wdm = WeblcmsDataManager :: get_instance();
    }

    private function get_required_course_property_names()
    {
        return array(Course :: PROPERTY_CATEGORY, Course :: PROPERTY_SHOW_SCORE, Course :: PROPERTY_VISUAL);
    }

    private function get_required_course_rel_user_property_names()
    {
        return array(
                CourseUserRelation :: PROPERTY_COURSE,
                CourseUserRelation :: PROPERTY_USER,
                CourseUserRelation :: PROPERTY_STATUS,
                CourseUserRelation :: PROPERTY_COURSE_GROUP,
                CourseUserRelation :: PROPERTY_TUTOR);
    }

    private function get_required_course_group_property_names()
    {
        return array(
                CourseGroup :: PROPERTY_COURSE_CODE,
                CourseGroup :: PROPERTY_NAME,
                CourseGroup :: PROPERTY_SELF_REG,
                CourseGroup :: PROPERTY_SELF_UNREG);
    }

    function validate_retrieve(&$object)
    {
    }

    function validate_create(&$object)
    {
    }

    function validate_update(&$object)
    {
    }

    function validate_delete(&$object)
    {
    }

    function validate_get_user_courses(&$input_user)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingUserCourses', null, WeblcmsManager :: APPLICATION_NAME);

        if (empty($input_user[User :: PROPERTY_USERNAME]))
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired', null, UserManager :: APPLICATION_NAME);
            return false;
        }

        $user = $this->get_person_id($input_user[User :: PROPERTY_USERNAME]);
        if ($user === false)
        {
            $this->errorMessage = Translation :: get('User', null, UserManager :: APPLICATION_NAME) . ' ' . $inputUser[User :: PROPERTY_USERNAME] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_user[User :: PROPERTY_ID] = $user;

        return true;
    }

    function validate_get_course_users(&$input_course)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingCourseUsers', null, WeblcmsManager :: APPLICATION_NAME);

        if (empty($input_course[Course :: PROPERTY_VISUAL]))
        {
            $this->errorMessage = Translation :: get('CourseVisualCodeIsRequired', null, WeblcmsManager :: APPLICATION_NAME);
            return false;
        }

        $course = $this->get_course_id($input_course[Course :: PROPERTY_VISUAL]);
        if ($course === false)
        {
            $this->errorMessage = Translation :: get('Course', null, WeblcmsManager :: APPLICATION_NAME) . ' ' . $inputCourse[Course :: PROPERTY_VISUAL] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course[Course :: PROPERTY_ID] = $course;

        return true;
    }

    function validate_get_new_course_publications(&$input_course)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingNewCoursePublications', null, WeblcmsManager :: APPLICATION_NAME);

        if (empty($input_course[CourseUserRelation :: PROPERTY_COURSE]))
        {
            $this->errorMessage = Translation :: get('CourseVisualCodeIsRequired', null, WeblcmsManager :: APPLICATION_NAME);
            return false;
        }

        if (empty($input_course[CourseUserRelation :: PROPERTY_USER]))
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired', null, UserManager :: APPLICATION_NAME);
            return false;
        }

        $course = $this->get_course_id($input_course[CourseUserRelation :: PROPERTY_COURSE]);
        if ($course === false)
        {
            $this->errorMessage = Translation :: get('Course', null, WeblcmsManager :: APPLICATION_NAME) . ' ' . $input_course[CourseUserRelation :: PROPERTY_COURSE] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course[CourseUserRelation :: PROPERTY_COURSE] = $course;

        $user = $this->get_person_id($input_course[CourseUserRelation :: PROPERTY_USER]);
        if ($user === false)
        {
            $this->errorMessage = Translation :: get('User', null, UserManager :: APPLICATION_NAME) . ' ' . $input_course[CourseUserRelation :: PROPERTY_USER] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course[CourseUserRelation :: PROPERTY_USER] = $user;

        return true;
    }

    function validate_get_new_publications_in_course_tool(&$input_course)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingNewPublicationsInCourseTool', null, WeblcmsManager :: APPLICATION_NAME);

        if (empty($input_course[ContentObjectPublicationUser :: PROPERTY_COURSE_ID]))
        {
            $this->errorMessage = Translation :: get('CourseVisualCodeIsRequired', null, WeblcmsManager :: APPLICATION_NAME);
            return false;
        }

        if (empty($input_course[ContentObjectPublicationUser :: PROPERTY_USER_ID]))
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired', null, UserManager :: APPLICATION_NAME);
            return false;
        }

        if (empty($input_course[ContentObjectPublicationUser :: PROPERTY_TOOL]))
        {
            $this->errorMessage = Translation :: get('ToolNameIsRequired', null, WeblcmsManager :: APPLICATION_NAME);
            return false;
        }

        $course = $this->get_course_id($input_course[ContentObjectPublicationUser :: PROPERTY_COURSE_ID]);
        if ($course === false)
        {
            $this->errorMessage = Translation :: get('Course', null, WeblcmsManager :: APPLICATION_NAME) . ' ' . $input_course[ContentObjectPublicationUser :: PROPERTY_COURSE_ID] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course[ContentObjectPublicationUser :: PROPERTY_COURSE_ID] = $course;

        $user = $this->get_person_id($input_course[ContentObjectPublicationUser :: PROPERTY_USER_ID]);
        if ($user === false)
        {
            $this->errorMessage = Translation :: get('User', null, UserManager :: APPLICATION_NAME) . ' ' . $input_course[ContentObjectPublicationUser :: PROPERTY_USER_ID] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_course[ContentObjectPublicationUser :: PROPERTY_USER_ID] = $user;

        return true;
    }

    function validate_get_publications_for_user($user)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingNewPublicationsInCourseTool');

        if (empty($user[User :: PROPERTY_USERNAME]))
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired', null, UserManager :: APPLICATION_NAME);
            return false;
        }

        $user = $this->get_person_id($user[User :: PROPERTY_USERNAME]);
        if ($user === false)
        {
            $this->errorMessage = Translation :: get('User', null, UserManager :: APPLICATION_NAME) . ' ' . $user[User :: PROPERTY_USERNAME] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $user[User :: PROPERTY_ID] = $user;

        return true;
    }

    function validate_get_publications_for_course($course)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingNewPublicationsInCourseTool');

        if (empty($course[Course :: PROPERTY_VISUAL]))
        {
            $this->errorMessage = Translation :: get('CourseVisualCodeIsRequired');
            return false;
        }

        $course = $this->get_course_id($course[Course :: PROPERTY_VISUAL]);
        if ($course === false)
        {
            $this->errorMessage = Translation :: get('Course', null, WeblcmsManager :: APPLICATION_NAME) . ' ' . $course[Course :: PROPERTY_VISUAL] . ' ' . Translation :: get('WasNotFoundInTheDatabase');
            return false;
        }
        else
            $course[Course :: PROPERTY_ID] = $course;

        return true;
    }

    function transform_publication_to_human_format(&$publication)
    {
        $publication[ContentObject :: PROPERTY_OWNER_ID] = $this->udm->retrieve_user($publication[ContentObject :: PROPERTY_OWNER_ID])->get_username();
        $publication[ContentObject :: PROPERTY_CREATION_DATE] = date('l jS \of F Y h:i:s A', $publication[ContentObject :: PROPERTY_CREATION_DATE]);
        $publication[ContentObject :: PROPERTY_MODIFICATION_DATE] = date('l jS \of F Y h:i:s A', $publication[ContentObject :: PROPERTY_MODIFICATION_DATE]);
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
}
?>