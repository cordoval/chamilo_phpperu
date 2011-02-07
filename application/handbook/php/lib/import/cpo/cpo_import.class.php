<?php

namespace application\handbook;

use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\Filecompression;
use common\libraries\Filesystem;
use repository\ContentObjectImport;
use repository\content_object\learning_path_item\LearningPathItem;
use repository\content_object\document\Document;
use repository\content_object\hotspot_question\HotspotQuestion;
use repository\content_object\hotpotatoes\Hotpotatoes;
use repository\content_object\learning_path\LearningPath;
use repository\content_object\scorm_item\ScormItem;
use DOMDocument;
use common\libraries\Text;
use application\metadata\MetadataNamespace;
use application\metadata\MetadataPropertyType;
use application\metadata\MetadataPropertyValue;
use repository\RepositoryDataManager;
use repository\ContentObject;
use application\metadata\MetadataDataManager;
use repository\ComplexContentObjectItem;
use application\metadata\ContentObjectMetadataPropertyValue;
use repository\content_object\handbook\Handbook;
use application\context_linker\ContextLink;
use common\libraries\AndCondition;

/**
 * Exports content object to the chamilo learning object format (xml)
 * TODO: this is just a temporary class untill the cpo-export from the repository is refactored
 * code for metadata export should go to metadata, code for co-object export should go to repository/co
 * code for context link export should go to context linker
 */
class HandbookCpoImport extends ContentObjectImport
{
    const MODE_NEW = 'new';
    const MODE_EXTEND = 'extend';
    const MODE_FULL = 'full';

    const LINK_ORIGINAL = 'orig';
    const LINK_ALTERNATIVE ='alt';
    const LINK_METADATA = 'meta';

    private $option_strict = false;
    private $option_limited = false;
    private $mode = self::MODE_NEW;
    private $metadata_limitations = array();
    /**
     * @var RepositoryDataManager
     */
    private $rdm;
    /**
     * The imported xml file
     * @var DomDOCUMENT
     */
    private $doc;
    /**
     * Array of files that are created (hash + path)
     * @var Array
     */
    private $files;

    /*
     * Array of hp files that are created
     */
    private $hp_files;

    /*
     * Array of scorm files that are created
     */
    private $scorm_files;
    /**
     * The reference to store the file id's of each content_object and the new content_object
     * @var Array of INT
     */
    private $content_object_reference;
    /**
     * The array that has the reference for all the old ids of the wrappers => all the new ids of the wrappers
     *
     * @var Array of INT
     */
    private $wrapper_reference;
    /**
     * The array where the subitems are stored untill all the learning objects are created
     * With this array the wrappers will then be created
     *
     * Example:
     *
     * $lo_subitems['object0'] = array(0 => array(id => 'object1', properties => array()));
     */
    private $lo_subitems;
    /**
     * The array where the attachments are stored untill all the learning objects are created
     * With this array the attachment links will be created
     *
     * Example:
     *
     * $lo_attachments['object0'] = array(0 => 'object1', 1 => 'object2'));
     */
    private $lo_attachments;
    /**
     * The array where the includes are stored untill all the learning objects are created
     * With this array the include links will be created
     *
     * Example:
     *
     * $lo_includes['object0'] = array(0 => 'object1', 1 => 'object2'));
     */
    private $lo_includes;
    /**
     * Used to determine which references need to be changed (used in learning path item, portfolio item)
     *
     * @var $references[$content_object] = id;
     */
    private $references;
    /**
     * Used to collect all hotspot questions to adapt the image
     *
     * @var $hotspot_questions[$content_object] = id;
     */
    private $hotspot_questions;
    /**
     * In this array we store the learning path item wrappers because we need to change the prerequisites after all the wrappers have been
     * created
     *
     * @var $learning_path_item_wrappers[] = $wrapper_object;
     */
    private $learning_path_item_wrappers;
    /**
     * Used to determine the new category id
     * @var $categories[$old_category_id] = id
     */
    private $categories;
    /**
     * Used to save the references in the object numbers
     * @var int[]
     *
     * Example:
     * $object_numbers[60] = 1;
     */
    private $object_numbers = array();
    private $log = array();
    private $copies;
    private $linked_to_copy;
    private $not_in_scope;


