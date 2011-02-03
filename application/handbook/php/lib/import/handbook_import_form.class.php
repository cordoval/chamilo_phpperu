<?php

namespace application\handbook;

use common\libraries;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Filesystem;
use common\libraries\Path;
use common\libraries\OptionsMenuRenderer;
use common\libraries\Utilities;
use common\libraries\ResourceManager;
use repository\RepositoryManager;
use repository\ContentObjectCategoryMenu;
use repository\ContentObjectImport;

require_once dirname(__FILE__) . '/cpo/cpo_import.class.php';

/**
 * A form to import a ContentObject.
 */
class HandbookImportForm extends FormValidator
{
    const IMPORT_FILE_NAME = 'content_object_file';

    private $category;
    private $user;
    private $import_type;
    private $show_category;
    private $messages;
    private $warnings;
    private $errors;
    private $log;

    /**
     * Constructor.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    function __construct($form_name, $method = 'post', $action = null, $category, $user, $import_type = null, $show_category = true)
    {
        parent :: __construct($form_name, $method, $action);
        $this->category = $category;
        $this->import_type = $import_type;
        $this->user = $user;
        $this->show_category = $show_category;
        $this->build_basic_form();
        $this->setDefaults();
    }

    /**
     * Gets the categories defined in the user's repository.
     * @return array The categories.
     */
    function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_user()->get_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Builds a form to import a learning object.
     */
    private function build_basic_form()
    {
        if ($this->show_category)
        {
            $this->add_select(RepositoryManager :: PARAM_CATEGORY_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
        }
        else
        {
            $this->addElement('hidden', RepositoryManager :: PARAM_CATEGORY_ID);
        }

        //import mode
        $this->add_select('mode', Translation :: get('Mode'), $this->get_modes());
        $check_boxes[] = $this->createElement('checkbox', 'option_strict', 'option_strict', Translation :: get('strict'));
        $check_boxes[] = $this->createElement('checkbox', 'option_limited', 'option_limited', Translation :: get('Limited'));

        $this->addGroup($check_boxes, 'options', Translation :: get('Options'), '&nbsp;', true);






        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName', null, Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive import', 'id' => 'import_button'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/resources/javascript/import.js'));
    }

    function get_types()
    {
        $folder = dirname(__FILE__) . '/import/';
        $folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($folders as $f)
        {
            if (strpos($f, '.svn') !== false || strpos($f, 'csv') !== false)
                continue;

            $types[$f] = Translation :: get('Type' . Utilities :: underscores_to_camelcase($f), null, Utilities :: COMMON_LIBRARIES);
        }

        return $types;
    }

    function get_messages()
    {
        return empty($this->messages) ? array() : $this->messages;
    }

    function get_warnings()
    {
        return empty($this->warnings) ? array() : $this->warnings;
    }

    function get_errors()
    {
        return empty($this->errors) ? array() : $this->errors;
    }

    function get_modes()
    {
        $modes[HandbookCpoImport::MODE_NEW] = Translation :: get('New', null, Utilities :: COMMON_LIBRARIES);
        $modes[HandbookCpoImport::MODE_EXTEND] = Translation :: get('Extend', null, Utilities :: COMMON_LIBRARIES);
        $modes[HandbookCpoImport::MODE_FULL] = Translation :: get('Full_Update', null, Utilities :: COMMON_LIBRARIES);

        return $modes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array())
    {
        $defaults[RepositoryManager :: PARAM_CATEGORY_ID] = $this->get_category();
        $defaults['mode'] = HandbookCpoImport::MODE_NEW;
        parent :: setDefaults($defaults);
    }

    function set_values($defaults)
    {
        parent :: setDefaults($defaults);
    }

    /**
     * Imports a learning object from the submitted form values.
     * @return ContentObject The newly imported learning object.
     */
    function import_content_object()
    {
        $values = $this->exportValues();

        $importer = new HandbookCpoImport($_FILES[self :: IMPORT_FILE_NAME], $this->get_user(), $this->exportValue(RepositoryManager :: PARAM_CATEGORY_ID, $this->exportValue('mode'), $this->parse_checkbox_value($values['options']['option_strict']), $this->parse_checkbox_value($values['options']['option_limited'])));
        $result = $importer->import_content_object();

        $this->log .= '<p style="color: green;">' . implode("</br>", $importer->get_log()) . '</p>';
        $this->messages = $importer->get_messages();
        $this->warnings = $importer->get_warnings();
        $this->errors = $importer->get_errors();
        return $result;
    }

    function import_metadata()
    {
        $importer = new HandbookCpoImport($_FILES[self :: IMPORT_FILE_NAME], $this->get_user(), $this->exportValue(RepositoryManager :: PARAM_CATEGORY_ID));
        $result = $importer->import_metadata();
        $this->log .= '<p style="color: blue;">' . implode("</br>", $importer->get_log()) . '</p>';
        $this->messages = $importer->get_messages();
        $this->warnings = $importer->get_warnings();
        $this->errors = $importer->get_errors();
        return $result;
    }

    function get_log()
    {
        return $this->log;
    }

    /**
     * Displays the form
     */
    function display()
    {
        if ($this->get_user()->get_id() == 0)
        {
            return parent :: display();
        }
        parent :: display();
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function get_category()
    {
        return $this->category;
    }

    function get_user()
    {
        return $this->user;
    }

}

?>