<?php
/**
 * $Id: content_object_copier.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerContentObjectCopierComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $lo_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $target_user = Request :: get(RepositoryManager :: PARAM_TARGET_USER);
        
        $content_object_copier = new ContentObjectCopier($target_user);
        
        if (! is_array($lo_ids))
        {
            $lo_ids = array($lo_ids);
        }
        
        if (count($lo_ids) == 0 || ! isset($target_user))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('ContentObjectAndTargetUserRequired'));
            $this->display_footer();
        }
        
        $failed = 0;
        
        foreach ($lo_ids as $lo_id)
        {
            $lo = $this->retrieve_content_object($lo_id);
            $content_object_copier->copy_content_object($lo);
        }
        
        if (count($lo_ids) > 0)
        {
            if ($failed == 0)
                $message = Translation :: get('ContentObjectsCopied');
            elseif ($failed > 0 && $failed < count($lo_ids))
                $message = Translation :: get('SomeContentObjectsNotCopied');
            else
                $message = Translation :: get('ContentObjectsNotCopied');
        }
        else
        {
            if ($failed == 0)
                $message = Translation :: get('ContentObjectCopied');
            else
                $message = Translation :: get('ContentObjectNotCopied');
        }
        
        $this->redirect($message, ($failed > 0), array(RepositoryManager :: PARAM_ACTION => null));
    
    }

}
?>
