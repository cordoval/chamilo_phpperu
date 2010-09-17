<?php
/**
 * $Id: qti_importer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once Path::get_application_path() . 'lib/weblcms/tool/assessment/assessment_tool.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 */
class AssessmentManagerQtiImporterComponent extends AssessmentManager
{

	protected $show_category = true;
	protected $messages = array();
	protected $warnings = array();
	protected $errors = array();

	/**
	 * Runs this component and displays its output.
	 */
	function run(){
		$this->messages = $this->warnings = $this->errors = array();

		$form = $this->build_importing_form();

		if(! $form->validate()){
			$this->print_form($form);
		}else if($aid = $this->import_qti($form)){
			$this->redirect_create_assessement_publication($aid);
		}else{
			$this->redirect_import_failed();
		}
	}

	/**
	 * Create and returns the import form.
	 *
	 */
	function build_importing_form()
	{
		$url = $this->get_url(array(AssessmentTool::PARAM_ACTION => AssessmentTool::ACTION_IMPORT_QTI));
		$form = new FormValidator('qti_import', 'post', $url);
		$form->addElement('html', '<b>Import assessment from QTI</b><br/><br/>');
		$form->addElement('html', '<em>' . Translation::get('FileMustContainAllAssessmentXML') . '</em>');
		$form->addElement('file', 'file', Translation::get('FileName'));

		if($this->show_category){
			$this->add_select($form, AssessmentManager::PARAM_CATEGORY, Translation::get('CategoryTypeName'), $this->get_categories());
		}else{
			$this->addElement('hidden', AssessmentManager::PARAM_CATEGORY);
		}

		$allowed_upload_types = array('zip');
		$form->addRule('file', Translation::get('OnlyZipAllowed'), 'filetype', $allowed_upload_types);

		$buttons[] = $form->createElement('style_submit_button', 'submit', Translation::get('Import'), array('class' => 'positive import'));

		$form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		return $form;
	}

	/**
	 * Print the form with page headers and messages.
	 *
	 * @param $form
	 */
	function print_form($form){
		$trail = BreadcrumbTrail::get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager::PARAM_ACTION => AssessmentManager::ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation::get('BrowseAssessmentPublications')));
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager::PARAM_ACTION => AssessmentManager::ACTION_IMPORT_QTI)), Translation::get('ImportQTI')));

		$this->display_header($trail, true);

		if($this->messages){
			Display::normal_message(implode('<br/>', $this->messages));
		}
		if($this->warnings){
			Display::warning_message(implode('<br/>', $this->warnings));
		}
		if($this->errors){
			Display::error_message(implode('<br/>', $this->errors));
		}

		echo $form->toHtml();
		$this->display_footer();
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

	/**
	 * Add a select HTML element to the form.
	 *
	 *
	 * @param $name
	 * @param $label
	 * @param $values
	 * @param $required
	 * @param $attributes
	 */
	function add_select($form, $name, $label, $values, $required = true, $attributes = array())
	{
		$element = $form->addElement('select', $name, $label, $values, $attributes);
		if ($required){
			$form->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		return $element;
	}

	/**
	 * In case of success move to next step: publish assessesment.
	 *
	 * @param $aid
	 */
	function redirect_create_assessement_publication($aid){
		$this->messages[] = Translation::get('QtiImported');

		$parameters = array();
		$parameters[Application::PARAM_ACTION] = AssessmentManager::ACTION_CREATE_ASSESSMENT_PUBLICATION;
		$parameters[RepoViewer::PARAM_ID] = $aid;
		$parameters[RepoViewer::PARAM_ACTION] = RepoViewer::ACTION_PUBLISHER;
		$parameters[self::PARAM_MESSAGE] = implode('<br/>', $this->messages);
		$parameters[self::PARAM_WARNING_MESSAGE] = implode('<br/>', $this->warnings);
		$parameters[self::PARAM_ERROR_MESSAGE] = implode('<br/>', $this->errors);

		$this->simple_redirect($parameters);
	}

	/**
	 * Redirect in case of import failure.
	 *
	 */
	function redirect_import_failed(){
		$this->errors[] = Translation::get('QtiNotImported');

		$parameters = array();
		$parameters[Application::PARAM_ACTION] = AssessmentManager::DEFAULT_ACTION;
		$parameters['object'] = $aid;
		$parameters[self::PARAM_MESSAGE] = implode('<br/>', $this->messages);
		$parameters[self::PARAM_WARNING_MESSAGE] = implode('<br/>', $this->warnings);
		$parameters[self::PARAM_ERROR_MESSAGE] = implode('<br/>', $this->errors);

		$this->simple_redirect($parameters);
	}

	/**
	 * Import the QTI file. Delegate works to the QTI import module.
	 * Returns the first assessement object.
	 *
	 * @param $form
	 */
	function import_qti($form){
		$values = $form->exportValues();

		$file = $_FILES['file'];
		$user = $this->get_user();
		$category = $values[AssessmentManager::PARAM_CATEGORY];

		$importer = ContentObjectImport::factory('qti', $file, $user, $category);
		$result = $importer->import_content_object();

		$this->messages =  $importer->get_messages();
		$this->warnings = $importer->get_warnings();
		$this->errors = $importer->get_errors();
		if($result){
			foreach($result as $item){
				if($item instanceof Assessment){
					return $item->get_id();
				}
			}
		}

		return false;
	}

	function import_groups()
	{
		$values = $this->exportValues();
		$this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
		return true;
	}
}


?>