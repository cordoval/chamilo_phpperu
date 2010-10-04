<?php
/**
 * $Id: metadata_component.class.php 207 2009-11-13 13:09:14Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */
require_once Path :: get_common_path() . 'debug/debug_utilities.class.php';
require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_mapper.class.php';
require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_langstring_mapper.class.php';


class RepositoryManagerMetadataComponent extends RepositoryManager
{
    const METADATA_FORMAT_LOM = 'lom';
    const METADATA_FORMAT_DUBLINCORE = 'dc';
    
    const METADATA_TRANSLATION_PREFIX = 'Metadata';

    /**
     * Check wether a learning object can be retrieved by using the URL params
     * @return boolean
     */
    function check_content_object_from_params()
    {
        $content_object = $this->get_content_object_from_params();
        if (isset($content_object))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function get_content_object_from_params()
    {
        /*
	     * Check if the learning object is given in the URL params  
	     */
        $lo_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        
        if (isset($lo_id) && is_numeric($lo_id))
        {
            /*
	         * Check if the learning object does exist 
	         */
            $dm = RepositoryDataManager :: get_instance();
            return $dm->retrieve_content_object($lo_id);
        }
        else
        {
            return null;
        }
    }

    function display_lom_xml($content_object, $metadata_mapper, $format_for_html_page = false)
    {
        if ($format_for_html_page)
        {
            echo '<div class="metadata" style="background-image: url(' . Theme :: get_common_image_path() . 'place_metadata.png);">';
            echo '<div class="title">' . $content_object->get_title() . '</div>';
            echo '<pre>';
        }
        
        $metadata_mapper->export_metadata($format_for_html_page);
        
        if ($format_for_html_page)
        {
            echo '</pre>';
            echo '</div>';
        }
    }

    /**
     * Return the metadata type that is requested.
     * 
     * @return string The type of metadata requested. Default returned is LOM.
     */
    function get_metadata_type()
    {
        $metadata_type = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (! isset($metadata_type))
        {
            $metadata_type = self :: METADATA_FORMAT_LOM;
        }
        
        return $metadata_type;
    }

    /**
     * Get all the catalogs stored in the metadata catalog table
     *
     * @return array Array of 'catalog type' => catalog array
     */
    function get_catalogs()
    {
        $catalogs = array();
        
        $catalogs[Catalog :: CATALOG_LOM_LANGUAGE] = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_LANGUAGE));
        $catalogs[Catalog :: CATALOG_LOM_ROLE] = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_ROLE));
        $catalogs[Catalog :: CATALOG_LOM_COPYRIGHT] = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_COPYRIGHT));
        $catalogs[Catalog :: CATALOG_DAY] = Catalog :: get_catalog(Catalog :: CATALOG_DAY);
        $catalogs[Catalog :: CATALOG_MONTH] = Catalog :: get_catalog(Catalog :: CATALOG_MONTH);
        $catalogs[Catalog :: CATALOG_YEAR] = Catalog :: get_catalog(Catalog :: CATALOG_YEAR);
        $catalogs[Catalog :: CATALOG_HOUR] = Catalog :: get_catalog(Catalog :: CATALOG_HOUR);
        $catalogs[Catalog :: CATALOG_MIN] = Catalog :: get_catalog(Catalog :: CATALOG_MIN);
        $catalogs[Catalog :: CATALOG_SEC] = Catalog :: get_catalog(Catalog :: CATALOG_SEC);
        
        return $catalogs;
    }

    private function get_metadata_specific_translation($catalog)
    {
        foreach ($catalog as $value => $title)
        {
            $catalog[$value] = Translation :: get(self :: METADATA_TRANSLATION_PREFIX . $title);
        }
        
        return $catalog;
    }

}
?>