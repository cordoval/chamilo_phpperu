<?php
/**
 * $Id: feedback_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */

class ToolFeedbackDeleterComponent extends ToolComponent
{
    private $cid;
    private $pid;

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
            if (Request :: get('fid'))
                $feedback_ids = Request :: get('fid');
            else
                $feedback_ids = $_POST['fid'];
            
            if (Request :: get('cid'))
                $this->cid = Request :: get('cid');
            else
                $this->cid = $_POST['cid'];
            
            if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
                $this->pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            else
                $this->pid = $_POST[Tool :: PARAM_PUBLICATION_ID];
            
            if (! is_array($feedback_ids))
            {
                $feedback_ids = array($feedback_ids);
            }
            
            $datamanager = RepositoryDataManager :: get_instance();
            
            foreach ($feedback_ids as $index => $fid)
            {
                $condition = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
                $feedbacks = $datamanager->retrieve_content_object_pub_feedback($condition);
                while ($feedback = $feedbacks->next_result())
                {
                    $feedback->delete();
                }
            }
            if (count($feedback_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ContentObjectFeedbacksDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ContentObjectFeedbackDeleted'));
            }
            
            switch (Request :: get('tool'))
            {
                case 'learning_path' :
                    $tool_action = 'view_clo';
                    break;
                case 'wiki' :
                    $tool_action = 'view';
                    break;
                default :
                    $tool_action = 'discuss';
                    break;
            }
            
            $this->redirect($message, '', array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => Request :: get('display_action'), Tool :: PARAM_PUBLICATION_ID => $this->pid, 'cid' => $this->cid));
        }
    }

}
?>
