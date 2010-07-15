<?php
abstract class ContentObjectRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';

    protected $repository_browser;

    function ContentObjectRenderer($repository_browser)
    {
        $this->repository_browser = $repository_browser;
    }

    function get_repository_browser()
    {
        return $this->repository_browser;
    }

    static function factory($type, $external_repository_browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_content_object_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ContentObjectRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'ContentObjectRenderer';
        return new $class($external_repository_browser);
    }

    abstract function as_html();

    public function get_parameters()
    {
        return $this->get_repository_browser()->get_parameters();
    }

    public function get_condition()
    {
        return $this->get_repository_browser()->get_condition();
    }

    function count_content_objects($condition)
    {
        return $this->get_repository_browser()->count_content_objects($condition);
    }

    function count_categories($conditions = null)
    {
        return $this->get_repository_browser()->count_categories($conditions);
    }

    function retrieve_content_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_repository_browser()->retrieve_content_objects($condition, $order_property, $offset, $count);
    }

    function get_content_object_actions(ExternalRepositoryObject $object)
    {
        return $this->get_repository_browser()->get_content_object_actions($object);
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    function get_content_object_viewing_url($object)
    {
        return $this->get_repository_browser()->get_content_object_viewing_url($object);
    }

    function get_type_filter_url($type)
    {
        return $this->get_repository_browser()->get_type_filter_url($type);
    }

    function get_content_object_editing_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_editing_url($content_object);
    }

    function get_content_object_recycling_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_recycling_url($content_object);
    }

    function get_content_object_moving_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_moving_url($content_object);
    }

    function get_content_object_metadata_editing_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_metadata_editing_url($content_object);
    }

    function get_content_object_rights_editing_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_rights_editing_url($content_object);
    }

    function get_content_object_exporting_url($content_object)
    {
        return $this->get_repository_browser()->get_content_object_exporting_url($content_object);
    }

    function get_publish_content_object_url($content_object)
    {
        return $this->get_repository_browser()->get_publish_content_object_url($content_object);
    }

    function get_copy_content_object_url($content_object_id, $to_user_id)
    {
        return $this->get_repository_browser()->get_copy_content_object_url($content_object_id, $to_user_id);
    }

    function get_browse_complex_content_object_url($content_object)
    {
        return $this->get_repository_browser()->get_browse_complex_content_object_url($content_object);
    }

    function get_document_downloader_url($document_id)
    {
        return $this->get_repository_browser()->get_document_downloader_url($document_id);
    }

    function get_user_id()
    {
        return $this->get_repository_browser()->get_user_id();
    }

    function get_user()
    {
        return $this->get_repository_browser()->get_user();
    }

}
?>