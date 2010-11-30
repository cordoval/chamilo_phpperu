<?php

namespace repository;

use common\libraries\Utilities;
use common\libraries\Session;
use common\libraries\Filecompression;
use common\libraries\Filesystem;
use common\libraries\Text;

/**
 *
 * Base class for Object Import. I.e. single file import per opposition to the CP multi file import.
 * Each subclass provides support for a specific format.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpObjectImportBase {

    /**
     * @return CpObjectImportBase
     */
    public static function factory() {
        $result = new BufferedCpImport();
        $aggregate = new CpObjectImportAggregate($result);
        $result->set_child($aggregate);
        $directory = dirname(__FILE__) . '/import/';
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));
        foreach ($files as $file) {
            $path = $directory . $file;
            if (strpos($file, '.class.php') !== false) {
                include_once($path);
                $class = str_replace('.class.php', '', $file);
                $class = __NAMESPACE__ . '\\' . Utilities::underscores_to_camelcase($class);
                $importer = new $class($aggregate);
                $aggregate->add($importer);
            }
        }
        $aggregate->sort();
        return $result;
    }

    private $parent;

    public function __construct($parent = null) {
        $this->parent = $parent;
    }

    /**
     * Direct parent of the current object. Must be an aggregated importer.
     */
    public function get_parent() {
        return $this->parent;
    }

    /**
     * Root importer. I.e. the top parent object.
     * Make calls on the root to ensure the whole tree is traversed.
     */
    public function get_root() {
        if (empty($this->parent)) {
            return $this;
        } else {
            return $this->parent->get_root();
        }
    }

    /**
     * Importer's name. By default the class name without the trailing CpImport.
     */
    public function get_name() {
        $result = get_class($this);

        $result = str_replace(__NAMESPACE__ . '\\', '', $result);
        $result = str_replace('CpImport', '', $result);
        return $result;
    }

    /**
     * File extentions supported by the importer.
     * Defaults to importer's name.
     */
    public function get_extentions() {
        $name = strtolower($this->get_name());

        if (!empty($name)) {
            $result = array($name);
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * Importer's weight.
     * Importers with a low value are tested first. Importers with a hight value are tested last.
     */
    public function get_weight() {
        return 0;
    }

    /**
     * Returns true if it accepts to import the file passed as parameters.
     *
     * @param boolean $settings
     */
    public function accept($settings) {
        $path = $settings->get_path();
        $file_ext = $settings->get_extention();
        $extentions = $this->get_extentions();
        foreach ($extentions as $ext) {
            if ($ext == $file_ext) {
                return true;
            }
        }
        return false;
    }

    /**
     * Import file. Returns the new created object on success or false on failure.
     * Delegate works to process_import
     * @param $settings
     */
    public function import(ObjectImportSettings $settings) {
        if ($this->accept($settings)) {
            if ($result = $this->process_import($settings)) {
                if ($result instanceof ContentObject && $result->has_errors()) {
                    $errors = $result->get_errors();
                    $settings->get_log()->error($errors);
                    return false;
                } else {//i.e. an array
                    return $result;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Performs the actual importation work. Function should be overriden by sub classes.
     * @param unknown_type $settings
     */
    protected function process_import($settings) {
        return false;
    }

    /**
     * Extract a zip file to a temp directory
     *
     * @param string $path path to the zip file
     * @param boolean $delete_file if true zip file is deleted, if false zip file is preserved
     * @return the temp directory path where the file has been extracted
     */
    protected function extract($path, $delete_file = true) {
        $zip = Filecompression::factory();
        $result = $zip->extract_file($path) . '/';
        if ($delete_file) {
            Filesystem::remove($path);
        }
        return $result;
    }

    /**
     * Returns a temp directory under $root.
     * @param string $root
     */
    protected function get_temp_directory($root) {
        $result = $root . '/d' . Session::get_user_id() . sha1(time() . uniqid()) . '/';
        return $result;
    }

    protected function save(ObjectImportSettings $settings, $object) {
        $title = $object->get_title();
        if (empty($title)) {
            $object->set_title($this->get_title($settings));
        }
        $description = $object->get_description();
        if (empty($description)) {
            $object->set_description($this->get_description($settings));
        }
        $owner_id = $object->get_owner_id();
        if (empty($owner_id)) {
            $object->set_owner_id($this->get_owner_id($settings));
        }
        $parent_id = $object->get_parent_id();
        if (empty($parent_id)) {
            $object->set_parent_id($this->get_parent_id($settings));
        }
        return $object->save();
    }

    protected function get_title($settings) {
        return $settings->get_filename();
    }

    protected function get_description($settings) {
        return $settings->get_filename();
    }

    protected function get_owner_id($settings) {
        return $settings->get_user()->get_id();
    }

    protected function get_parent_id($settings) {
        return $settings->get_category_id();
    }

    /**
     * If the html file contains a $name meta tag with name equals to $name returns its content attribute.
     * Otherwise returns $default.
     *
     * @param $settings
     * @param string $name the meta tag name to search for
     * @returns string the content attribute of the found meta tag or '' if not found.
     */
    protected function get_meta(ObjectImportSettings $settings, $name, $default = '') {
        if ($doc = $settings->get_dom()) {
            $name = strtolower($name);
            $list = $doc->getElementsByTagName('meta');
            foreach ($list as $meta) {
                if (strtolower($meta->getAttribute('name')) == $name) {
                    return $meta->getAttribute('content');
                }
            }
        }
        return $default;
    }

    /**
     * Returns the inner html of a node.
     *
     * @param $node
     */
    protected function get_innerhtml($node) {
        $result = '';
        $doc = $node->ownerDocument;
        $children = $node->childNodes;
        foreach ($children as $child) {
            $result .= $doc->saveXml($child);
        }
        return $result;
    }

    protected function translate_text($settings, $html) {
        $result = $html;
        $images = Text::fetch_tag_into_array($html, '<img>');
        foreach ($images as $image) {
            $src = $image['src'];
            $old_src = 'src="' . $src . '"';
            $new_src = $this->translate_path($settings, $src);
            $new_src = 'src="' . $new_src . '"';
            $result = str_replace($old_src, $new_src, $result);
        }
        return $result;
    }

    private function translate_path($settings, $path) {
        $file_path = $settings->get_directory() . $path;
        $object_settings = $settings->copy($file_path);
        if ($object = $this->get_root()->import($object_settings)) {
            $id = $object->get_id();
            $result = "core.php?go=document_downloader&amp;display=1&amp;object=$id&amp;application=repository";
            return $result;
        } else {
            return $path;
        }
    }

}

?>