<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Session;
use common\libraries\FormValidator;
use common\libraries\EqualityCondition;
use application\weblcms\CourseUserRelation;
use application\weblcms\WeblcmsDataManager;

require_once Path :: get_application_path() . 'weblcms/php/lib/course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/fedora_tree.class.php';

/**
 * Form used to select a course.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraCourseSelectionForm extends FormValidator
{

    const PARAM_COURSE_ID = FedoraExternalRepositoryManager :: PARAM_COURSE_ID;

    private $parameters = array();
    private $data = false;

    function __construct($application, $parameters, $data = false)
    {
        parent :: __construct(__CLASS__, 'post', Redirect :: get_url($parameters));
        $this->parameters = $parameters;
        $this->data = $data;
        $this->build_form();
    }

    function retrieve_courses()
    {
        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, Session :: get_user_id(), CourseUserRelation :: get_table_name());
        return WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition);
    }

    function get_course_id()
    {
        return Request :: get(self :: PARAM_COURSE_ID);
    }

    function validate()
    {
        $course_id = $this->get_course_id();
        return $course_id ? true : false;
    }

    function build_form()
    {
        $this->addElement('html', '<h3>' . Translation :: get('SelectCourse') . '</h3>');

        $tree = new FedoraTree($this->get_courses_tree());
        $html = $tree->render_as_tree();
        $this->addElement('static', 'course', Translation :: get('Course'), $html);

    //$buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('Next'), array('class' => 'positive'));
    //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation::get('Reset'), array('class' => 'normal empty'));
    //$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    private $_course_tree = false;

    protected function get_courses_tree()
    {
        if ($this->_course_tree)
        {
            return $this->_course_tree;
        }

        $rs = $this->retrieve_courses();
        $courses = array();
        $types = array();
        $this->_course_tree = array();
        while ($course = $rs->next_result())
        {
            $courses[] = $course;
            $type = $course->get_course_type();
            $types[$type->get_id()]['type'] = $type;
            $types[$type->get_id()]['courses'][] = $course;
        }

        $this->_course_tree = array();
        foreach ($types as $entry)
        {
            $type = $entry['type'];
            $courses = $entry['courses'];
            $type_node = array();
            $title = $type ? $type->get_name() : translation :: get('Typeless');
            $title = $title ? $title : translation :: get('Typeless');
            $type_node['title'] = $title;
            $type_node['class'] = 'category';
            $type_node['url'] = '#';
            $type_node['onclick'] = '';
            $type_node['sub'] = array();
            foreach ($courses as $course)
            {
                $course_node = array();
                $course_node['title'] = $course->get_name();
                $course_node['class'] = $course->get_access() ? 'lock' : 'home';
                $course_node['url'] = $this->get_url($course);
                $course_node['onclick'] = '';
                $course_node['sub'] = null;
                $type_node['sub'][] = $course_node;
            }
            $this->_course_tree[] = $type_node;
        }
        return $this->_course_tree;
    }

    protected function get_url($course)
    {
        $parameters = $this->parameters;
        $parameters[self :: PARAM_COURSE_ID] = $course->get_id();
        return Redirect :: get_url($parameters);
    }

}
















