<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: DiskQuota.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if uploading a document is possible compared to the
 * available disk quota.
 */
class HTML_QuickForm_Rule_DiskQuota extends HTML_QuickForm_Rule
{

    /**
     * Function to check if an uploaded file can be stored in the repository
     * @see HTML_QuickForm_Rule
     * @param mixed $file Uploaded file (array)
     * @return boolean True if the filesize doesn't cause a disk quota overflow
     */
    function validate($file)
    {
        $size = $file['size'];
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user(Session :: get_user_id());
        $quotamanager = new QuotaManager($user);
        $available_disk_space = $quotamanager->get_available_disk_space();
        return $size <= $available_disk_space;
    }
}
?>