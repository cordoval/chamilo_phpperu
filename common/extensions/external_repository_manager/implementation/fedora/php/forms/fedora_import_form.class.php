<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Filesystem;

use common\libraries\Session;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Utilities;
use common\libraries\FormValidator;
use application\weblcms\Course;
use repository\RepositoryManager;
use repository\ContentObjectCategoryMenu;
use common\libraries\OptionsMenuRenderer;
use common\libraries\EqualityCondition;
use application\weblcms\CourseUserRelation;
use application\weblcms\WeblcmsDataManager;

//require_once Path :: get_application_path() . 'lib/weblcms/course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/fedora_tree.class.php';

/**
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraImportForm extends FormValidator
{

    protected $parameters = array();
    protected $application = null;
    protected $file = false;
    protected $data = false;

    function __construct($application, $parameters, $data = false)
    {
        parent :: __construct(__CLASS__, 'post', Redirect :: get_url($parameters));
        $this->application = $application;
        $this->paramaters = $parameters;
        $this->data = $data;
        $this->build_form();
    }

    /**
     * @return FedoraExternalRepositoryManager
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     * @return User
     */
    public function get_user()
    {
        return $this->get_application()->get_user();
    }

    /**
     * Gets the categories defined in the user's repository.
     * @return array The categories.
     */
    function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_user()->get_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    function retrieve_courses()
    {
        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, Session :: get_user_id(), CourseUserRelation :: get_table_name());
        return WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition);
    }

    function build_form()
    {
        $this->build_header();
        $this->build_body();
        $this->build_footer();
    }

    protected function build_header()
    {
        $this->addElement('html', '<h3>' . Translation :: get('SelectCategory') . '</h3>');
    }

    protected function build_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    protected function build_body()
    {
        $this->add_select(RepositoryManager :: PARAM_CATEGORY_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
        $default = array(RepositoryManager :: PARAM_CATEGORY_ID => 0);

        $this->addCourse();

        $this->setDefaults($default);
    }

    function addCourse()
    {
        $key = FedoraExternalRepositoryManager :: PARAM_COURSE_ID;
        $text_name = 'course_dd[course_text]';
        $dropdown_id = 'dd';

        $courses = $this->get_courses_tree($key, $text_name, $dropdown_id);
        if (empty($courses))
        {
            return false;
        }

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'function toggle_dropdown(item)';
        $html[] = '{';
        $html[] = '	if (document.getElementById(item).style.display == \'block\')';
        $html[] = '  {';
        $html[] = '		document.getElementById(item).style.display = \'none\';';
        $html[] = '  }';
        $html[] = '	else';
        $html[] = '  {';
        $html[] = '		document.getElementById(item).style.display = \'block\';';
        $html[] = '	}';
        $html[] = '}';
        $html[] = '</script>';
        $javascript = implode('', $html);
        $group = array();
        $group[] = $this->createElement('static', '', '', $javascript);

        $onclick = 'toggle_dropdown(\'' . $dropdown_id . '\');return false;';
        $text = 'course_text';
        $group[] = $this->createElement('text', $text, Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES), array(
                "size" => "50",
                'id' => $text,
                'readonly' => 'readonly',
                'onclick' => $onclick));

        $group[] = $this->createElement('style_button', 'dd', '  ', array('class' => 'dropdown', 'onclick' => $onclick));

        $this->addElement('hidden', $key);

        $tree = new FedoraTree($courses);
        $html = $tree->render_as_tree();
        $html = '<div id="' . $dropdown_id . '" style="display:none" class="dropdown">' . $html . '</div>';
        $group[] = $this->createElement('static', '', '', $html);

        $this->addGroup($group, 'course_dd', Translation :: get('Publish'));
        return true;
    }

    private $_course_tree = false;

    protected function get_courses_tree($editor_key, $editor_text, $dropdown)
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
                $course_node['url'] = '#';
                $onclick = '';
                $onclick .= "document.getElementsByName('$editor_key').item(0).value = '{$course->get_id()}';";
                $onclick .= "document.getElementsByName('$editor_text').item(0).value = '{$course->get_name()}';";
                $onclick .= "toggle_dropdown('$dropdown');";
                $onclick .= 'return false;';
                $course_node['onclick'] = $onclick;
                $course_node['sub'] = null;
                $type_node['sub'][] = $course_node;
            }
            $this->_course_tree[] = $type_node;
        }
        return $this->_course_tree;
    }

    function get_types()
    {
        $folder = dirname(__FILE__) . '/import/';
        $folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($folders as $f)
        {
            if (strpos($f, '.svn') !== false || strpos($f, 'csv') !== false)
                continue;

            $types[$f] = Translation :: get('Type' . $f);
        }

        return $types;
    }
}
?>