<?php
class MatterhornExternalRepositoryObjectAttachment
{
    private $ref;
    private $type;
    private $id;
    private $mimetype;
    private $tags;
    private $url;

    /**
     * @return the $ref
     */
    public function get_ref()
    {
        return $this->ref;
    }

    /**
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return the $mimetype
     */
    public function get_mimetype()
    {
        return $this->mimetype;
    }

    /**
     * @return the $tags
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * @param $ref the $ref to set
     */
    public function set_ref($ref)
    {
        $this->ref = $ref;
    }

    /**
     * @param $type the $type to set
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param $mimetype the $mimetype to set
     */
    public function set_mimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * @param $tags the $tags to set
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    private function get_type_as_image()
    {
        $result = str_replace('/', '_', $this->get_type());
        $result = str_replace('+', '_', $result);
        return Theme :: get_common_image('external_repository/matterhorn/attachments/' . $result, 'png', $this->get_type(), '', ToolbarItem :: DISPLAY_ICON);
    }

    public function as_string()
    {
        $html = array();

        $html[] = Utilities :: mimetype_to_image($this->get_mimetype());
        $html[] = $this->get_type_as_image();
        return implode(" ", $html);
    }
}