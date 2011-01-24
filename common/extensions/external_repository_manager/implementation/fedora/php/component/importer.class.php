<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use repository\content_object\document\Document;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Session;
use common\libraries\Utilities;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\libraries\Filesystem;
use common\libraries\Filecompression;
use repository\ContentObjectImport;
use application\weblcms\WeblcmsDataManager;
use common\libraries\Application;
use application\weblcms\Tool;
use application\weblcms\ContentObjectPublication;
use repository\RepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ExternalSync;

require_once Path :: get_repository_path() . '/lib/import/content_object_import.class.php';
require_once dirname(__FILE__) . '/../forms/fedora_import_form.class.php';
require_once Path :: get_application_path() . 'weblcms/php/lib/course_type/course_type_tool.class.php';

/**
 * Impot a Fedora object into the repository.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerImporterComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if ($api = $this->create_api_component())
        {
            return $api->run();
        }

        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object(FedoraExternalRepositoryObject $external_object)
    {
        if (! $external_object->is_importable())
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(null, false, $parameters);
        }

        $form = new FedoraImportForm($this, $_GET, array());

        if ($form->validate())
        {
            $category_id = $form->exportValue('category');
            $course_id = $form->exportValue('course_id');
            $this->import($external_object, $category_id, $course_id);
            return true;
        }
        else
        {
            $this->display($form);
            die(); //required to avoid redirection from caller
        }
    }

    function import($external_object, $category_id, $course_id)
    {
        $pid = $external_object->get_id();
        $ds = $this->get_datastream($external_object);
        $dsID = $ds->get_dsID();
        $mime_type = $ds->get_mime_type();
        $ext = $ds->get_extention();
        $ext = $ext ? '.' . $ext : '';
        $content = $this->retrieve_datastream_content($pid, $dsID);
        $name = $ds->get_title();

        $path = Path :: get_temp_path() . '/f' . sha1(Session :: get_user_id() . time()) . $ext;

        //file_put_contents($path, $content);
        Filesystem :: write_to_file($path, $content);

        $file = array();
        $file['type'] = $mime_type;
        $file['tmp_name'] = $path;
        $file['name'] = $name;

        $user = $this->get_user();

        if ($this->is_imscp($file, $mime_type))
        {
            $importer = ContentObjectImport :: factory('cp', $file, $user, $category_id);
            $result = $importer->import_content_object();
        }
        else
        {
            if (count(explode('.', $name)) >= 1)
            {
                $file['name'] = $name . $ext;
            }

            $importer = ContentObjectImport :: factory('document', $file, $user, $category_id);
            $result = $importer->import_content_object();
        }
        if ($result)
        {
            if ($course = $this->retrieve_course($course_id))
            {
                $this->publish($course, $result);
            }
            if ($result instanceof Document)
            {
                $quicksave = ExternalSync :: quicksave($result, $external_object, $this->get_external_repository()->get_id());
            }
        }

        $messages = $importer->get_messages();
        $warnings = $importer->get_warnings();
        $errors = $importer->get_errors();

        if ($result)
        {
            $messages[] = Translation :: get('ImportSuccesfull');
        }
        else
        {
            $errors[] = Translation :: get('ImportFailed');
        }

        $parameters = $this->get_parameters();
        if (count($messages) > 0)
        {
            $parameters[Application :: PARAM_MESSAGE] = implode('<br/>', $messages);
        }
        if (count($warnings) > 0)
        {
            $parameters[Application :: PARAM_WARNING_MESSAGE] = implode('<br/>', $warnings);
        }
        if (count($errors) > 0)
        {
            $parameters[Application :: PARAM_ERROR_MESSAGE] = implode('<br/>', $errors);
        }

        $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
        $this->simple_redirect($parameters, array(
                ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY,
                ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
    }

    function display($form)
    {
        $this->display_header($trail, false);
        $form->display();
        $this->display_footer();
    }

    /**
     * Returns true if $file is an IMS CP zip file. False otherwise
     *
     * @param array $file
     * @param string $mime_type
     * @return bool
     */
    protected function is_imscp($file, $mime_type)
    {
        if (strpos($mime_type, 'zip') === false)
        {
            return false;
        }
        else
        {
            $zip = Filecompression :: factory();
            $result = $zip->extract_file($file['tmp_name']) . '/';
            $items = Filesystem :: get_directory_content($result, Filesystem :: LIST_FILES, false);
            foreach ($items as $item)
            {
                if (strtolower($item) == 'imsmanifest.xml')
                {
                    Filesystem :: remove($result);
                    return true;
                }
            }
            Filesystem :: remove($result);
            return false;
        }
    }

    /**
     * Returns the datastream object to import.
     * If a datastream ID has been provided returns it. Otherwise returns the first non-system datastream.
     *
     * @param $external_object
     */
    protected function get_datastream(FedoraExternalRepositoryObject $external_object)
    {
        if ($dsID = Request :: get(FedoraExternalRepositoryManager :: PARAM_DATASTREAM_ID))
        {
            return $external_object->get_datastreams($dsID);
        }
        else
        {
            $dss = $external_object->get_datastreams();
            foreach ($dss as $ds)
            {
                if (! $ds->is_system_datastream())
                {
                    return $ds;
                }
            }
        }
        return false;
    }

    /**
     * Returns the datastream content as string
     *
     * @param unknown_type $pid
     * @param unknown_type $dsID
     */
    protected function retrieve_datastream_content($pid, $dsID)
    {
        $connector = $this->get_external_repository_manager_connector();
        $result = $connector->retrieve_datastream_content($pid, $dsID);
        return $result;
    }

    protected function retrieve_course($id)
    {
        if (empty($id))
        {
            return false;
        }

        return WeblcmsDataManager :: get_instance()->retrieve_course($id);
    }

    /**
     * Publish a content object to a course.
     *
     * @param Course $course
     * @param ContentObject $object
     */
    public function publish(Course $course, $object)
    {
        $objects = is_array($object) ? $object : array($object);
        $user = $this->get_user();
        $application = Application :: factory('Weblcms', $user);
        foreach ($objects as $object)
        {
            if ($tool = $this->get_tool_name($application, $course, $object))
            {
                $pub = new ContentObjectPublication();
                $pub->set_course_id($course->get_id());
                $pub->set_content_object_id($object->get_id());
                $pub->set_tool($tool);
                $pub->set_hidden(false);
                $pub->set_publisher_id($user->get_id());
                $pub->set_parent_id(0);
                $pub->set_category_id(0);
                $pub->set_from_date(0);
                $pub->set_to_date(0);
                $time = time();
                $pub->set_publication_date($time);
                $pub->set_modified_date($time);
                $pub->save();
            }
        }
    }

    /**
     * Returns the tool name used to publish a content object to a course.
     *
     * @param $application
     * @param Course $course
     * @param ContentObject $object
     */
    protected function get_tool_name($application, Course $course, ContentObject $object)
    {
        $tools_properties = $course->get_tools();
        foreach ($tools_properties as $tool_properties)
        {
            $tool = Tool :: factory($tool_properties->name, $application);
            $allowed_types = $tool->get_allowed_types();
            if (in_array($object->get_type(), $allowed_types))
            {
                return $tool_properties->name;
            }
        }
        return null;
    }

}

?>