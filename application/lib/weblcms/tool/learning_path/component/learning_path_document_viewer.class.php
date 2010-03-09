<?php
/**
 * $Id: learning_path_document_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
class LearningPathToolDocumentViewerComponent extends LearningPathToolComponent
{

    function run()
    {
        $id = Request :: get('id');
        $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($id);
        
        $name = $lo->get_filename();
        $type = $lo->get_mime_type();
        
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Content-Type: ' . $type);
        header('Content-Description: ' . $lo->get_filename());
        readfile($lo->get_full_path());
    }

}
?>