<?php
/**
 * $Id: content_object_form.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */

/**
 * A form to create and edit a ContentObject.
 */
abstract class ContentObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_COMPARE = 3;
    const TYPE_REPLY = 4;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $allow_new_version;

    private $owner_id;

    /**
     * The learning object.
     */
    private $content_object;

    /**
     * Any extra information passed to the form.
     */
    private $extra;

    protected $form_type;

    /**
     * Constructor.
     * @param int $form_type The form type; either
     *                       ContentObjectForm :: TYPE_CREATE or
     *                       ContentObjectForm :: TYPE_EDIT.
     * @param ContentObject $content_object The object to create or update.
     *                                        May be an AbstractContentObject
     *                                        upon creation.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    protected function __construct($form_type, $content_object, $form_name, $method = 'post', $action = null, $extra = null, $additional_elements, $allow_new_version = true)
    {
        parent :: __construct($form_name, $method, $action);
        $this->form_type = $form_type;
        $this->content_object = $content_object;
        $this->owner_id = $content_object->get_owner_id();
        $this->extra = $extra;
        $this->additional_elements = $additional_elements;
        $this->allow_new_version = $allow_new_version;

        if ($this->form_type == self :: TYPE_EDIT || $this->form_type == self :: TYPE_REPLY)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        elseif ($this->form_type == self :: TYPE_COMPARE)
        {
            $this->build_version_compare_form();
        }
        if ($this->form_type != self :: TYPE_COMPARE)
        {
            $this->add_progress_bar(2);
            $this->add_footer();
        }
        $this->setDefaults();
    }

    /**
     * Returns the ID of the owner of the learning object being created or
     * edited.
     * @return int The ID.
     */
    protected function get_owner_id()
    {
        return $this->owner_id;
    }

    /**
     * Sets the ID of the owner of the learning object being created or
     * edited.
     * @param int The owner id.
     */
    protected function set_owner_id($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    /**
     * Returns the learning object associated with this form.
     * @return ContentObject The learning object, or null if none.
     */
    function get_content_object()
    {
        /*
		 * For creation forms, $this->content_object is the default learning
		 * object and therefore may be abstract. In this case, we do not
		 * return it.
		 * For this reason, methods of this class itself will want to access
		 * $this->content_object directly, so as to take both the learning
		 * object that is being updated and the default learning object into
		 * account.
		 */
        if ($this->content_object instanceof AbstractContentObject)
        {
            return null;
        }
        return $this->content_object;
    }

    protected function get_content_object_type()
    {
        return $this->content_object->get_type();
    }

    protected function get_content_object_class()
    {
        return Utilities :: underscores_to_camelcase($this->get_content_object_type());
    }

    /**
     * Sets the learning object associated with this form.
     * @param ContentObject $content_object The learning object.
     */
    protected function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    function get_form_type()
    {
        return $this->form_type;
    }

    /**
     * Gets the categories defined in the user's repository.
     * @return array The categories.
     */
    function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_owner_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Builds a form to create a new learning object.
     */
    protected function build_creation_form($htmleditor_options = array())
    {
        $this->addElement('category', Translation :: get('GeneralProperties'));
        $this->build_basic_form($htmleditor_options);
        $this->addElement('category');
    }

    /**
     * Builds a form to edit a learning object.
     */
    protected function build_editing_form($htmleditor_options = array())
    {
        $object = $this->content_object;
        $owner = UserDataManager :: get_instance()->retrieve_user($this->get_owner_id());
        $quotamanager = new QuotaManager($owner);

        $this->addElement('category', Translation :: get('GeneralProperties'));
        $this->build_basic_form($htmleditor_options);
        if ($object->is_versionable()) // && $this->allow_new_version)
        {
            if ($object->get_version_count() < $quotamanager->get_max_versions($object->get_type()))
            {
                if ($object->is_versioning_required())
                {
                    $this->addElement('hidden', 'version');
                }
                else
                {
                    $this->add_element_hider('script_block');
                    $this->addElement('checkbox', 'version', Translation :: get('CreateAsNewVersion'), null, 'onclick="javascript:showElement(\'' . ContentObject :: PROPERTY_COMMENT . '\')"');
                    $this->add_element_hider('begin', ContentObject :: PROPERTY_COMMENT);
                    $this->addElement('text', ContentObject :: PROPERTY_COMMENT, Translation :: get('VersionComment'), array("size" => "50"));
                    $this->add_element_hider('end', ContentObject :: PROPERTY_COMMENT);
                }
            }
            else
            {
                $this->add_warning_message('version_quotum_message', null, Translation :: get('VersionQuotaExceeded'));
            }
        }
        $this->addElement('hidden', ContentObject :: PROPERTY_ID);
        $this->addElement('category');
    }

    /**
     * Builds a form to compare learning object versions.
     */
    private function build_version_compare_form()
    {
        $renderer = $this->defaultRenderer();
        $form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>

EOT;
        $renderer->setFormTemplate($form_template);
        $element_template = <<<EOT
	<div>
			<!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}
	</div>

EOT;
        $renderer->setElementTemplate($element_template);

        if (isset($this->extra['version_data']))
        {
            $object = $this->content_object;

            if ($object->is_latest_version())
            {
                $html[] = '<div class="versions" style="margin-top: 1em;">';
            }
            else
            {
                $html[] = '<div class="versions_na" style="margin-top: 1em;">';
            }

            $html[] = '<div class="versions_title">' . htmlentities(Translation :: get('Versions')) . '</div>';

            $this->addElement('html', implode("\n", $html));
            $this->add_element_hider('script_radio', $object);

            $i = 0;

            $radios = array();

            foreach ($this->extra['version_data'] as $version)
            {
                $versions = array();
                $versions[] = & $this->createElement('static', null, null, '<span ' . ($i == ($object->get_version_count() - 1) ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') . ' id="A' . $i . '">');
                $versions[] = & $this->createElement('radio', 'object', null, null, $version['id'], 'onclick="javascript:showRadio(\'B\',\'' . $i . '\')"');
                $versions[] = & $this->createElement('static', null, null, '</span>');
                $versions[] = & $this->createElement('static', null, null, '<span ' . ($i == 0 ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') . ' id="B' . $i . '">');
                $versions[] = & $this->createElement('radio', 'compare', null, null, $version['id'], 'onclick="javascript:showRadio(\'A\',\'' . $i . '\')"');
                $versions[] = & $this->createElement('static', null, null, '</span>');
                $versions[] = & $this->createElement('static', null, null, $version['html']);

                $this->addGroup($versions, null, null, "\n");
                $i ++;
            }

            //$this->addElement('submit', 'submit', Translation :: get('CompareVersions'));
            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('CompareVersions'), array('class' => 'normal compare'));
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

            $this->addElement('html', '</div>');
        }
    }

    /**
     * Builds a form to create or edit a learning object. Creates fields for
     * default learning object properties. The result of this function is equal
     * to build_creation_form()'s, but that one may be overridden to extend the
     * form.
     */
    private function build_basic_form($htmleditor_options = array())
    {
        //$this->add_textfield(ContentObject :: PROPERTY_TITLE, Translation :: get('Title'), true, 'size="100" style="width: 100%"');
        //$this->add_textfield(ContentObject :: PROPERTY_TITLE, Translation :: get('Title'), true, array('size' => '100'));
        $this->addElement('html', '<div id="message"></div>');
        $this->add_textfield(ContentObject :: PROPERTY_TITLE, Translation :: get(get_class($this) . 'Title'), true, array('size' => '100', 'id' => 'title', 'style' => 'width: 95%'));
        if ($this->allows_category_selection())
        {
            $select = $this->add_select(ContentObject :: PROPERTY_PARENT_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
            $select->setSelected($this->content_object->get_parent_id());
        }
        $value = PlatformSetting :: get('description_required', 'repository');
        $required = ($value == 1) ? true : false;
        $this->add_html_editor(ContentObject :: PROPERTY_DESCRIPTION, Translation :: get(get_class($this) . 'Description'), $required, $htmleditor_options);
    }

    /**
     * Adds a footer to the form, including a submit button.
     */
    protected function add_footer()
    {
        $object = $this->content_object;
        //$elem = $this->addElement('advmultiselect', 'ihsTest', 'Hierarchical select:', array("test"), array('style' => 'width: 20em;'), '<br />');


        if ($this->supports_attachments())
        {

            $html[] = '<script type="text/javascript">';
            $html[] =   'var support_attachments = true';
            $html[] = '</script>';
        	$this->addElement('html', implode("\n", $html));
        	if ($this->form_type != self :: TYPE_REPLY)
            {
                $attached_objects = $object->get_attached_content_objects();
                $attachments = Utilities :: content_objects_for_element_finder($attached_objects);
            }
            else
            {
                $attachments = array();
            }

            $los = RepositoryDataManager :: get_instance()->retrieve_content_objects(new EqualityCondition('owner_id', $this->owner_id));
            while ($lo = $los->next_result())
            {
                $defaults[$lo->get_id()] = array('title' => $lo->get_title(), 'description', $lo->get_description(), 'class' => $lo->get_type());
            }

            $url = $this->get_path(WEB_PATH) . 'repository/xml_feed.php';
            $locale = array();
            $locale['Display'] = Translation :: get('AddAttachments');
            $locale['Searching'] = Translation :: get('Searching');
            $locale['NoResults'] = Translation :: get('NoResults');
            $locale['Error'] = Translation :: get('Error');
            $hidden = true;

            $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify/jquery.uploadify.js'));
            $this->addElement('category', Translation :: get('Attachments'), 'content_object_attachments');
            $this->addElement('static', 'uploadify', Translation :: get('UploadDocument'), '<div id="uploadify"></div>');
            $elem = $this->addElement('element_finder', 'attachments', Translation :: get('SelectAttachment'), $url, $locale, $attachments, $options);
            $this->addElement('category');

            $elem->setDefaults($defaults);

            if ($id = $object->get_id())
            {
                $elem->excludeElements(array($object->get_id()));
            }
            //$elem->setDefaultCollapsed(count($attachments) == 0);
        }

        if (count($this->additional_elements) > 0)
        {
            $count = 0;
            foreach ($this->additional_elements as $element)
            {
                if ($element->getType() != 'hidden')
                    $count ++;
            }

            if ($count > 0)
            {
                $this->addElement('category', Translation :: get('AdditionalProperties'));
                foreach ($this->additional_elements as $element)
                {
                    $this->addElement($element);
                }
                $this->addElement('category');
            }
        }

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/content_object_form.js'));

        $buttons = array();

        switch ($this->form_type)
        {
            case self :: TYPE_COMPARE :
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Compare'), array('class' => 'normal compare'));
                break;
            case self :: TYPE_CREATE :
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
                break;
            case self :: TYPE_EDIT :
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
                break;
            case self :: TYPE_REPLY :
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Reply'), array('class' => 'positive send'));
                break;
            default :
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
                break;
        }

        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $lo = $this->content_object;
        $defaults[ContentObject :: PROPERTY_ID] = $lo->get_id();

        if ($this->form_type == self :: TYPE_REPLY)
        {
            $defaults[ContentObject :: PROPERTY_TITLE] = Translation :: get('ReplyShort') . ' ' . $lo->get_title();
        }
        else
        {
            $defaults[ContentObject :: PROPERTY_TITLE] = $defaults[ContentObject :: PROPERTY_TITLE] == null ? $lo->get_title() : $defaults[ContentObject :: PROPERTY_TITLE];
            $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
        }

        if ($lo->is_versioning_required() && $this->form_type == self :: TYPE_EDIT)
        {
            $defaults['version'] = 1;
        }

        parent :: setDefaults($defaults);
    }

    function setParentDefaults($defaults)
    {
        parent :: setDefaults($defaults);
    }

    function set_values($defaults)
    {
        parent :: setDefaults($defaults);
    }

    /**
     * Creates a learning object from the submitted form values. Traditionally,
     * you override this method to ensure that the form's learning object is
     * set to the object that is to be created, and call the super method.
     * @return ContentObject The newly created learning object.
     */
    function create_content_object()
    {
        $values = $this->exportValues();

        $object = $this->content_object;
        $object->set_owner_id($this->get_owner_id());
        $object->set_title($values[ContentObject :: PROPERTY_TITLE]);
        $desc = $values[ContentObject :: PROPERTY_DESCRIPTION] ? $values[ContentObject :: PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc);
        if ($this->allows_category_selection())
        {
            $object->set_parent_id($values[ContentObject :: PROPERTY_PARENT_ID]);
        }

        $object->create();

        if($object->has_errors())
        {
            //TODO: display errors
            //DebugUtilities :: show($object->get_errors());
            return null;
        }

        // Process includes
        ContentObjectIncludeParser :: parse_includes($this);

        // Process attachments
        if ($object->supports_attachments())
        {
            foreach ($values['attachments'] as $aid)
            {
                $aid = str_replace('lo_', '', $aid['id']);
                $object->attach_content_object($aid);
            }
        }
        return $object;
    }

    function compare_content_object()
    {
        $values = $this->exportValues();
        $ids = array();
        $ids['object'] = $values['object'];
        $ids['compare'] = $values['compare'];
        return $ids;
    }

    /**
     * Updates a learning object with the submitted form values. Traditionally,
     * you override this method to first set values for the necessary
     * additional learning object properties, and then call the super method.
     * @return boolean True if the update succeeded, false otherwise.
     */
    function update_content_object()
    {
        $object = $this->content_object;
        $values = $this->exportValues();
        
        $object->set_title($values[ContentObject :: PROPERTY_TITLE]);

        $desc = $values[ContentObject :: PROPERTY_DESCRIPTION] ? $values[ContentObject :: PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc ? $desc : '');

        if ($this->allows_category_selection())
        {
            $parent = $values[ContentObject :: PROPERTY_PARENT_ID];
            if ($parent != $object->get_parent_id())
            {
                if ($object->move_allowed($parent))
                {
                    $object->set_parent_id($parent);

                }
                else
                {
                    /*
					 * TODO: Make this more meaningful, e.g. by returning error
					 * constants instead of booleans, like
					 * ContentObjectForm :: SUCCESS (not implemented).
					 */
                    return self :: RESULT_ERROR;
                }
            }
        }

        if (isset($values['version']) && $values['version'] == 1)
        {
            $object->set_comment(nl2br($values[ContentObject :: PROPERTY_COMMENT]));
            $result = $object->version();
        }
        else
        {
            $result = $object->update();
        }

        if($object->has_errors())
        {
            //TODO: display errors
            //DebugUtilities :: show($object->get_errors());
            return false;
        }

        // Process includes
        ContentObjectIncludeParser :: parse_includes($this);

        //$include_parser->parse_editors();


        // Process attachments
        if ($object->supports_attachments())
        {
            /*
			 * TODO: Make this faster by providing a function that matches the
			 *      existing IDs against the ones that need to be added, and
			 *      attaches and detaches accordingly.
			 */
            foreach ($object->get_attached_content_objects() as $o)
            {
                $object->detach_content_object($o->get_id());
            }
            
            foreach ($values['attachments'] as $aid)
            {
                $aid = str_replace('lo_', '', $aid['id']);
                $object->attach_content_object($aid);
            }
        }
        return $result;
    }

    function is_version()
    {
        $values = $this->exportValues();
        return (isset($values['version']) && $values['version'] == 1);
    }

    /**
     * Checks whether the learning object that is being created or edited may
     * have learning objects attached to it.
     * @return boolean True if attachments are supported, false otherwise.
     */
    function supports_attachments()
    {
        $lo = $this->content_object;
        return $lo->supports_attachments();
    }

    /**
     * Displays the form
     */
    function display()
    {
        $owner = UserDataManager :: get_instance()->retrieve_user($this->get_owner_id());
        $quotamanager = new QuotaManager($owner);
        if ($this->form_type == self :: TYPE_CREATE && $quotamanager->get_available_database_space() <= 0)
        {
            Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfContentObjectsReached')));
        }
        else
        {
            parent :: display();
        }
    }

    private function allows_category_selection()
    {
        $lo = $this->content_object;
        return ($this->form_type == self :: TYPE_CREATE || $this->form_type == self :: TYPE_REPLY || $lo->get_parent_id());
    }

    /**
     * Creates a form object to manage a learning object.
     * @param int $form_type The form type; either
     *                       ContentObjectForm :: TYPE_CREATE or
     *                       ContentObjectForm :: TYPE_EDIT.
     * @param ContentObject $content_object The object to create or update.
     *                                        May be an AbstractContentObject
     *                                        upon creation.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    static function factory($form_type, $content_object, $form_name, $method = 'post', $action = null, $extra = null, $additional_elements = array(), $allow_new_version = true, $form_variant = null)
    {
        $type = $content_object->get_type();

        if ($form_variant)
        {
            $class = ContentObject :: type_to_class($type) . ContentObject :: type_to_class($form_variant) . 'Form';
            require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '_'. $form_variant .'_form.class.php';
        }
        else
        {
            $class = ContentObject :: type_to_class($type) . 'Form';
            require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '_form.class.php';
        }

//        $class = ContentObject :: type_to_class($type) . 'Form';
//        require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '_form.class.php';
        return new $class($form_type, $content_object, $form_name, $method, $action, $extra, $additional_elements, $allow_new_version);
    }

    /**
     * Validates this form
     * @see FormValidator::validate
     */
    function validate()
    {
        if ($this->isSubmitted() && $this->form_type == self :: TYPE_COMPARE)
        {
            $values = $this->exportValues();
            if (! isset($values['object']) || ! isset($values['compare']))
            {
                return false;
            }
        }

        return parent :: validate();
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }
}
?>