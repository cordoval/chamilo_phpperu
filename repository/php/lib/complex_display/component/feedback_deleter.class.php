<?php
/**
 * $Id: feedback_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class ComplexDisplayComponentFeedbackDeleterComponent extends ComplexDisplayComponent
{
    private $cid;
    private $pid;

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get('fid'))
                $feedback_ids = Request :: get('fid');
            else
                $feedback_ids = $_POST['fid'];
            
            if (Request :: get('selected_cloi'))
                $this->cid = Request :: get('selected_cloi');
            else
                $this->cid = $_POST['selected_cloi'];
            
            if (Request :: get('pid'))
                $this->pid = Request :: get('pid');
            else
                $this->pid = $_POST['pid'];
            
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
            
            $this->redirect($message, '', array(Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'pid' => $this->pid, 'selected_cloi' => $this->cid));
        }
    }

}
?>