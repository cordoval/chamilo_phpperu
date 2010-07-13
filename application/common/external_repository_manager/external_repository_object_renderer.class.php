<?php
abstract class ExternalRepositoryObjectRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';

    protected $external_repository_browser;

    function ExternalRepositoryObjectRenderer($external_repository_browser)
    {
        $this->external_repository_browser = $external_repository_browser;
    }

    function get_external_repository_browser()
    {
        return $this->external_repository_browser;
    }

    static function factory($type, $external_repository_browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_external_repository_object_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ExternalRepositoryObjectRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryObjectRenderer';
        return new $class($external_repository_browser);
    }

    abstract function as_html();

    public function get_parameters()
    {
        return $this->get_external_repository_browser()->get_parameters();
    }

    public function get_condition()
    {
        return $this->get_external_repository_browser()->get_condition();
    }

    function count_external_repository_objects($condition)
    {
        return $this->get_external_repository_browser()->count_external_repository_objects($condition);
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_external_repository_browser()->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
    }

    function get_external_repository_browser_gallery_table_property_model()
    {
        return $this->get_external_repository_browser()->get_external_repository_browser_gallery_table_property_model();
    }

    function get_external_repository_browser_gallery_table_cell_renderer()
    {
        return $this->get_external_repository_browser()->get_external_repository_browser_gallery_table_cell_renderer();
    }

    function is_editable($id)
    {
        return $this->get_external_repository_browser()->get_parent()->is_editable($id);
    }

    function is_stand_alone()
    {
        return $this->get_external_repository_browser()->get_parent()->is_stand_alone();
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_external_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    function get_external_repository_object_viewing_url($object)
    {
        return $this->get_external_repository_browser()->get_external_repository_object_viewing_url($object);
    }
}
?>