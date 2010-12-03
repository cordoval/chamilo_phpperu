<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Utilities;
use repository\CpExport;
use common\libraries\FormValidator;
use application\weblcms\WeblcmsDataManager;
use common\libraries\EqualityCondition;
use application\weblcms\ContentObjectPublication;
use common\libraries\ObjectTableOrder;
use application\weblcms\Course;
use repository\CpObjectExport;

require_once dirname(__FILE__) . '/fedora_tree.class.php';
require_once Path::get_repository_path() . 'lib/export/cp/object_export/cp_object_export.class.php';

/**
 * Form used to select publications in a course.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 */
class FedoraCoursePublicationSelectionForm extends FormValidator
{
    const PARAM_COURSE_ID = FedoraExternalRepositoryManager :: PARAM_COURSE_ID;

    protected $parameters = array();

    function __construct($application, $parameters, $data = false)
    {
        parent :: __construct(__CLASS__, 'post', Redirect :: get_url($parameters));
        $this->parameters = $parameters;
        $this->build_form();
    }

    function get_course_id()
    {
        return Request :: get(self :: PARAM_COURSE_ID);
    }

    function get_course()
    {
        $id = $this->get_course_id();
        $store = Course :: get_data_manager();
        $result = $store->retrieve_course($id);
        return $result;
    }

    function build_form()
    {
        $defaults = array();
        $publications = $this->retrieve_publications();

        $this->addElement('html', '<h3>' . Translation :: get('SelectPublications') . ' (' . $this->get_course()->get_name() . ')</h3>');

        //$this->addElement('html', '<h4>' . Translation::get('Publications') . '</h4>');


        foreach ($publications as $tool => $tool_publications)
        {
            $label = Translation :: get(ucfirst($tool) . 'ToolTitle');
            foreach ($tool_publications as $index => $publication)
            {
                $label = $index == 0 ? $label : '';
                $content_object = $publication->get_content_object();
                $id = "publications[{$publication->get_id()}]";
                $accept = CpObjectExport :: accept($content_object);
                $this->addElement('checkbox', $id, $label, $content_object->get_title(), $accept ? array() : array('disabled' => 'disabled'));
                $defaults[$id] = $accept;
            }
        }

        $this->addFormRule(array($this, 'count_selected_publications'));

        //$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation::get('Previous'));


        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Next', null, Utilities::COMMON_LIBRARIES) . ' >>', array('class' => 'export'));
        $this->addGroup($buttons, 'buttons', '', '&nbsp;', false);
        //$this->setDefaultAction('next');
        $this->setDefaults($defaults);
        $this->_formBuilt = true;
    }

    function retrieve_publications()
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $publications_set = $datamanager->retrieve_content_object_publications($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_TOOL, SORT_ASC));
        $publications = array();
        while ($publication = $publications_set->next_result())
        {
            $publications[$publication->get_tool()][] = $publication;
        }
        return $publications;
    }

    /**
     * Returns the number of selected publications
     * @param array $values
     */
    function count_selected_publications($values)
    {
        if (isset($values['publications']) || isset($values['course_sections']))
        {
            return true;
        }
        return array('buttons' => Translation :: get('SelectPublications'));
    }

}
?>