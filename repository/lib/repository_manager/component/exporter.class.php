<?php
/**
 * $Id: exporter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerExporterComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        if ($ids)
        {
            if (! is_array($ids))
                $ids = array($ids);

            if (count($ids) > 0)
            {
                if ($ids[0] == 'all')
                {
                    $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
                    $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
                    $condition = new AndCondition($conditions);
                }
                else
                {
                    $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
                }

                $los = $this->retrieve_content_objects($condition);
                while ($lo = $los->next_result())
                {
                    $content_objects[] = $lo;
                }

                $exporter = ContentObjectExport :: factory('cpo', $content_objects);
                
                if ($ids[0] == 'all')
                	$path = $exporter->export_content_object(true);
                else
                	$path = $exporter->export_content_object(false);

                /*Filesystem :: file_send_for_download($path, true, 'content_objects.cpo');
				Filesystem :: remove($path);*/

                Filesystem :: copy_file($path, Path :: get(SYS_TEMP_PATH) . $this->get_user_id() . '/content_objects.cpo', true);
                $webpath = Path :: get(WEB_TEMP_PATH) . $this->get_user_id() . '/content_objects.cpo';

                $this->display_header();
                $this->display_message('<a href="' . $webpath . '">' . Translation :: get('Download') . '</a>');
                $this->display_footer();
            }
            else
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NoObjectsSelected'));
                $this->display_footer();
            }
        }
    }
}
?>