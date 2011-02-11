<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use common\libraries\Application;
use repository\content_object\link\Link;

/**
 * $Id: linker.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.block
 */
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/repository_block.class.php';

class RepositoryLinker extends RepositoryBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Link:: get_type_name())) . 'logo/' . $size . '.png';
        }
    }

    function display_header() {
        $html = array();
        $icon = $this->get_default_image_path();

        $html[] = '<div class="block" id="block_' . $this->get_block_info()->get_id() . '" style="background-image: url(' . $icon . ');">';
        $html[] = $this->display_title();
        $html[] = '<div class="description"' . ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') . '>';

        return implode("\n", $html);
    }

    function as_html() {
        $configuration = $this->get_configuration();
        $object_id = $configuration['use_object'];

        $html = array();

        if (!isset($object_id) || $object_id == 0) {
            $html[] = $this->display_header();
            $html[] = Translation :: get('ConfigureBlockFirst', null, HomeManager :: APPLICATION_NAME);
            $html[] = $this->display_footer();
        } else {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($configuration['use_object']);

            //$icon = Theme::get_content_object_image_path($content_object->get_type(), Theme::ICON_MEDIUM);
            $icon = self::get_default_image_path();

            $html[] = '<div class="block" id="block_' . $this->get_block_info()->get_id() . '" style="background-image: url(' . $icon . ');">';
            $html[] = '<div class="title"><div style="float: left;">' . $content_object->get_title() . '</div>';
            $html[] = $this->display_actions();
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
            $html[] = '<div class="description"' . ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') . '>';
            $html[] = $content_object->get_description();
            $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($content_object->get_url()) . '">' . htmlentities($content_object->get_url()) . '</a></div>';
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

}

?>