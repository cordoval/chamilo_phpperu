<?php
/**
 * $Id: learning_path_scorm_exporter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */

class LearningPathToolScormExporterComponent extends LearningPathToolComponent
{

    function run()
    {
        $lpid = Request :: get(LearningPathTool :: PARAM_LEARNING_PATH_ID);
        $learning_path = RepositoryDataManager :: get_instance()->retrieve_content_object($lpid);
        $exporter = ContentObjectExport :: factory('scorm', $learning_path);
        $exporter->export_content_object();
    }
}
?>