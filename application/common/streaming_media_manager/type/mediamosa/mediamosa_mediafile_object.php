<?php
/**
 * Description of mediamosa_transcoding_profile
 *
 * @author jevdheyd
 */
class MediamosaMediafileObject {

    private $default_properties;

    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_PARENT = 'parent';
    const PROPERTY_URL = 'url';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_FPS = 'fps';
    const PROPERTY_VIDEO_CODEC = 'video_codec';
    const PROPERTY_AUDIO_CODEC = 'audio_codec';
    const PROPERTY_COLORSPACE = 'colorspace';
    const PROPERTY_SAMPLE_RATE = 'sample_rate';
    const PROPERTY_CHANNELS = 'channels';

    /**
     * @return the $default_properties
     */
    public function get_default_properties()
    {
        return $this->default_properties;
    }

    /**
     * Gets a default property of this data class object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return (isset($this->defaultProperties) && array_key_exists($name, $this->defaultProperties))
        	? $this->defaultProperties[$name]
        	: null;
    }

    /**
     * @param $default_properties the $default_properties to set
     */
    public function set_default_properties($default_properties)
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Sets a default property of this data class by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_PARENT;
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_property_names[] = self :: PROPERTY_WIDTH;
        $extended_property_names[] = self :: PROPERTY_HEIGHT;
        $extended_property_names[] = self :: PROPERTY_FPS;
        $extended_property_names[] = self :: PROPERTY_COLORSPACE;
        $extended_property_names[] = self :: PROPERTY_VIDEO_CODEC;
        $extended_property_names[] = self :: PROPERTY_AUDIO_CODEC;
        $extended_property_names[] = self :: PROPERTY_SAMPLE_RATE;
        $extended_property_names[] = self :: PROPERTY_CHANNELS;

        return $extended_property_names;
    }

	public function get_additional_properties()
    {
        return $this->additional_properties;
    }

	/**
     * @param $additional_properties the $additional_properties to set
     */
    public function set_additional_properties($additional_properties)
    {
        $this->additional_properties = $additional_properties;
    }

    /**
     * Sets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_additional_property($name, $value)
    {
        //$this->check_for_additional_properties();
        $this->additional_properties[$name] = $value;
    }

    /**
     * Gets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     */
    function get_additional_property($name)
    {
        return $this->additional_properties[$name];
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    function set_width($width)
    {
        $this->set_default_property(self :: PROPERTY_WIDTH, $width);
    }

    function get_width($width)
    {
        return $this->get_default_property(self :: PROPERTY_WIDTH);
    }

    function set_height($height)
    {
        $this->set_default_property(self :: PROPERTY_HEIGHT, $height);
    }

    function get_height($height)
    {
       return  $this->get_default_property(self :: PROPERTY_HEIGHT);
    }

    function set_fps($fps)
    {
        $this->set_default_property(self :: PROPERTY_FPS, $fps);
    }

    function get_fps($fps)
    {
        return $this->get_default_property(self :: PROPERTY_FPS);
    }

    function set_video_codec($video_codec)
    {
        $this->set_default_property(self :: PROPERTY_VIDEO_CODEC, $video_codec);
    }

    function get_video_codec()
    {
        return $this->get_default_property(self :: PROPERTY_VIDEO_CODEC);
    }

    function set_audio_codec($audio_codec)
    {
        $this->set_default_property(self :: PROPERTY_AUDIO_CODEC, $audio_codec);
    }

    function get_audio_codec()
    {
        return $this->get_default_property(self :: PROPERTY_AUDIO_CODEC);
    }

    function set_sample_rate($sample_rate)
    {
        $this->set_default_property(self :: PROPERTY_SAMPLE_RATE, $sample_rate);
    }

    function get_sample_rate()
    {
        return $this->get_default_property(self :: PROPERTY_SAMPLE_RATE);
    }

    function set_colorspace($colorspace)
    {
        $this->set_default_property(self :: PROPERTY_COLORSPACE, $colorspace);
    }

    function get_colorspace()
    {
        return $this->get_default_property(self :: PROPERTY_COLORSPACE);
    }

    function set_channels($channels)
    {
        $this->set_default_property(self :: PROPERTY_CHANNELS, $channels);
    }

    function get_channels()
    {
        return $this->get_default_property(self :: PROPERTY_CHANNELS);
    }

}
?>
