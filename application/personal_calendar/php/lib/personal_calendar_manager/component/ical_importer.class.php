<?php
namespace application\personal_calendar;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\FormValidator;
use common\libraries\EqualityCondition;
use repository\RepositoryCategory;
use common\libraries\AndCondition;
use repository\RepositoryDataManager;
use repository\ContentObjectImport;
/**
 * $Id: ical_importer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_rights.class.php';

class PersonalCalendarManagerIcalImporterComponent extends PersonalCalendarManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        if(! PersonalCalendarRights :: is_allowed_in_personal_calendar_subtree(PersonalCalendarRights :: RIGHT_SHARE, PersonalCalendarRights :: get_personal_calendar_subtree_root()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $form = $this->build_importing_form();
        if ($form->validate())
        {
            $object = $this->import_ical($form);

            $this->redirect(Translation :: get('IcalImported'), false, array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_CREATE_PUBLICATION, RepoViewer::PARAM_ID => $object, RepoViewer::PARAM_ACTION => RepoViewer::ACTION_PUBLISHER));
        }
        else
        {
            $this->display_header(null, true);

            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_importing_form()
    {
        $url = $this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_IMPORT_ICAL));
        $form = new FormValidator('ical_import', 'post', $url);

        $this->categories[0] = Translation :: get('MyRepository');
        $this->retrieve_categories(0, 1);
        $categories = $this->categories;

        $form->addElement('select', 'category', Translation :: get('Category'), $categories);
        $form->addElement('file', 'file', sprintf(Translation :: get('FileName'), ini_get('upload_max_filesize')));

        $allowed_upload_types = array('ics');
        $form->addRule('file', Translation :: get('OnlyIcsAllowed'), 'filetype', $allowed_upload_types);

        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function import_ical($form)
    {
        $values = $form->exportValues();
        $category = $values['category'];

        $file = $_FILES['file'];
        $user = $this->get_user();

        $importer = ContentObjectImport :: factory('ical', $file, $user, $category);
        $result = $importer->import_content_object();
        return $result;
    }

    private $categories;

    function retrieve_categories($parent_id, $level)
    {
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent_id);
        $condition = new AndCondition($conditions);

        $category_list = RepositoryDataManager :: get_instance()->retrieve_categories($condition);

        while ($category = $category_list->next_result())
        {
            $this->categories[$category->get_id()] = str_repeat('--', $level) . ' ' . $category->get_name();
            $this->retrieve_categories($category->get_id(), ($level + 1));
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_ical_importer');
    }

}
?>