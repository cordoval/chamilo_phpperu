<?php
/**
 * $Id: learning_path_scorm_importer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class LearningPathToolScormImporterComponent extends LearningPathTool
{

    function run()
    {
        $parameters = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_IMPORT_SCORM);
        $import_form = new ContentObjectImportForm('import', 'post', $this->get_url($parameters), 0, $this->get_user(), 'scorm');
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('ImportScorm')));
        $trail->add_help('courses learnpath tool');
        $this->display_header($trail, true);
        
        $objects = Request :: get('objects');
        
        if ($import_form->validate() || $objects)
        {
            if (! $objects)
            {
                $content_objects = $import_form->import_content_object();
                foreach ($content_objects as $content_object)
                {
                    $lo_ids[] = $content_object->get_id();
                }
            }
            else
            {
                $lo_ids = $objects;
            }
            
            $publisher = new ContentObjectPublisher($this);
            $this->set_parameter('objects', $lo_ids);
            echo $publisher->get_publications_form($lo_ids);
        }
        else
        {
            $import_form->display();
        }
        
        $this->display_footer();
    }

    public function with_mail_option()
    {
        return false;
    }
}
?>