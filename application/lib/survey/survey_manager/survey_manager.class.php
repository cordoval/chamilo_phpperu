<?php

require_once dirname(__FILE__) . '/../survey_data_manager.class.php';

require_once Path :: get_application_path() . 'lib/survey/survey_rights.class.php';
require_once Path :: get_application_path() . 'lib/survey/testcase_manager/testcase_manager.class.php';

require_once dirname(__FILE__) . '/component/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManager extends WebApplication
{

	const APPLICATION_NAME = 'survey';

	const PARAM_SURVEY_PUBLICATION = 'survey_publication';
	const PARAM_SURVEY = 'survey';
	const PARAM_SURVEY_PARTICIPANT = 'survey_participant';
	const PARAM_SURVEY_PAGE = 'survey_page';
	const PARAM_SURVEY_QUESTION = 'survey_question';
	const PARAM_MAIL_PARTICIPANTS = 'mail_participant';
	const PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS = 'delete_selected_survey_publications';

	const PARAM_TESTCASE = 'testcase';

	const PARAM_TARGET = 'target_users_and_groups';
	const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
	const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
	
	const ACTION_DELETE_SURVEY_PUBLICATION = 'delete';
	const ACTION_EDIT_SURVEY_PUBLICATION = 'edit';
	const ACTION_CREATE_SURVEY_PUBLICATION = 'create';
	const ACTION_BROWSE_SURVEY_PUBLICATIONS = 'browse';
	const ACTION_BROWSE_SURVEY_PAGES = 'browse_pages';
	const ACTION_BROWSE_SURVEY_PAGE_QUESTIONS = 'browse_page_questions';
	const ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES = 'manage_categories';
	const ACTION_VIEW_SURVEY_PUBLICATION = 'view';
	const ACTION_VIEW_SURVEY_PUBLICATION_RESULTS = 'view_results';
	const ACTION_REPORTING_FILTER = 'reporting_filter';
	const ACTION_REPORTING = 'reporting';
	const ACTION_EXCEL_EXPORT = 'excel_export';
	const ACTION_QUESTION_REPORTING = 'question_reporting';

	const ACTION_IMPORT_SURVEY = 'import_survey';
	const ACTION_EXPORT_SURVEY = 'export_survey';
	const ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY = 'change_visibility';
	const ACTION_MOVE_SURVEY_PUBLICATION = 'move';
	const ACTION_EXPORT_RESULTS = 'export_results';
	const ACTION_DOWNLOAD_DOCUMENTS = 'download_documents';

	const ACTION_MAIL_SURVEY_PARTICIPANTS = 'mail_survey_participants';

	const ACTION_BUILD_SURVEY = 'build';
	const ACTION_TESTCASES = 'testcases';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
	function SurveyManager($user = null)
	{
		parent :: __construct($user);
		//$this->parse_input_from_table();
	}

	/**
	 * Run this survey manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_SURVEY_PUBLICATIONS :
				$component = $this->create_component('Browser');
				break;
			case self :: ACTION_BROWSE_SURVEY_PAGES :
				$component = $this->create_component('PageBrowser');
				break;
			case self :: ACTION_BROWSE_SURVEY_PAGE_QUESTIONS :
				$component = $this->create_component('QuestionBrowser');
				break;
			case self :: ACTION_TESTCASES :
				$component = $this->create_component('Testcase');
				break;
			case self :: ACTION_DELETE_SURVEY_PUBLICATION :
				$component = $this->create_component('Deleter');
				break;
			case self :: ACTION_EDIT_SURVEY_PUBLICATION :
				$component = $this->create_component('Updater');
				break;
			case self :: ACTION_CREATE_SURVEY_PUBLICATION :
				$component = $this->create_component('Creator');
				break;
			case self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES :
				$component = $this->create_component('CategoryManager');
				break;
			case self :: ACTION_VIEW_SURVEY_PUBLICATION :
				$component = $this->create_component('Viewer');
				break;
			case self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS :
				$component = $this->create_component('ResultsViewer');
				break;
			case self :: ACTION_REPORTING_FILTER :
				$component = $this->create_component('ReportingFilter');
				break;
			case self :: ACTION_REPORTING :
				$component = $this->create_component('Reporting');
				break;
			case self :: ACTION_QUESTION_REPORTING :
				$component = $this->create_component('QuestionReporting');
				break;
			case self :: ACTION_IMPORT_SURVEY :
				$component = $this->create_component('SurveyImporter');
				break;
			case self :: ACTION_EXPORT_SURVEY :
				$component = $this->create_component('SurveyExporter');
				break;
			case self :: ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY :
				$component = $this->create_component('VisibilityChanger');
				break;
			case self :: ACTION_MOVE_SURVEY_PUBLICATION :
				$component = $this->create_component('Mover');
				break;
			case self :: ACTION_EXPORT_RESULTS :
				$component = $this->create_component('ResultsExporter');
				break;
			case self :: ACTION_DOWNLOAD_DOCUMENTS :
				$component = $this->create_component('DocumentDownloader');
				break;
			case self :: ACTION_MAIL_SURVEY_PARTICIPANTS :
				$component = $this->create_component('Mailer');
				break;
			case self :: ACTION_BUILD_SURVEY :
				$component = $this->create_component('Builder');
				break;
			case self :: ACTION_EXCEL_EXPORT :
				$component = $this->create_component('SurveyExcelExporter');
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_SURVEY_PUBLICATIONS);
				$component = $this->create_component('Browser');

		}
		$component->run();
	}

//	private function parse_input_from_table()
//	{
//		if (isset($_POST['action']))
//		{
//
//			if (isset($_POST[SurveyPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
//			{
//				$selected_ids = $_POST[SurveyPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
//			}
//
//			if (empty($selected_ids))
//			{
//				$selected_ids = array();
//			}
//			elseif (! is_array($selected_ids))
//			{
//				$selected_ids = array($selected_ids);
//			}
//
//			switch ($_POST['action'])
//			{
//				case self :: PARAM_REPORTING_SELECTED_SURVEY_PUBLICATIONS :
//					$this->set_action(self :: ACTION_REPORTING);
//					$_GET[self :: PARAM_SURVEY_PUBLICATION] = $selected_ids;
//					break;
//				case self :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS :
//					$this->set_action(self :: ACTION_DELETE_SURVEY_PUBLICATION);
//					$_GET[self :: PARAM_SURVEY_PUBLICATION] = $selected_ids;
//					break;
//				case self :: PARAM_MAIL_PARTICIPANTS :
//					$this->set_action(self :: ACTION_MAIL_SURVEY_PARTICIPANTS);
//					$_GET[self :: PARAM_SURVEY_PUBLICATION] = $selected_ids;
//					break;
//			}
//
//		}
//	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving


	function count_survey_participant_trackers($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_participant_trackers($condition);
	}

	function retrieve_survey_participant_trackers($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_participant_trackers($condition, $offset, $count, $order_property);
	}

	function count_survey_publications($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_publications($condition);
	}

	function retrieve_survey_publications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publications($condition, $offset, $count, $order_property);
	}

	function retrieve_survey_publication($id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
	}

	function count_survey_publication_groups($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_publication_groups($condition);
	}

	function retrieve_survey_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_groups($condition, $offset, $count, $order_property);
	}

	function retrieve_survey_publication_group($id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_group($id);
	}

	function count_survey_publication_users($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_publication_users($condition);
	}

	function retrieve_survey_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_users($condition, $offset, $count, $order_property);
	}

	function retrieve_survey_publication_user($id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_user($id);
	}

	function count_survey_publication_mails($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_publication_mails($condition);
	}

	function retrieve_survey_publication_mail($id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_mail($id);
	}

	function retrieve_survey_publication_mails($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_publication_mails($condition, $offset, $count, $order_property);
	}

	function count_survey_pages($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_pages($condition);
	}

	function retrieve_survey_pages($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_pages($condition, $offset, $count, $order_property);
	}

	function retrieve_survey_page($page_id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_page($page_id);
	}

	function count_survey_questions($condition)
	{
		return SurveyDataManager :: get_instance()->count_survey_questions($condition);
	}

	function retrieve_survey_question($question_id)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_question($question_id);
	}

	function retrieve_survey_questions($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->retrieve_survey_questions($condition, $offset, $count, $order_property);
	}

	// Url Creation


	function get_create_survey_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SURVEY_PUBLICATION));
	}

	function get_update_survey_publication_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_delete_survey_publication_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_browse_survey_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS), array(self :: PARAM_SURVEY_PUBLICATION, ComplexBuilder :: PARAM_BUILDER_ACTION));
	}

	function get_browse_survey_pages_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGES, self :: PARAM_SURVEY => $survey_publication->get_content_object()));
	}

	function get_browse_survey_page_questions_url($survey_page)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGE_QUESTIONS, self :: PARAM_SURVEY_PAGE => $survey_page->get_id()));
	}

	function get_manage_survey_publication_categories_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES));
	}

	function get_testcase_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TESTCASES), array(TestcaseManager :: PARAM_ACTION, TestcaseManager :: PARAM_SURVEY_PUBLICATION, ComplexBuilder :: PARAM_BUILDER_ACTION));
	}

	function get_survey_publication_viewer_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_survey_results_viewer_url($survey_publication)
	{
		$id = $survey_publication ? $survey_publication->get_id() : null;
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS, self :: PARAM_SURVEY_PUBLICATION => $id));
	}
	
	function get_reporting_filter_survey_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING_FILTER));
	}
	
	function get_reporting_survey_publication_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_question_reporting_url($question)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_QUESTION_REPORTING, self :: PARAM_SURVEY_QUESTION => $question->get_id()));
	}

	function get_import_survey_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_SURVEY));
	}

	function get_export_survey_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_change_survey_publication_visibility_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_move_survey_publication_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_results_exporter_url($tracker_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
	}

	function get_download_documents_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_mail_survey_participant_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MAIL_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	function get_build_survey_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}
	
	function get_survey_publication_export_excel_url($survey_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXCEL_EXPORT, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
	}

	//publications


	function content_object_is_published($object_id)
	{
		return SurveyDataManager :: get_instance()->content_object_is_published($object_id);
	}

	function any_content_object_is_published($object_ids)
	{
		return SurveyDataManager :: get_instance()->any_content_object_is_published($object_ids);
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
		return SurveyDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
	}

	function get_content_object_publication_attribute($publication_id)
	{
		return SurveyDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
	}

	function count_publication_attributes($type = null, $condition = null)
	{
		return SurveyDataManager :: get_instance()->count_publication_attributes($type, $condition);
	}

	function delete_content_object_publications($object_id)
	{
		return SurveyDataManager :: get_instance()->delete_content_object_publications($object_id);
	}

	function delete_content_object_publication($publication_id)
	{
		return SurveyDataManager :: get_instance()->delete_content_object_publication($publication_id);
	}

	function update_content_object_publication_id($publication_attr)
	{
		return SurveyDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
	}

	function add_publication_attributes_elements($form)
	{
		$form->addElement('category', Translation :: get('PublicationDetails'));
		$form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		$form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_TEST, Translation :: get('TestCase'));
		$form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');

		$attributes = array();
		$attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
		$locale = array();
		$locale['Display'] = Translation :: get('ShareWith');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
		$attributes['defaults'] = array();
		$attributes['options'] = array('load_elements' => false);

		$form->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);

		$form->addElement('category');
		$form->addElement('html', '<br />');
		$defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
		$defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
		$form->setDefaults($defaults);
	}

	function get_content_object_publication_locations($content_object)
	{
		$allowed_types = array(Survey :: get_type_name());

		$type = $content_object->get_type();
		if (in_array($type, $allowed_types))
		{
			$categories = SurveyDataManager :: get_instance()->retrieve_survey_publication_categories();
			$locations = array();
			while ($category = $categories->next_result())
			{
				$locations[$category->get_id()] = $category->get_name() . ' - category';
			}
			$locations[0] = Translation :: get('RootSurveyCategory');

			return $locations;
		}

		return array();
	}

	function publish_content_object($content_object, $location, $attributes)
	{

		if (! SurveyRights :: is_allowed(SurveyRights :: ADD_RIGHT, 'publication_browser', 'sts_component'))
		{
			return Translation :: get('NoRightsForSurveyPublication');
		}

		$publication = new SurveyPublication();
		$publication->set_content_object($content_object->get_id());
		$publication->set_publisher(Session :: get_user_id());
		$publication->set_published(time());
		$publication->set_category($location);

		if ($attributes[SurveyPublication :: PROPERTY_HIDDEN] == 1)
		{
			$publication->set_hidden(1);
		}
		else
		{
			$publication->set_hidden(0);
		}

		if ($attributes['forever'] == 1)
		{
			$publication->set_from_date(0);
			$publication->set_to_date(0);
		}
		else
		{
			$publication->set_from_date(Utilities :: time_from_datepicker($attributes['from_date']));
			$publication->set_to_date(Utilities :: time_from_datepicker($attributes['to_date']));
		}

		if ($attributes[SurveyPublication :: PROPERTY_TEST] == 1)
		{
			$publication->set_test(1);
		}
		else
		{
			$publication->set_test(0);
		}

		if ($attributes[self :: PARAM_TARGET_OPTION] != 0)
		{
			$user_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['user'];
			$group_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['group'];
		}
		else
		{
			$users = UserDataManager :: get_instance()->retrieve_users();
			$user_ids = array();
			while ($user = $users->next_result())
			{
				$user_ids[] = $user->get_id();
			}
		}

		$publication->set_target_users($user_ids);
		$publication->set_target_groups($group_ids);

		$publication->create();
		return Translation :: get('PublicationCreated');
	}
}
?>