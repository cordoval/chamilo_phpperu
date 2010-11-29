<?php
namespace application\weblcms\tool\maintenance;

use application\weblcms\ContentObjectPublication;
use application\weblcms\WeblcmsDataManager;
use common\libraries\ObjectTableOrder;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use repository\CpObjectExport;
use application\weblcms\Tool;

require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';

//require_once Path :: get_repository_path() . '/lib/export/cp/cp_export.class.php';

/**
 * This form can be used to let the user select publications in the course.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class CpExportSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{

    function buildForm()
    {
        $defaults = array();
        $datamanager = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
        $publications_set = $datamanager->retrieve_content_object_publications($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_TOOL, SORT_ASC));
        $publications = array();
        while ($publication = $publications_set->next_result())
        {
            $publications[$publication->get_tool()][] = $publication;
        }

        $this->addElement('html', '<h3>' . Translation :: get('Publications') . '</h3>');

        foreach ($publications as $tool => $tool_publications)
        {
            $label = Translation :: get('TypeName', null, Tool :: get_tool_type_namespace($tool));
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

        $this->addFormRule(array('PublicationSelectionMaintenanceWizardPage', 'count_selected_publications'));

        $prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES));
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES) . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->setDefaults($defaults);
        $this->_formBuilt = true;
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