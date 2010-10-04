<?php
/**
 * $Id: scorm_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.scorm
 */
require_once dirname(__FILE__) . '/learning_path/learning_path_scorm_export.class.php';

/**
 * Exports learning object to the chamilo learning object format (xml)
 */
class ScormExport extends ContentObjectExport
{
    private $rdm;

    function ScormExport($content_object)
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        parent :: __construct($content_object);
    }

    public function export_content_object()
    {
        $exporter = self :: factory_scorm($this->get_content_object());
        return $exporter->export_content_object();
    }

    function get_rdm()
    {
        return $this->rdm;
    }

    static function factory_scorm($content_object)
    {
        switch ($content_object->get_type())
        {
            case LearningPath :: get_type_name() :
                $exporter = new LearningPathScormExport($content_object);
                break;
            default :
                $exporter = null;
                break;
        }
        return $exporter;
    }
}
?>