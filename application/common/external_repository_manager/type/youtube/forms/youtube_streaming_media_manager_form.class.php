<?php
/**
 * $Id: youtube_streaming_media_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package 
 */

class YoutubeStreamingMediaManagerForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'GroupUpdated';
    const RESULT_ERROR = 'GroupUpdateFailed';
    
    const VIDEO_TITLE = 'title';
    const VIDEO_CATEGORY = 'category';
    const VIDEO_TAGS = 'tags';
    const VIDEO_DESCRIPTION = 'description';

    private $application;
    private $video_entry;
    private $form_type;
    private $streaming_media_object;

    function YoutubeStreamingMediaManagerForm($form_type, $action, $application)
    {
        parent :: __construct('youtube_upload', 'post', $action);
        
        $this->application = $application;
       
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function set_streaming_media_object(YoutubeStreamingMediaObject $streaming_media_object)
    {
    	$this->streaming_media_object = $streaming_media_object;
    	$this->addElement('hidden', StreamingMediaObject::PROPERTY_ID);
    	$defaults[YoutubeStreamingMediaObject::PROPERTY_TITLE] = $streaming_media_object->get_title();
    	$defaults[YoutubeStreamingMediaObject::PROPERTY_DESCRIPTION] = $streaming_media_object->get_description();
    	$defaults[YoutubeStreamingMediaObject::PROPERTY_CATEGORY] = $streaming_media_object->get_category();
    	$defaults[YoutubeStreamingMediaObject::PROPERTY_TAGS] = $this->get_tags();
    	parent :: setDefaults($defaults);
    }
    
    public function get_tags()
    {
    	$streaming_media_object = $this->streaming_media_object;
    	$tags = $streaming_media_object->get_tags();
    	return implode(",", $tags);
    }
    
    function build_basic_form()
    {    	
    	$this->addElement('text', YoutubeStreamingMediaObject::PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(YoutubeStreamingMediaObject::PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        
        $this->addElement('select', YoutubeStreamingMediaObject::PROPERTY_CATEGORY, Translation :: get('Category'), $this->get_youtube_categories());
                
        $this->addElement('textarea', YoutubeStreamingMediaObject::PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "1", "cols" => "80"));
        $this->addRule(YoutubeStreamingMediaObject::PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('textarea', YoutubeStreamingMediaObject::PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "110"));
    }
    
    function get_youtube_categories()
    {
    	$connector = YoutubeStreamingMediaConnector::get_instance($this->application);
    	return $connector->retrieve_categories();
    }

    function build_editing_form()
    {      
        $this->build_basic_form();
        
        $this->addElement('hidden', Group :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_video_entry()
    {
        $youtube = YoutubeStreamingMediaConnector::get_instance($this->application);
    	
    	$values = $this->exportValues();
        
    	return $youtube->update_youtube_video($values);
                       
        /*if ($value)
        {
            Event :: trigger('update', 'video_entry', array('video_entry_id' => $this->video_entry->getId()));
        }
        return $value;*/
    }

    function get_upload_token()
    {
        $values = $this->exportValues();
        
        $connector = YoutubeStreamingMediaConnector::get_instance($this->application);
        return $connector->get_upload_token($values);
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
//        $defaults[self :: VIDEO_TITLE] = $this->video_entry->getVideoTitle();
//        $defaults[self :: VIDEO_CATEGORY] = $this->video_entry->getVideoCategory();
//        $defaults[self :: VIDEO_TAGS] = $this->video_entry->getVideoTags();
//        $defaults[self :: VIDEO_DESCRIPTION] = $this->video_entry->getVideoDescription();
        parent :: setDefaults($defaults);
    }

    function get_entry_video()
    {
        return $this->entry_video;
    }
}
?>