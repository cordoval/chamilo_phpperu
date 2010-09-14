:: E<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_object.class.php';

/**
 *
 * @author magali.gillard
 *
 */
class MatterhornExternalRepositoryObject extends StreamingMediaExternalRepositoryObject
{
    const OBJECT_TYPE = 'matterhorn';

    const PROPERTY_SERIES = 'series';
    const PROPERTY_CONTRIBUTORS = 'contributors';
    const PROPERTY_SUBJECTS = 'subjects';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_TRACKS = 'tracks';
    const PROPERTY_STREAMING = 'streaming';
    const PROPERTY_ATTACHMENTS = 'attachments';

    function get_attachments()
    {
    	return $this->get_default_property(self :: PROPERTY_ATTACHMENTS);
    }

    function set_attachments($attachments)
    {
    	return $this->set_default_property(self :: PROPERTY_ATTACHMENTS, $attachments);
    }

	function get_series()
    {
        return $this->get_default_property(self :: PROPERTY_SERIES);
    }

    function set_series($series)
    {
        return $this->set_default_property(self :: PROPERTY_SERIES, $series);
    }

	function get_contributors()
    {
        return $this->get_default_property(self :: PROPERTY_CONTRIBUTORS);
    }

    function set_contributors($contributors)
    {
        return $this->set_default_property(self :: PROPERTY_CONTRIBUTORS, $contributors);
    }

    function get_subjects()
    {
        return $this->get_default_property(self :: PROPERTY_SUBJECTS);
    }

    function set_subjects($subjects)
    {
        return $this->set_default_property(self :: PROPERTY_SUBJECTS, $subjects);
    }

   function get_language()
   {
   		return $this->get_default_property(self :: PROPERTY_LANGUAGE);
   }

    function set_language($language)
    {
    	return $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

	function set_streaming($streaming)
    {
        $this->set_default_property(self :: PROPERTY_STREAMING, $streaming);
    }

    function get_streaming()
    {
        return $this->get_default_property(self :: PROPERTY_STREAMING);
    }

	function set_tracks($tracks)
    {
        $this->set_default_property(self :: PROPERTY_TRACKS, $tracks);
    }

    function get_tracks()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKS);
    }

    function set_license($license)
    {
    	$this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    function get_license()
    {
    	return $this->get_default_property(self :: PROPERTY_LICENSE);
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SUBJECTS, self :: PROPERTY_LANGUAGE, self :: PROPERTY_CONTRIBUTORS, self :: PROPERTY_CONTRIBUTORS, self :: PROPERTY_TRACKS, self :: PROPERTY_LICENCE, self :: PROPERTY_STREAMING));
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
	}

	public function add_track($track)
	{
		$tracks = $this->get_tracks();
		$tracks[] = $track;
		$this->set_tracks($tracks);
	}

	public function add_attachment($attachment)
	{
		$attachments = $this->get_attachments();
		$attach = explode('/', $attachment->get_type());
		
		$attachments[$attach[1]] = $attachment;
		$this->set_attachments($attachments);
	}

	public function get_search_preview()
	{
		$attachments = $this->get_attachments();
		
		return $attachments['search+preview'];
	}
	
	public function is_usable()
	{
		return ExternalRepositoryObject::is_usable();
		
	}
}
?>