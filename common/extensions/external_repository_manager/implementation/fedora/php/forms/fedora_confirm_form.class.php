<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use application\weblcms\Course;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Utilities;
use common\libraries\FormValidator;
use common\libraries\PropertiesTable;

/**
 * Default form to confirm the transmission of a file to Fedora. Display metadata.
 * By default display standard fields such as title, description, etc.
 * Provides logic to display additional fields - collection, discipline, ... - by subclassing the form.
 * To to this inherit from this class and overwrite required functions - get_licences, etc.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraConfirmForm extends FormValidator
{

    const PARAM_COURSE_ID = FedoraExternalRepositoryManager :: PARAM_COURSE_ID;

    private $parameters = array();
    private $application = null;
    private $data = false;

    function __construct($application, $parameters, $data = false)
    {
        parent :: __construct(__CLASS__, 'post', Redirect :: get_url($parameters));
        $this->application = $application;
        $this->parameters = $parameters;

        $this->addElement('hidden', 'data');
        if ($data)
        {
            if (is_string($data['file']))
            {
                $data['file'] = unserialize($data['file']);
            }
            $this->data = $data;
            $default['data'] = serialize($data);
            $this->setDefaults($default);
        }
        else
        {
            $value = $this->exportValue('data');
            $this->data = $value ? unserialize($value) : array();
        }

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

    public function get_data()
    {
        return $this->data;
    }

    public function get_file()
    {
        $result = $this->get('file');
        $result = is_string($result) ? unserialize($result) : $result;
        return $result;
    }

    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : '';
    }

    public function exportValues()
    {
        return $this->data;
    }

    /**
     * @return FedoraExternalRepositoryManagerConnector
     */
    public function get_connector()
    {
        return $this->get_application()->get_external_repository_manager_connector();
    }

    function build_form()
    {
        $this->build_header();
        $this->build_body();
        $this->build_footer();
    }

    protected function build_header()
    {

    }

    protected function build_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array(
                'class' => 'upload'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    protected function build_body()
    {
        $properties = array();

        if ($file = $this->get_file())
        {
            $html = '<a href="' . $file['href'] . '">' . $this->get(FedoraExternalRepositoryObject :: PROPERTY_TITLE) . '</a>';
            $properties[Translation :: get('Title')] = $html;
        }
        else
        {
            $properties[Translation :: get('Title')] = $this->get(FedoraExternalRepositoryObject :: PROPERTY_TITLE);
        }
        $properties[Translation :: get('Overwrite')] = $this->get('pid') ? '<span class="highlight">' . Translation :: get('Yes_') . '</span>' : Translation :: get('No_');
        $properties[Translation :: get('Description')] = $this->get(FedoraExternalRepositoryObject :: PROPERTY_DESCRIPTION);
        $properties[Translation :: get('Author')] = $this->get(FedoraExternalRepositoryObject :: PROPERTY_AUTHOR);
        if ($thumbnail = $this->get('thumbnail'))
        {
            $html = '<img src="' . $thumbnail['href'] . '" alt="' . $thumbnail['name'] . '" style="max-width:200px;max-height:200px"/>';
            $properties[Translation :: get('Thumbnail')] = $html;
        }

        if ($licences = $this->get_licences($this->get(FedoraExternalRepositoryObject :: PROPERTY_LICENSE)))
        {
            $properties[Translation :: get('Licence')] = $licences;
        }

        if ($access_rights = $this->get_access_rights($this->get(FedoraExternalRepositoryObject :: PROPERTY_ACCESS_RIGHTS)))
        {
            $properties[Translation :: get('Rights')] = $access_rights;
        }
        if ($edit_rights = $this->get_edit_rights($this->get(FedoraExternalRepositoryObject :: PROPERTY_EDIT_RIGHTS)))
        {
            $properties[Translation :: get('EditRights')] = $edit_rights;
        }

        $subject_dd = $this->get(FedoraExternalRepositoryObject :: PROPERTY_SUBJECT . '_dd');
        if ($subject_text = $subject_dd['subject_text'])
        {
            $properties[Translation :: get('Subject')] = $subject_text;
        }

        $table = new PropertiesTable($properties);
        $html = '';
        $html .= '<h3>' . Translation :: get('Confirm') . '</h3>';
        $html .= $table->toHtml();

        $this->addElement('html', $html);
    }

    function get_licences($key = false)
    {
        return $key ? false : array();
    }

    function get_access_rights($key = false)
    {
        return $key ? false : array();
    }

    function get_edit_rights($key = false)
    {
        return $key ? false : array();
    }

    function get_collections($key = false)
    {
        return $key ? false : array();
    }

}

?>