    function __construct($content_object_file, $user, $category, $mode = self::MODE_NEW, $option_strict = false, $option_limited = false, $metadata_limitations = array())
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        parent :: __construct($content_object_file, $user, $category);
        $this->mode = $mode;
        $this->option_limited = $option_limited;
        $this->option_strict = $option_strict;
        $this->metadata_limitations = $metadata_limitations;
    }

    public function get_log()
    {
        return $this->log;
    }

    public function import_metadata()
    {
        $zip = Filecompression :: factory();
        $temp = $zip->extract_file($this->get_content_object_file_property('tmp_name'));
        $dir = $temp . '/';

        $path = $dir . 'content_object.xml';

        if (!file_exists($path))
        {
            if ($temp)
            {
                Filesystem :: remove($temp);
            }
            return false;
        }

        $this->import_files($dir);

        $doc = $this->doc;
        $doc = new DOMDocument();

        $doc->load($path);
        $metadata_schemas = $doc->getElementsByTagname('metadata_schema');
        $metadata_elements = $doc->getElementsByTagname('metadata_element');

        foreach ($metadata_schemas as $schema)
        {
            $this->create_metadata_schema($schema);
        }
        foreach ($metadata_elements as $element)
        {
            $this->create_metadata_element($element);
        }

        return true;
    }

    public function import_content_object()
    {
        $zip = Filecompression :: factory();
        $temp = $zip->extract_file($this->get_content_object_file_property('tmp_name'));
        $dir = $temp . '/';

        $path = $dir . 'content_object.xml';

        if (!file_exists($path))
        {
            if ($temp)
            {
                Filesystem :: remove($temp);
            }
            return false;
        }

        $this->import_files($dir);

        $doc = $this->doc;
        $doc = new DOMDocument();

        $doc->load($path);
        $content_objects = $doc->getElementsByTagname('content_object');

        $this->create_categories($doc->getElementsByTagname('category'));

        foreach ($content_objects as $lo)
        {
            $this->create_content_object($lo);
        }

        $this->create_complex_wrappers();
        $this->create_attachments();
        $this->create_includes();
        $this->update_references();
        $this->update_learning_path_prerequisites();
        $this->update_hotspot_questions();
        $this->create_context_links();


        if ($temp)
        {
            Filesystem :: remove($temp);
        }
        return true;
    }

    public function create_context_links()
    {
        foreach ($this->context_links as $context_link_data)
        {
            if (!in_array($context_link_data[self::LINK_ORIGINAL], $this->copies) && !in_array($context_link_data[self::LINK_ORIGINAL], $this->linked_to_copy))
            {
                //original was not a copy so add context-link
                $orig_id = $this->content_object_reference[$context_link_data[self::LINK_ORIGINAL]];
                $alt_id = $this->content_object_reference[$context_link_data[self::LINK_ALTERNATIVE]];
                if ($alt_id != null && $orig_id != null)
                {
                    //TODO: implement
                    $link = new ContextLink();
                    $link->set_original_content_object_id($orig_id);
                    $link->set_alternative_content_object_id($alt_id);

                    list($namespace_prefix, $element_name) = explode(':', $context_link_data[self::LINK_METADATA]);
                    $namespace = MetadataDataManager::get_instance()->retrieve_metadata_namespace_by_prefix($namespace_prefix);

                    if ($namespace)
                    {
                        $ns_id = $namespace->get_id();
                        $property = MetadataDataManager::get_instance()->retrieve_metadata_property_type_by_ns_name($ns_id, $element_name);
                        $property_id = $property->get_id();

                        $mdm = MetadataDataManager::get_instance();
                        $conditions[] = new EqualityCondition(ContentObjectMetadataPropertyValue::PROPERTY_PROPERTY_TYPE_ID, $property_id);
                        $conditions[] = new EqualityCondition(ContentObjectMetadataPropertyValue::PROPERTY_CONTENT_OBJECT_ID, $alt_id);
                        $condition = new AndCondition($conditions);
                        $value_set = $mdm->retrieve_content_object_metadata_property_values($condition = null);

                        if($value_set != null && $value_set != false)
                        {
                            $value = $value_set->next_result();
                            $metadata_property_value_id = $value->get_id();
                        }

                    }
                    $link->set_metadata_property_value_id($metadata_property_value_id);
                    $success = $link->create();
                    if($success)
                    {
                         $this->log[] = '<b><i> created the context link between ' . $context_link_data[self::LINK_ORIGINAL] . ' and ' . $context_link_data[self::LINK_ALTERNATIVE] . ' (on metadata = ' . $context_link_data[self::LINK_METADATA] . ')</i></b>';

                    }
                    else
                    {
                         $this->log[] = '<i> Problem! the context link between ' . $context_link_data[self::LINK_ORIGINAL] . ' and ' . $context_link_data[self::LINK_ALTERNATIVE] . ' (on metadata = ' . $context_link_data[self::LINK_METADATA] . ') could not be created</i>';

                    }
                }
                else
                {
                    $this->log[] = '<i> something went wrong on creating the context link between ' . $context_link_data[self::LINK_ORIGINAL] . ' and ' . $context_link_data[self::LINK_ALTERNATIVE] . ' here (on metadata = ' . $context_link_data[self::LINK_METADATA] . ')</i>';
                }
            }
            else
            {
                $this->log[] = '<i> contextlink for ' . $context_link_data[self::LINK_ORIGINAL] . ' and ' . $context_link_data[self::LINK_ALTERNATIVE] . ' (on metadata = ' . $context_link_data[self::LINK_METADATA] . ') not created since the original is a copy or linked to copy</i>';
                if ($this->mode == self::MODE_NEW)
                {
                    //do nothing
                }
                else if ($this->mode == self::MODE_EXTEND)
                {
                    //TODO: implement
                    $this->log[] = 'todo: check if this is a new context link & add';
                }
                else if ($this->mode == self::MODE_FULL)
                {
                    //TODO: IMPLEMENT
                    $this->log[] = 'todo: add new context links and replace data in existing co\'s with data from import';
                }
            }
        }
    }

    private function import_files($dir)
    {
        $user = $this->get_user();

        $lo_data_dir = $dir . 'data/';
        if (file_exists($lo_data_dir))
        {
            $files = Filesystem :: get_directory_content($lo_data_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $repdir = Path :: get(SYS_REPO_PATH);

            foreach ($files as $f)
            {
                $usr_path = $user->get_id() . '/' . Text :: char_at($f, 0);
                $full_path = $repdir . $usr_path;

                $hash = Filesystem :: create_unique_name($full_path, $f);

                Filesystem :: copy_file($dir . 'data/' . $f, $full_path . '/' . $hash, false);
                $this->files[$f] = array('hash' => $hash, 'path' => $usr_path . '/' . $hash);
            }
        }

        $hp_dir = $dir . 'hotpotatoes/';
        if (file_exists($hp_dir))
        {
            $files = Filesystem :: get_directory_content($hp_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $new_dir = Path :: get(SYS_HOTPOTATOES_PATH) . $user->get_id() . '/';

            foreach ($files as $f)
            {
                $dirname = Filesystem :: create_unique_name($new_dir, $f);
                Filesystem :: recurse_copy($dir . 'hotpotatoes/' . $f, $new_dir . $dirname, false);
                $this->hp_files[$f] = dirname;
            }
        }

        $scorm_dir = $dir . 'scorm/';
        if (file_exists($scorm_dir))
        {
            $files = Filesystem :: get_directory_content($scorm_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $new_dir = Path :: get(SYS_SCORM_PATH) . $user->get_id() . '/';

            foreach ($files as $f)
            {
                $dirname = Filesystem :: create_unique_name($new_dir, $f);
                Filesystem :: recurse_copy($dir . 'scorm/' . $f, $new_dir . $dirname, false);
                $this->scorm_files[$f] = dirname;
            }
        }
    }

    public function import_extra_properties($type, $additionalProperties, $lo)
    {
        if ($type == Document :: get_type_name())
        {
            $hash = $additionalProperties['hash'];

            $additionalProperties['hash'] = $this->files[$hash]['hash'];
            $additionalProperties['path'] = $this->files[$hash]['path'];
        }

        if ($type == Hotpotatoes :: get_type_name())
        {
            $path = $additionalProperties['path'];
            foreach ($this->hp_files as $folder => $new_folder)
            {
                if (strpos(dirname($path), $folder) !== false)
                {
                    $additionalProperties['path'] = str_replace($folder, $new_folder, $path);
                    break;
                }
            }
        }

        if ($type == LearningPath :: get_type_name())
        {
            $path = $additionalProperties['path'];
            foreach ($this->scorm_files as $folder => $new_folder)
            {
                if ($path == $folder)
                {
                    $additionalProperties['path'] = $new_folder;
                    break;
                }
            }
        }

        if ($type == ScormItem :: get_type_name())
        {
            $path = $additionalProperties['path'];
            foreach ($this->scorm_files as $folder => $new_folder)
            {
                if (strpos($path, $folder) !== false)
                {
                    $additionalProperties['path'] = str_replace($folder, $new_folder, $path);
                    break;
                }
            }
        }

        return $additionalProperties;
    }

    public function create_metadata_schema($schema)
    {
        foreach ($schema->attributes as $attrName => $attrNode)
        {
            if ($attrName == 'prefix')
            {
                $prefix = $attrNode->value;
            }
            elseif ($attrName == 'name')
            {
                $name = $attrNode->value;
            }
            elseif ($attrName == 'url')
            {
                $url = $attrNode->value;
            }
        }

        $namespace = MetadataDataManager::get_instance()->retrieve_metadata_namespace_by_prefix($prefix);

        if (!$namespace)
        {
            //namespace not in system -> create
            $ns = new MetadataNamespace();
            $ns->set_ns_prefix($prefix);
            $ns->set_name($name);
            $ns->set_url($url);
            $success = $ns->create();

            if ($success)
            {
                $this->log[] = 'schema ' . $prefix . '  created ';
            }
            else
            {
                $this->log[] = 'problem! schema ' . $prefix . ' could not be created ';
            }
        }
        else
        {
            $same = true;
            //namespace is in system -> check consistency
            $same &= $namespace->get_name() == $name;
            $same &= $namespace->get_url() == $url;

            if (!$same)
            {
                //TODO: problem-> catch and resolve!
                $this->log[] = 'schema already exists but inconsistency in information: ' . $namespace->get_name() . '/' . $name . '  -  ' . $namespace->get_url() . '/' . $url;
            }
            else
            {
                $this->log[] = 'schema already exists and is consistent: ' . $namespace->get_name();
            }
        }
    }

    public function create_metadata_element($element)
    {
        foreach ($element->attributes as $attrName => $attrNode)
        {
            if ($attrName == 'schema_prefix')
            {
                $namespace_prefix = $attrNode->value;
            }
            elseif ($attrName == 'element')
            {
                $element_name = $attrNode->value;
            }
        }
        $namespace = MetadataDataManager::get_instance()->retrieve_metadata_namespace_by_prefix($namespace_prefix);

        if ($namespace)
        {
            $ns_id = $namespace->get_id();

            $property = MetadataDataManager::get_instance()->retrieve_metadata_property_type_by_ns_name($ns_id, $element_name);
            if (!$property)
            {
                //property does not exist & needs to be created
                $property = new MetadataPropertyType();
                $property->set_namespace($ns_id);
                $property->set_name($element_name);
                $success = $property->create();
                if ($success)
                {
                    $this->log[] = '<b>property ' . $namespace_prefix . ':' . $element_name . ' created </b>';
                }
                else
                {
                    $this->log[] = 'problem! property ' . $namespace_prefix . ':' . $element_name . ' could not be created in namespace ' . $ns_id;
                }
            }
            else
            {
                $this->log[] = 'property ' . $namespace_prefix . ':' . $element_name . ' already exists in system ';
            }
        }
        else
        {
            //TODO: problem: namespace does not exist -> should have been imported & created: what went wrong?
            $this->log[] = 'import problem: namespace does not exist & was not created: ' . $namespace_prefix;
        }
    }

    public function check_uniqueness($content_object, $type, $id)
    {
        //handbook_item and handbook: check uuid to see if item already exists (if exists: don't import -> update
        $exists_already = false;
        if (($type == 'handbook') || ($type == 'handbook_item'))
        {
            $extended = $content_object->getElementsByTagName('extended')->item(0);
            $nodes = $extended->childNodes;
            foreach ($nodes as $node)
            {
                if ($node->nodeName == 'uuid')
                {
                    $uuid = convert_uudecode($node->nodeValue);
                }
                if ($node->nodeName == 'reference_id')
                {
                    $reference_id = convert_uudecode($node->nodeValue);
                }
            }
            if ($type == 'handbook_item')
            {
                $co = HandbookDataManager::get_instance()->retrieve_handbook_item_data_by_uuid($uuid);
            }
            if ($type == 'handbook')
            {
                $co = HandbookDataManager::get_instance()->retrieve_handbook_data_by_uuid($uuid);
            }
            if ($co != false)
            {
                $exists_already = true;
                $this->log[] = 'item with uid ' . $uuid . ' (' . $id . ')exists already';
                $this->copies[] = $id;
                $this->content_object_reference[$id] = $co[Handbook :: PROPERTY_ID];
                if ($reference_id)
                {
                    $this->log[] = 'item with id ' . $reference_id . ' added to list linked to copy (wrapper exists)';
                    $this->linked_to_copy[] = $reference_id;
                }
            }
        }

        return $exists_already;
    }

    public function render_metadata($content_object, $lo, $id)
    {
        //metadata
        $metadata = $content_object->getElementsByTagName('content_object_metadata')->item(0);
        if (is_object($metadata))
        {
            $children = $metadata->childNodes;
            for ($i = 0; $i < $children->length; $i++)
            {
                $co_metadata = $children->item($i);
                if ($co_metadata->nodeName == "#text")
                    continue;
                if ($co_metadata->hasAttributes())
                {
                    foreach ($co_metadata->attributes as $attrName => $attrNode)
                    {
                        if ($attrName == 'name')
                        {
                            $property = $attrNode->value;
                        }
                        elseif ($attrName == 'value')
                        {
                            $value = $attrNode->value;
                        }
                    }
                    list($namespace_prefix, $element_name) = explode(':', $property);
                    $namespace = MetadataDataManager::get_instance()->retrieve_metadata_namespace_by_prefix($namespace_prefix);
                    if ($namespace)
                    {
                        $ns_id = $namespace->get_id();
                        $property_type = MetadataDataManager::get_instance()->retrieve_metadata_property_type_by_ns_name($ns_id, $element_name);
                        if ($property_type)
                        {
                            $mpv = new ContentObjectMetadataPropertyValue();
                            $mpv->set_content_object_id($lo->get_id());
                            $mpv->set_property_type_id($property_type->get_id());
                            $mpv->set_value($value);
                            $success = $mpv->create();
                            if ($success)
                            {
                                $this->log[] = '<b> metadata ' . $property . '=' . $value . ' added to item with id ' . $lo->get_id() . '/' . $id . '</b>';
                            }
                            else
                            {
                                //problem: metadata_property_value not created
                                $this->log[] = 'Problem! metadata ' . $property . '=' . $value . ' was not added to item with id ' . $lo->get_id() . '/' . $id;
                            }
                        }
                        else
                        {
                            //problem: property was not imported
                            $this->log[] = 'Problem! metadata ' . $property . '=' . $value . ' was not added to item with id ' . $lo->get_id() . '/' . $id . ' because property does not exist in namespace with id ' . $namespace_id;
                        }
                    }
                    else
                    {
                        //problem: namespace was not imported
                        $this->log[] = 'Problem! metadata ' . $property . '=' . $value . ' was not added to item with id ' . $lo->get_id() . '/' . $id . ' because namespace ' . $namespace_prefix . ' does not exist';
                    }
                }
            }
        }
    }

    public function create_content_object($content_object)
    {
        $exists_already = false;
        $id = $content_object->getAttribute('id');
        if (isset($this->content_object_reference[$id]))
            return;

        if ($content_object->hasChildNodes())
        {
            $general = $content_object->getElementsByTagName('general')->item(0);
            $type = $general->getElementsByTagName('type')->item(0)->nodeValue;
            $title = $general->getElementsByTagName('title')->item(0)->nodeValue;
            $description = $general->getElementsByTagName('description')->item(0)->nodeValue;
            $comment = $general->getElementsByTagName('comment')->item(0)->nodeValue;
            $created = $general->getElementsByTagName('created')->item(0)->nodeValue;
            $modified = $general->getElementsByTagName('modified')->item(0)->nodeValue;
            if (is_object($general->getElementsByTagName('parent')->item(0)))
            {
                $category = $general->getElementsByTagName('parent')->item(0)->nodeValue;
            }
            else
            {
                $category = null;
            }
            $object_number = $general->getElementsByTagName('object_number')->item(0)->nodeValue;

            $exists_already = $this->check_uniqueness($content_object, $type, $id);

            //CREATE CONTENT OBJECT
            if (!$exists_already && !in_array($id, $this->linked_to_copy) && !in_array($id, $this->not_in_scope))
            {
                $this->create_object($content_object, $id, $type, $title, $description, $comment, $created, $modified, $object_number, $category);
            }
            else
            {
                $this->log[] = 'item with id ' . $id . ' copy or linked to copy or not in scope';
                if ($this->mode == self::MODE_NEW)
                {
                    //DO NOTHING
                }
                else if ($this->mode == self::MODE_EXTEND)
                {
                    //CREATE OBJECT IF IT IS LINKED TO COPY
                    if (in_array($id, $this->linked_to_copy) && !in_array($id, $this->not_in_scope))
                    {
                        //TODO: check if co not in system & create
                        $this->log[] = 'todo: item with id ' . $id . ' is linked to a copy. check if it is already in system';
                    }
                }
                else if ($this->mode == self::MODE_FULL)
                {
                    //CREATE OBJECT IF IT IS LINKED TO COPY OR UPDATE DATA OF COPY
                    if (!in_array($id, $this->not_in_scope))
                    {
                        //TODO: update or create
                        $this->log[] = 'todo: item with id ' . $id . ' will be updated or created';
                    }
                }
            }

            //ADD CONTEXT LINKS
            $this->render_context_links($content_object, $id);

            //ADD COMPLEX CHILDREN IF ANY
            $this->render_complex_children($content_object, $id);
        }
    }

    public function render_context_links($content_object, $id)
    {
        $links = $content_object->getElementsByTagName('linked_items')->item(0);
        if (is_object($links))
        {
            $children = $links->childNodes;
            for ($i = 0; $i < $children->length; $i++)
            {
                $context_link = $co_metadata = $children->item($i);
                if ($context_link->nodeName == "#text")
                    continue;
                if ($context_link->hasAttributes())
                {
                    foreach ($context_link->attributes as $attrName => $attrNode)
                    {
                        if ($attrName == 'idref')
                        {
                            $idref = $attrNode->value;
                        }
                        elseif ($attrName == 'metadata_link')
                        {
                            $metadata_link = $attrNode->value;
                        }
                    }
                    list($namespace_prefix, $element_name) = explode(':', $metadata_link);
                    if (!$exists_already && !in_array($id, $this->linked_to_copy) && !in_array($id, $this->not_in_scope))
                    {
                        //store original id - alternative id - metadata id
                        $this->context_links[] = array(self::LINK_ORIGINAL => $id, self::LINK_ALTERNATIVE => $idref, self::LINK_METADATA => $metadata_link);
                    }
                    else
                    {
                        //store original id - alternative id - metadata id
                        $this->context_links[] = array(self::LINK_ORIGINAL => $id, self::LINK_ALTERNATIVE => $idref, self::LINK_METADATA => $metadata_link);
                        $this->linked_to_copy[] = $idref;
                    }
                }
            }
        }
    }

    public function render_attachements($content_object, $id)
    {
        // Attachments
        $attachments = $content_object->getElementsByTagName('attachments')->item(0);
        if (is_object($attachments))
        {
            $children = $attachments->childNodes;
            if ($children)
            {
                for ($i = 0; $i < $children->length; $i++)
                {
                    $attachment = $children->item($i);
                    if ($attachment->nodeName == "#text")
                        continue;

                    $idref = $attachment->getAttribute('idref');
                    $type = $attachment->getAttribute('type');
                    $this->lo_attachments[$id][] = array('idref' => $idref, 'type' => $type);
                }
            }
        }
    }

    public function render_includes($content_object, $id)
    {
        // Includes
        $includes = $content_object->getElementsByTagName('includes')->item(0);
        if (is_object($includes))
        {
            $children = $includes->childNodes;

            if ($children)
            {
                for ($i = 0; $i < $children->length; $i++)
                {
                    $include = $children->item($i);
                    if ($include->nodeName == "#text")
                        continue;

                    $idref = $include->getAttribute('idref');
                    $this->lo_includes[$id][] = $idref;
                }
            }
        }
    }

    public function create_object($content_object, $id, $type, $title, $description, $comment, $created, $modified, $object_number, $category)
    {
        if ($this->option_strict || $this->option_limited)
        {
            //TODO: extra checks to see if content_object is in scope
            //first get metadata for co
            //if strict: check if dc:publisher & dc:language are set
            //if limited: check is metadata reqs are met
        }
        if (!in_array($id, $this->not_in_scope))
        {
            $this->log[] = '<b>item with title ' . $title . ' and id ' . $id . ' will be created</b>';
            $lo = ContentObject :: factory($type, array(ContentObject :: PROPERTY_STATE => ContentObject :: STATE_NORMAL));
            if (!$lo)
            {
                return null;
            }
            $lo->set_title($title);
            $lo->set_description($description);
            $lo->set_comment($comment);
            $lo->set_creation_date($created);
            $lo->set_modification_date($modified);
            $lo->set_owner_id($this->get_user()->get_id());
            $object_number_exists = array_key_exists($object_number, $this->object_numbers);
            if ($object_number_exists)
            {
                $lo->set_object_number($this->object_numbers[$object_number]);
            }
            if ($category == 'category0' || !$category)
            {
                $lo->set_parent_id($this->get_category());
            }
            else
            {
                $lo->set_parent_id($this->categories[$category]);
            }

            //extended
            $extended = $content_object->getElementsByTagName('extended')->item(0);
            if ($extended->hasChildNodes())
            {
                $nodes = $extended->childNodes;
                $additionalProperties = array();
                foreach ($nodes as $node)
                {
                    if ($node->nodeName == "#text" || $node->nodeName == 'id' || $node->nodeName == 'category')
                        continue;
                    $prop_names = $lo->get_additional_property_names();
                    if (in_array($node->nodeName, $prop_names))
                    {
                        $additionalProperties[$node->nodeName] = convert_uudecode($node->nodeValue);
                    }
                }
                $additionalProperties = $this->import_extra_properties($type, $additionalProperties, $lo);
                $lo->set_additional_properties($additionalProperties);
            }
            if ($type == Document :: get_type_name() && !$lo->get_path())
            {
                return;
            }
            if ($object_number_exists)
            {
                $lo->version();
            }
            else
            {
                $lo->create_all();
                $this->object_numbers[$object_number] = $lo->get_object_number();
            }
            if (in_array($type, RepositoryDataManager :: get_active_helper_types()))
            {
                $this->references[$lo->get_id()] = $additionalProperties['reference_id'];
            }
            if ($type == HotspotQuestion :: get_type_name())
            {
                $this->hotspot_questions[$lo->get_id()] = $lo->get_image();
            }
            $this->content_object_reference[$id] = $lo->get_id();

            $this->render_metadata($content_object, $lo, $id);
            $this->render_includes($content_object, $id);
            $this->render_attachements($content_object, $id);
        }
    }

    public function render_complex_children($content_object, $id)
    {
        // Complex children
        $subitems = $content_object->getElementsByTagName('sub_items')->item(0);
        if (is_object($subitems))
        {
            $children = $subitems->childNodes;
            for ($i = 0; $i < $children->length; $i++)
            {
                $subitem = $children->item($i);
                if ($subitem->nodeName == "#text")
                    continue;
                if ($subitem->hasAttributes())
                {
                    $properties = array();
                    foreach ($subitem->attributes as $attrName => $attrNode)
                    {
                        if ($attrName == 'idref')
                        {
                            $idref = $attrNode->value;
                        }
                        elseif ($attrName == 'id')
                        {
                            $my_id = $attrNode->value;
                        }
                        else
                        {
                            $properties[$attrName] = $attrNode->value;
                        }
                    }

                     if (!$exists_already && !in_array($id, $this->linked_to_copy))
                    {
                        $this->log[] = 'item with id ' . $idref . ' added to list of subitems';
                        $this->lo_subitems[$id][] = array('id' => $my_id, 'idref' => $idref, 'properties' => $properties);
                    }
                    else
                    {
                        $this->log[] = 'item with id ' . $idref . ' added to list linked to copy';
                        $this->linked_to_copy[] = $idref;
                        //TODO: update functionality?
                    }
                }
            }
        }
    }

    /**
     * Method to fix the embedded links in html editor fields
     *
     * @param ContentObject $co
     */
    private function fix_links($co)
    {
        if (count($co->get_included_content_objects()) == 0)
        {
            return;
        }

        $fields = $co->get_html_editors();

        //$pattern = '/http:\/\/.*\/files\/repository\/[1-9]*\/[^\"]*/';
        //$pattern = '/http:\/\/.*\/core\.php\?go=document_downloader&display=1&object=[0-9]*&application=repository/';
        $pattern = '/core\.php\?go=document_downloader&amp;display=1&amp;object=[0-9]*&amp;application=repository/';
        foreach ($fields as $field)
        {
            $value = $co->get_default_property($field);
            $value = preg_replace_callback($pattern, array($this, 'fix_link_matches'), $value);
            $co->set_default_property($field, $value);
        }

        $co->update();
    }

    private function fix_link_matches($matches)
    {
        //TODO: Use the correct link (downloader) - You will need to change the structue of the import (first import everything, then loop through all the objects)
        //        $base_path = Path :: get(WEB_REPO_PATH);
        //
        //        foreach ($this->files as $hash => $file)
        //        {
        //            if (strpos($matches[0], $hash) !== false)
        //            {
        //                return $base_path . $file['path'];
        //            }
        //        }


        $url = $matches[0];
        preg_match('/object=([0-9]*)/', $url, $matches);
        $object_id = $matches[1];

        return str_replace('object=' . $object_id, 'object=' . $this->content_object_reference['object' . $object_id], $url);
    }

    function create_complex_wrappers()
    {
        if (!$this->lo_subitems)
        {
            $this->log[] = 'no complex wrappers to be created';
            return;
        }

        foreach ($this->lo_subitems as $parent_id => $children)
        {
            $real_parent_id = $this->content_object_reference[$parent_id];

            if (!$real_parent_id)
                continue;

            if (!in_array($parent_id, $this->copies) && !in_array($parent_id, $this->linked_to_copy))
            {
                foreach ($children as $child)
                {
                    $real_child_id = $this->content_object_reference[$child['idref']];

                    if (!$real_child_id)
                        continue;

                    $childlo = $this->rdm->retrieve_content_object($real_child_id);

                    $cloi = ComplexContentObjectItem :: factory($childlo->get_type());

                    $cloi->set_ref($childlo->get_id());
                    $cloi->set_user_id($this->get_user()->get_id());
                    $cloi->set_parent($real_parent_id);
                    $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($real_parent_id));
                    $cloi->set_additional_properties($child['properties']);
                    $cloi->create();

                    $this->log[] = '<b>wrapper created to link ' . $parent_id . ' and ' . $child['idref'] . '</b>';

                    if ($childlo->get_type() == LearningPathItem :: get_type_name())
                    {
                        $this->learning_path_item_wrappers[] = $cloi;
                    }

                    $this->wrapper_reference[$child['id']] = $cloi->get_id();
                }
            }
            else
            {
                $this->log[] = 'wrapper to link ' . $parent_id . ' and ' . $child['idref'] . 'not created because the parent is a copy or linked to a copy';
            }
        }
    }

    function create_attachments()
    {
        if (!$this->lo_attachments)
        {
            return;
        }

        foreach ($this->lo_attachments as $lo_id => $children)
        {
            $real_lo_id = $this->content_object_reference[$lo_id];

            if (!$real_lo_id)
                continue;

            $lo = $this->rdm->retrieve_content_object($real_lo_id);

            foreach ($children as $child)
            {
                if ($this->content_object_reference[$child['idref']])
                    $lo->attach_content_object($this->content_object_reference[$child['idref']], $child['type']);
            }
        }
    }

    function create_includes()
    {
        if (!$this->lo_includes)
        {
            return;
        }

        foreach ($this->lo_includes as $lo_id => $children)
        {
            $real_lo_id = $this->content_object_reference[$lo_id];

            if (!$real_lo_id)
                continue;

            $lo = $this->rdm->retrieve_content_object($real_lo_id);

            foreach ($children as $child)
            {
                if ($this->content_object_reference[$child])
                    $lo->include_content_object($this->content_object_reference[$child]);
            }

            $this->fix_links($lo);
        }
    }

    function update_references()
    {
        if (!$this->references)
        {
            return;
        }

        foreach ($this->references as $lo_id => $reference)
        {
            $real_reference = $this->content_object_reference[$reference];

            if (!$real_reference)
                continue;

            $lo = $this->rdm->retrieve_content_object($lo_id);
            $lo->set_reference($real_reference);
            $lo->update();
            $this->log[] = '<b> change reference to ' . $reference . ' in real id ' . $real_reference . ' in content-object ' . $lo_id . '</b>';
        }
    }

    function update_learning_path_prerequisites()
    {
        if (!$this->learning_path_item_wrappers)
        {
            return;
        }

        foreach ($this->learning_path_item_wrappers as $lp_wrapper)
        {
            $ref = $this->rdm->retrieve_content_object($lp_wrapper->get_ref());
            $reference = $this->rdm->retrieve_content_object($ref->get_reference());
            if ($reference->get_type() != ScormItem :: get_type_name())
            {
                if ($prereq = $lp_wrapper->get_prerequisites())
                {
                    $pattern = '/[^()&|~]+/';
                    $prereq = preg_replace_callback($pattern, array($this, 'test_matches'), $prereq);
                    $lp_wrapper->set_prerequisites($prereq);
                    $lp_wrapper->update();
                }
            }
        }
    }

    function test_matches($matches)
    {
        return $this->wrapper_reference[$matches[0]];
    }

    function update_hotspot_questions()
    {
        if (!$this->hotspot_questions)
        {
            return;
        }

        foreach ($this->hotspot_questions as $question_id => $image_id)
        {
            $new_image_id = $this->content_object_reference[$image_id];

            if (!$new_image_id)
                continue;

            $co = $this->rdm->retrieve_content_object($question_id);
            $co->set_image($new_image_id);
            $co->update();
        }
    }

    function create_categories($categories)
    {
        foreach ($categories as $category)
        {
            if ($category->hasAttributes())
            {
                $id = $category->getAttribute('id');
                $name = $category->getAttribute('name');
                $parent = $category->getAttribute('parent');

                // Check if categories exist
                /* $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_NAME, $name);
                  $categories = RepositoryDataManager :: get_instance()->retrieve_categories($condition);
                  if($categories->size() > 0)
                  {
                  $this->categories[$id] = $categories->next_result()->get_id();
                  }
                  else */
                {
                    $category = new RepositoryCategory();
                    $category->set_name($name);

                    if ($parent == 'category0' || !$this->categories[$parent])
                    {
                        $category->set_parent($this->get_category());
                    }
                    else
                    {
                        $category->set_parent($this->categories[$parent]);
                    }

                    $category->set_user_id($this->get_user()->get_id());
                    $category->create();

                    $this->categories[$id] = $category->get_id();
                }
            }
        }
    }

}

?>