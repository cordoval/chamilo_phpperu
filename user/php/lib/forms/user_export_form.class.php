<?php
namespace user;

use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\Export;
use common\libraries\Utilities;
/**
 * $Id: user_export_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);

class UserExportForm extends FormValidator
{

    const TYPE_EXPORT = 1;

    private $current_tag;
    private $current_value;
    private $user;
    private $users;

    /**
     * Creates a new UserImportForm
     * Used to export users to a file
     */
    function __construct($form_type, $action)
    {
        parent :: __construct('user_export', 'post', $action, '_blank');

        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_EXPORT)
        {
            $this->build_exporting_form();
        }
    }

    function build_exporting_form()
    {
        $this->addElement('select', 'file_type', Translation :: get('OutputFileType'), Export :: get_supported_filetypes(array('ical')));
        //$this->addElement('submit', 'user_export', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive export'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaults(array('file_type' => 'csv'));

    }
}
?>