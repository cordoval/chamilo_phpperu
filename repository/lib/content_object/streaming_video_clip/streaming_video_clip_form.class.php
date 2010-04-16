<?php

/**
 * Description of StreamingVideoClipForm class
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../streaming_video_clip_manager/streaming_video_clip_manager.class.php';
//require_once dirname(__FILE__) . '/../../repository_manager/component/streaming_video_clip_creator.class.php';

class StreamingVideoClipForm extends ContentObjectForm {

   protected function __construct($form_type, $content_object, $form_name, $method = 'post', $action = null, $extra = null, $additional_elements, $allow_new_version = true)
    {
        //TODO:jens-->see if this is correct
        //$form_type_deviate = 5;
        //$form_name .= '_' . Request::get('type');

        //parent :: __construct($form_type_deviate, $content_object, $form_name, $method = 'post', $action = null, $extra = null, $additional_elements, $allow_new_version = true);
        
        $this->FormValidator($form_name, $method, $action);

        $this->form_type = $form_type;
        $this->set_content_object($content_object);
        $this->owner_id = $content_object->get_owner_id();
        $this->extra = $extra;
        $this->additional_elements = $additional_elements;
        $this->allow_new_version = $allow_new_version;

        $this->form_type = $form_type;

        if ($this->form_type == parent :: TYPE_EDIT || $this->form_type == self :: TYPE_REPLY)
        {
            //TODO:implement
        }
        elseif ($this->form_type == parent :: TYPE_CREATE)
        {
             $sub_manager = new StreamingVideoClipManager();
             $sub_manager->create(&$this);
        }
        elseif ($this->form_type == parent :: TYPE_COMPARE)
        {
             //TODO:implement
        }
        if ($this->form_type != parent :: TYPE_COMPARE)
        {
             $this->add_progress_bar(2);
             $this->add_footer();
        }

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
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('CreateStreamingVideo'), array('class' => 'positive'));
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

}
?>