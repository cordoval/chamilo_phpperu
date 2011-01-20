<?php
namespace repository;

use repository\content_object\survey\Survey;
use repository\content_object\assessment\Assessment;

use common\libraries\Path;
use common\libraries\Session;
use common\libraries\ImscpManifestWriter;
use common\libraries\Filesystem;
use common\libraries\Filecompression;

/**
 * $Id: qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti
 */
//FIXME Do not require main as it causses a cyclic dependency
//require_once dirname(__FILE__) . '/main.php';


/**
 * Exports learning object to QTI format (xml)
 */
class QtiExport extends ContentObjectExport
{

    static function factory_qti($content_object, $directory, $manifest, $toc)
    {
        if ($result = QtiSerializerExport :: factory($content_object, $directory, $manifest, $toc))
        {
            return $result;
        }
        else
        {
            return null;
        }
    }

    public static function accept($object)
    {
        if (! $object instanceof ContentObject)
        {
            return false;
        }
        return $object instanceof Assessment || $object instanceof Survey || strpos(strtolower($object->get_type()), 'question');
    }

    private $manifest = null;
    private $directory = '';
    private $toc = null;

    function __construct($content_object, $directory = '', $manifest = null, $toc = null)
    {
        parent :: __construct($content_object);
        if (empty($manifest))
        {
            $manifest = new ImscpManifestWriter();
            $manifest = $manifest->add_manifest();
            $this->manifest = $manifest;
            $this->toc = $manifest->add_organizations()->add_organization();
        }
        else
        {
            $this->manifest = $manifest;
            $this->toc = $toc;
        }

        if (empty($directory))
        {
            $directory = Path :: get(SYS_TEMP_PATH) . Session :: get_user_id() . '/export_qti/';
            if (! is_dir($directory))
            {
                mkdir($directory, 0777, true);
            }
        }
        $this->directory = $directory;
    }

    public function get_manifest()
    {
        return $this->manifest;
    }

    public function get_toc()
    {
        return $this->toc;
    }

    public function export_content_object()
    {
        $items = $this->get_content_object();
        $items = is_array($items) ? $items : array($items);
        foreach ($items as $item)
        {
            $directory = $this->get_temp_directory();
            $manifest = $this->get_manifest();
            $toc = $this->toc;
            if ($exporter = self :: factory_qti($item, $directory, $manifest, $toc))
            {
                $result = $exporter->export_content_object();
            }
            /*
            $questions = $item->get_questions();
            while ($complex_question = $questions->next_result()) {
                $directory = $this->get_temp_directory();
                $manifest = $this->get_manifest();
                $toc = $this->toc;
                if ($exporter = self :: factory_qti($complex_question->get_ref_object(), $directory, $manifest, $toc)) {
                    $result = $exporter->export_content_object();
                } else {

                }
            }
             */

        }

        $xml = $this->get_manifest()->saveXML();
        $file_name = ImscpManifestWriter :: MANIFEST_NAME;
        $this->create_qti_file($file_name, $xml);

        $temp_dir = $this->get_temp_directory();
        $zip = Filecompression :: factory();
        $zip->set_filename('qti', 'zip');
        $zippath = $zip->create_archive($temp_dir);
        Filesystem :: remove($temp_dir);
        return $zippath;
    }

    protected function get_temp_directory()
    {
        return $this->directory;
    }

    protected function create_qti_file($file_name, $xml)
    {
        $file_path = $this->get_temp_directory() . $file_name;
        Filesystem :: write_to_file($file_path, $xml);
        return $file_path;
    }

}

?>