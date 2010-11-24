<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;
use common\libraries\Translation;

/**
 * Synchronize a Chamilo Document object by writing Chamilo's content to Fedora.
 * Works only for Document objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerExternalSyncerComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if ($api = $this->create_api_component())
        {
            return $api->run();
        }
        ExternalRepositoryComponent :: launch($this);
    }

    function synchronize_external_repository_object(ExternalRepositoryObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();

        if ($this->update_document($external_object, $content_object))
        {
            $external_object = $this->get_external_repository_connector()->retrieve_external_repository_object($external_object->get_id());

            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_repository_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = $content_object->get_id();
                $this->redirect(Translation :: get('ContentObjectUpdatedSuccessful'), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(Translation :: get('ContentObjectUpdatedFailed'), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(Translation :: get('ExternalRepositoryObjectUpdatedFailed'), true, $parameters);
        }
    }

    function update_document(ExternalRepositoryObject $external_object, Document $document)
    {
        $pid = $external_object->get_id();
        $label = $document->get_title();

        $mime_type = $document->get_mime_type();
        $path = Path :: get(SYS_FILE_PATH) . 'repository/' . $document->get_path();

        $name = $document->get_filename();

        if ($document->is_image())
        {
            $this->update_thumbnail($pid, $name, $path, $mime_type);
        }

        $this->update_data($pid, $name, $path, $mime_type);
        $this->update_label($pid, $label);

        $data = $external_object->get_metadata();
        $description = $document->get_description();
        $description = $this->html2txt($description);
        $description = html_entity_decode($description);
        $description = trim($description);
        $description = utf8_encode($description);

        $this->update_metadata($pid, $description, $data);
        return true;
    }

    function update_label($pid, $label)
    {
        $fedora = $this->get_external_repository_connector()->get_fedora();
        $fedora->modify_object($pid, $label);
        $fedora->modify_datastream($pid, 'DS1', $label);
    }

    function update_thumbnail($pid, $name, $path, $mime_type)
    {
        $connector = $this->get_external_repository_connector();
        $connector->update_thumbnail($pid, $name, $path, $mime_type);
    }

    function update_metadata($pid, $description, $data)
    {
        $meta = new fedora_object_meta();
        $meta->pid = $pid;
        $switch = new switch_object_meta();

        foreach ($data as $key => $value)
        {
            $switch->{$key} = $data[$key];
        }

        $switch->discipline = $data['subject'];
        $switch->discipline_text = $data['subject_dd']['subject_text'];
        $switch->creator = isset($data['creator']) ? $data['creator'] : $this->get_user()->get_fullname();
        $switch->description = $description;

        $connector = $this->get_external_repository_connector();
        $fedora = $connector->get_fedora();

        $content = SWITCH_get_rels_int($meta, $switch);
        $fedora->modify_datastream($pid, 'RELS-EXT', 'Relationships to other objects', $content, 'application/rdf+xml');
        $content = SWITCH_get_chor_dc($meta, $switch);
        $fedora->modify_datastream($pid, 'CHOR_DC', 'SWITCH CHOR_DC record for this object', $content, 'text/xml');
    }

    function update_data($pid, $name, $path, $mime_type)
    {
        $fedora = $this->get_external_repository_connector()->get_fedora();
        $content = file_get_contents($path);
        //try{
        $fedora->modify_datastream($pid, 'DS1', $name, $content, $mime_type, false);
        //}catch(Exception $e){
    //	$fedora->add_datastream($pid, 'DS1', $name, $content, $mime_type, false);
    //}
    }

    function html2txt($html)
    {
        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
'@<[?]php[^>].*?[?]>@si', //scripts php
'@<[?][^>].*?[?]>@si', //scripts php
'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
'@<![\s\S]*?--[ \t\n\r]*>@')// Strip multi-line comments including CDATA
;
        $result = preg_replace($search, '', $html);
        return $result;
    }

}
?>