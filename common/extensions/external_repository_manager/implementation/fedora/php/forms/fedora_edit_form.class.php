<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Utilities;
use common\libraries\FormValidator;
use application\weblcms\Course;

require_once Path::get_application_path() .'/weblcms/php/lib/course/course.class.php';
require_once dirname(__FILE__) . '/fedora_tree.class.php';

/**
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraEditForm extends FormValidator{

	const PARAM_COURSE_ID = FedoraExternalRepositoryManager::PARAM_COURSE_ID;

	protected $parameters = array();
	protected $application = null;
	protected $file = false;
	protected $data = false;

	function __construct($application, $parameters, $data=false){
		parent::__construct(__CLASS__, 'post', Redirect::get_url($parameters));
		$this->application = $application;
		$this->paramaters = $parameters;
		$this->data = $data;

		if($data && isset($data['file'])){
			$file = $data['file'];
			$file = is_string($file) ? unserialize($file) : $file;
			$this->file = $file;
		}

		$this->build_form();
	}

	function exportValues($elementList=null){
		$result = parent::exportValues($elementList);
		if(isset($this->file)){
			$result['file'] = $this->file;
		}
		return $result;
	}

	/**
	 * @return FedoraExternalRepositoryManager
	 */
	public function get_application(){
		return $this->application;
	}

	/**
	 * @return User
	 */
	public function get_user(){
		return $this->get_application()->get_user();
	}

	function get_course_id(){
		return Request::get(self::PARAM_COURSE_ID);
	}

	function get_course(){
		$id = $this->get_course_id();
		$store = Course::get_data_manager();
		$result = $store->retrieve_course($id);
		return $result;
	}

	public function get_file(){
		return $this->file;
	}

	/**
	 * @return FedoraExternalRepositoryManagerConnector
	 */
	public function get_connector(){
		return $this->get_application()->get_external_repository_manager_connector();
	}

	public function set_external_repository_object(FedoraExternalRepositoryObject $external_repository_object){
		$this->external_repository_object = $external_repository_object;
		$this->addElement('hidden', FedoraExternalRepositoryObject::PROPERTY_ID);
		$defaults[FedoraExternalRepositoryObject::PROPERTY_TITLE] = $external_repository_object->get_title();
		$defaults[FedoraExternalRepositoryObject::PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
		$defaults[FedoraExternalRepositoryObject::PROPERTY_AUTHOR] = $external_repository_object->get_creator();
		parent::setDefaults($defaults);
	}

	function build_form(){
		$this->build_header();
		$this->build_body();
		$this->build_footer();
	}

	protected function build_header(){
		//$this->addElement('html', '<h3>' . Translation::get('Edit') .'</h3>');
	}

	protected function build_footer(){
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('Ok'), array('class' => 'positive'));
		//$buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('Cancel'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	protected function build_body(){
		$defaults = array();

		$this->addElement('category', Translation::get('Data'));
			$this->addElement('file', 'data', Translation::get('File'));
		$this->addElement('category');

		$this->addElement('category', Translation::get('GeneralProperties'));

		if($file = $this->get_file()){
			$html = '<a href="' . $file['href'] .'" title="'.$file['title'].'">' . Translation::get('Download') . '</a>';
			$this->addElement('static', '', Translation::get('File'), $html);
		}


		$this->addElement('text', $id = FedoraExternalRepositoryObject::PROPERTY_TITLE, Translation::get('Title'), array("size" => "50"));
		$this->addRule(FedoraExternalRepositoryObject::PROPERTY_TITLE, Translation::get('ThisFieldIsRequired'), 'required');
		$defaults[$id] = $this->default_title();

		$this->addElement('textarea', FedoraExternalRepositoryObject::PROPERTY_DESCRIPTION, Translation::get('Description'), array("rows" => "7", "cols" => "80"));

		$this->addElement('text', $key = FedoraExternalRepositoryObject::PROPERTY_AUTHOR, Translation::get('Author'), array("size" => "50"));
		$defaults[$key] = $this->get_user()->get_fullname();

		$this->addElement('file', 'thumbnail', Translation::get('Thumbnail'));

		$this->addElement('category');

		$licences = $this->get_licenses();
		$access_rights = $this->get_access_rights();
		$edit_rights = $this->get_edit_rights();
		if(!(empty($licences) && empty($access_rights)&&empty($edit_rights))){
			$this->addElement('category', Translation::get('Rights'));
			if($licences){
				$this->addElement('select', FedoraExternalRepositoryObject::PROPERTY_LICENSE, Translation::get('License'), $licences);
			}
			if($access_rights){
				$this->addElement('select', FedoraExternalRepositoryObject::PROPERTY_ACCESS_RIGHTS, Translation::get('AccessRights'), $access_rights);
			}
			if($edit_rights){
				$this->addElement('select', FedoraExternalRepositoryObject::PROPERTY_EDIT_RIGHTS, Translation::get('Rights'), $edit_rights);
			}
			$this->addElement('category');
		}
		if($this->has_disciplines()){
			$this->addElement('category', Translation::get('Classification'));
			$this->addSubject();
		}

		if($collections = $this->get_collections()){
			if(count($collections) == 1){
				$this->addElement('hidden', FedoraExternalRepositoryObject::PROPERTY_COLLECTION, Translation::get('Collection'), $collections);
				foreach($collections as $key => $value){
					$defaults[FedoraExternalRepositoryObject::PROPERTY_COLLECTION] = $key;
				}
			}
		}
		$this->addElement('category');

		$this->setDefaults($defaults);
	}

	function addSubject(){
		$key = FedoraExternalRepositoryObject::PROPERTY_SUBJECT;
		$text_name = 'subject_dd[subject_text]';
		$dropdown_id = 'dd';

		$disciplines = $this->get_disciplines($key, $text_name, $dropdown_id);
		if(empty($disciplines)){
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
		$text = FedoraExternalRepositoryObject::PROPERTY_SUBJECT . '_text';
		$group[] = $this->createElement('text', $text, Translation::get('Subject'), array("size" => "50", 'id'=>$text, 'readonly'=>'readonly', 'onclick '=> $onclick));

		$group[] = $this->createElement('style_button', 'dd', '  ', array('class'=>'dropdown', 'onclick' => $onclick));

		$this->addElement('hidden', $key);

		$tree = new FedoraTree($disciplines);
		$html = $tree->render_as_tree();
		$html = '<div id="'. $dropdown_id .'" style="display:none" class="dropdown">' . $html . '</div>';
		$group[] = $this->createElement('static', '' , '', $html);

		$this->addGroup($group, FedoraExternalRepositoryObject::PROPERTY_SUBJECT.'_dd', Translation::get('Subject'));
		return true;
	}

	function has_disciplines(){
		$key = FedoraExternalRepositoryObject::PROPERTY_SUBJECT;
		$text_name = 'subject_dd[subject_text]';
		$dropdown_id = 'dd';

		$disciplines = $this->get_disciplines($key, $text_name, $dropdown_id);
		return (bool)$disciplines;
	}

	function get_disciplines($editor_key, $editor_text, $dropdown){
		$result = array();
		return $result;
	}

	function get_licenses(){
		$result = array();
		return $result;
	}

	function get_access_rights(){
		$result = array();
		return $result;
	}

	function get_edit_rights(){
		$result = array();
		return $result;
	}

	function get_collections(){
		$result = array();
		return $result;
	}

	function default_title(){
		if($course = $this->get_course()){
			$result = $course->get_name() . '-' . Translation::get('Export') . '-' . time();
		}else{
			$file = $this->get_file();
			$result = $file['title'] . '-' . Translation::get('Export') . '-' . time();
		}
		return $result;
	}

}
















?>