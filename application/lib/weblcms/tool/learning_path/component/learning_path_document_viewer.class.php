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
        
        $types = array('text/html' => array('.html', '.htm'), 'text/plain' => array('.txt'), 'image/' => array('.jpg', '.bmp', '.jpeg', '.png'));
        $name = $lo->get_filename();
        
        foreach ($types as $type => $extensions)
        {
            foreach ($extensions as $extension)
            {
                $len = strlen($extension) * - 1;
                if (substr(strtolower($name), $len) == $extension)
                {
                    if ($type == 'image/')
                        $type .= substr($extension, 1);
                    
                    $bool = true;
                    break;
                }
            }
            
            if ($bool)
                break;
        }
        
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Content-Type: ' . $type);
        header('Content-Description: ' . $lo->get_filename());
        readfile($lo->get_full_path());
    }

}
?>