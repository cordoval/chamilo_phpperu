<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;

require_once dirname(__FILE__) . '/../forms/fedora_edit_form.class.php';

/**
 * Edit a Fedora object. Allow to change the main datastream and metadata.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerEditorComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if (get_class($this) != __CLASS__)
        {
            $this->run_default();
        }
        else
            if ($api = $this->create_api_component())
            {
                return $api->run();
            }
            else
            {
                $this->run_default();
            }
    }

    protected function run_default()
    {
        $id = $this->get_external_repository_id();
        $form = $this->create_form($id);
        $object = $this->retrieve_external_repository_object($id);
        $form->set_external_repository_object($object);

        if ($form->validate())
        {
            $data = $form->exportValues();
            $success = $this->update_repository_object($data);
            if ($sucess)
            {
                $error_message = '';
                $info_message = Translation :: get('ObjectUpdated', null, Utilities::COMMON_LIBRARIES);
            }
            else
            {
                $error_message = Translation :: get('ObjectNotUpdated', null, Utilities::COMMON_LIBRARIES);
                $info_message = '';
            }

            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

            $this->redirect('', '', $parameters);
        }
        else
        {
            $this->display($form);
        }
    }

    function get_external_repository_id()
    {
        return Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * @return FedoraProxy
     */
    function get_fedora()
    {
        return $this->get_external_repository_connector()->get_fedora();
    }

    /**
     * @return FedoraExternalRepositoryConnector
     *
     */
    function get_connector()
    {
        return $this->get_external_repository_connector();
    }

    function create_form($object_external_id)
    {
        $result = new FedoraEditForm($this, $_GET, array('edit' => $object_external_id));
        return $result;
    }

    function update_repository_object($data)
    {
        $pid = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $label = $data['title'];

        if (isset($_FILES['thumbnail']) && ! empty($_FILES['thumbnail']['tmp_name']))
        {
            $file = $_FILES['thumbnail'];
            $name = $file['name'];
            $path = $file['tmp_name'];
            $mime_type = $file['type'];
            $this->update_thumbnail($pid, $name, $path, $mime_type);
        }
        else
            if (isset($_FILES['data']) && ! empty($_FILES['data']['tmp_name']))
            {
                $mime_type = $_FILES['data']['type'];
                $ext = mimetype_to_ext($mime_type);
                if ($this->is_image($ext))
                {
                    $name = $_FILES['data']['name'];
                    $path = $_FILES['data']['tmp_name'];
                    $this->update_thumbnail($pid, $name, $path, $mime_type);
                    Filesystem :: remove($tmp);
                }
            }
        if (isset($_FILES['data']) && ! empty($_FILES['data']['tmp_name']))
        {
            $file = $_FILES['data'];
            $name = $file['name'];
            $path = $file['tmp_name'];
            $mime_type = $file['type'];
            $this->update_data($pid, $name, $path, $mime_type);
        }

        $this->update_label($pid, $label);
        $this->update_metadata($pid, $data);
    }

    function update_label($pid, $label)
    {
        $fedora = $this->get_fedora();
        $fedora->modify_object($pid, $label);
        $fedora->modify_datastream($pid, 'DS1', $label);
    }

    function update_thumbnail($pid, $name, $path, $mime_type)
    {
        $connector = $this->get_connector();
        $connector->update_thumbnail($pid, $name, $path, $mime_type);
    }

    function update_metadata($pid, $data)
    {
        $meta = new fedora_object_meta();
        $meta->pid = $pid;

        $switch = new switch_object_meta();
        $keys = array_keys($data);
        foreach ($keys as $key)
        {
            if (isset($data[$key]))
            {
                $switch->{$key} = $data[$key];
            }
        }
        $switch->discipline = $data['subject'];
        $switch->discipline_text = $data['subject_dd']['subject_text'];
        $switch->creator = $data['author'];
        $switch->description = $data['description'];
        $switch->rights = $data['edit_rights'];
        $switch->accessRights = $data['access_rights'];

        $fedora = $this->get_external_repository_connector()->get_fedora();
        $content = SWITCH_get_rels_int($meta, $switch);
        $fedora->modify_datastream($pid, 'RELS-EXT', 'Relationships to other objects', $content, 'application/rdf+xml');
        $content = SWITCH_get_chor_dc($meta, $switch);
        $fedora->update_datastream($pid, 'CHOR_DC', 'SWITCH CHOR_DC record for this object', $content, 'text/xml');
    }

    function update_data($pid, $name, $path, $mime_type)
    {
        $fedora = $this->get_fedora();
        $content = file_get_contents($path);
        $fedora->update_datastream($pid, 'DS1', $name, $content, $mime_type, false);
    }

    function display($form)
    {
        $this->display_header($trail, false);
        $form->display();
        $this->display_footer();
    }

    protected function is_image($ext)
    {
        return in_array($ext, Document :: get_image_types());
    }

}

?>