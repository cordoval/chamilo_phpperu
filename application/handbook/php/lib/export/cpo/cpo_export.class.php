<?php
namespace application\handbook;

use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\ComplexContentObjectSupport;
use common\libraries\Filecompression;
use common\libraries\Filesystem;
use repository\ContentObjectExport;

use DOMDocument;

use repository\content_object\document\Document;
use repository\content_object\hotpotatoes\Hotpotatoes;
use repository\content_object\learning_path_item\LearningPathItem;
use repository\content_object\learning_path\LearningPath;
use repository\content_object\hotspot_question\HotspotQuestion;
use application\metadata\MetadataDataManager;
use repository\RepositoryDataManager;
use repository\ContentObject;
use repository\ComplexContentObjectItem;
use repository\ContentObjectAttachment;
use application\context_linker\ContextLinkerDataManager;
use application\context_linker\ContextLink;


/**
 * Exports Handbook & all related content-objects to the chamilo learning object format (xml)
 * TODO: this is just a temporary class untill the cpo-export from the repository is refactored
 */
class HandbookCpoExport extends ContentObjectExport
{
    /**
     * @var RepositoryDataManager
     */
    private $rdm;
    /**
     * @var DOMDocument
     */
    private $doc;

    /*
	 * Array of files to export
	 */
    private $files;

    /**
     * Array of hotpotatoes files to export
     */
    private $hotpot_files;

    /**
     * Array of scorm files to export
     */
    private $scorm_files;

    /*
	 * The <content_objects> tag in the xml file
	 */
    private $root;

    /**
     * The <categories> tag in the xml file
     */
    private $cat_root;

    /**
     * Array of already exported learning objects to prevent doubles
     */
    private $exported_content_objects;

    /**
     * Array of already exported categories to prevent doubles
     * @var Array
     */
    private $exported_categories;

    /**
     * Array of already exported namespaces
     * @var Array
     */
    private $exported_namespaces;

    /**
     * Array of already exported metadata-elements
     * @var Array
     */
    private $exported_metadata_elements;

    private $metadata;

    /**
     * Bool to determine wheter the categories should be exported
     * @var bool
     */
    private $export_categories;

    function __construct($content_object)
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        $this->exported_content_objects = array();
        parent :: __construct($content_object);
    }

    public function render_metadata_element($element_and_prefix)
    {
        list($namespace_prefix, $element_name) = \split(':', $element_and_prefix);

        if(!in_array($namespace_prefix, $this->exported_namespaces))
        {
            $this->exported_namespaces[] = $namespace_prefix;

            $doc = $this->doc;
            $root = $this->metadata;

            $schema = $doc->createElement('metadata_schema');
            $root->appendChild($schema);

            $namespace = MetadataDataManager::get_instance()->retrieve_metadata_namespace_by_prefix($namespace_prefix);

            $prefix = $doc->createAttribute('prefix');
            $schema->appendChild($prefix);
            $prefix_value = $doc->createTextNode($namespace_prefix);
            $prefix->appendChild($prefix_value);

            $name = $doc->createAttribute('name');
            $schema->appendChild($name);
            $name_value = $doc->createTextNode($namespace->get_name());
            $name->appendChild($name_value);

            $url = $doc->createAttribute('url');
            $schema->appendChild($url);
            $url_value = $doc->createTextNode($namespace->get_url());
            $url->appendChild($url_value);
        }

        if(!in_array($element_and_prefix, $this->exported_metadata_elements))
        {
            $this->exported_metadata_elements[] = $element_and_prefix;

            $doc = $this->doc;
            $root = $this->metadata;

            $elements = $doc->createElement('metadata_element');
            $root->appendChild($elements);

            $schema_prefix = $doc->createAttribute('schema_prefix');
            $elements->appendChild($schema_prefix);
            $schema_prefix_value = $doc->createTextNode($namespace_prefix);
            $schema_prefix->appendChild($schema_prefix_value);

            $element = $doc->createAttribute('element');
            $elements->appendChild($element);
            $element_value = $doc->createTextNode($element_name);
            $element->appendChild($element_value);
        }
    }

    public function export_content_object($export_categories = false)
    {
        $this->export_categories = $export_categories;

        $content_objects = $this->get_content_object();
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;

        $parent = $this->doc->createElement('export');
        $this->doc->appendChild($parent);

        $this->metadata = $this->doc->createElement('metadata_structure');
        $parent->appendChild($this->metadata);

        $this->root = $this->doc->createElement('content_objects');
        $parent->appendChild($this->root);

        if ($this->export_categories)
        {
            $this->cat_root = $this->doc->createElement('categories');
            $parent->appendChild($this->cat_root);
        }

        $user = null;

        foreach ($content_objects as $lo)
        {
            if (! $user)
                $user = $lo->get_owner_id();

            $this->render_content_object($lo);
        }

        $temp_dir = Path :: get(SYS_TEMP_PATH) . $user . '/export_content_objects/';

        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }

        $xml_path = $temp_dir . 'content_object.xml';
        $this->doc->save($xml_path);

        $this->add_files($temp_dir);

        $zip = Filecompression :: factory();
        //$zip->set_filename('content_objects_export');
        $zippath = $zip->create_archive($temp_dir);

        Filesystem :: remove($temp_dir);

        return $zippath;
    }

    function export_additional_properties($content_object)
    {
        if ($content_object->get_type() == Document :: get_type_name())
        {
            $this->files[$content_object->get_hash()] = $content_object->get_full_path();
        }

        if ($content_object->get_type() == Hotpotatoes :: get_type_name())
        {
            $this->hotpot_files[] = dirname($content_object->get_full_path());
        }

        if ($content_object->get_type() == LearningPath :: get_type_name() && $content_object->get_path())
        {
            $this->scorm_files[] = $content_object->get_full_path();
        }

        if (in_array($content_object->get_type(), RepositoryDataManager :: get_active_helper_types()))
        {
            $id = $content_object->get_reference();
            if ($id)
            {
                $this->render_content_object($this->rdm->retrieve_content_object($id));
                $content_object->set_reference('object' . $id);
            }
        }

        if ($content_object->get_type() == HotspotQuestion :: get_type_name())
        {
            $content_object->set_image('object' . $content_object->get_image());
        }
    }

    function add_files($temp_dir)
    {
        foreach ($this->files as $hash => $path)
        {
            $newfile = $temp_dir . 'data/' . $hash;
            Filesystem :: copy_file($path, $newfile, true);
        }
        foreach ($this->hotpot_files as $hotpot_dir)
        {
            $newfile = $temp_dir . 'hotpotatoes/' . basename(rtrim($hotpot_dir, '/'));
            Filesystem :: recurse_copy($hotpot_dir, $newfile, true);
        }

        foreach ($this->scorm_files as $scorm_dir)
        {
            $newfile = $temp_dir . 'scorm/' . basename(rtrim($scorm_dir, '/'));
            Filesystem :: recurse_copy($scorm_dir, $newfile, true);
        }
    }

    /**
     * Render the contentobject
     * @param ContentObject $content_object
     */
    function render_content_object($content_object, $is_version = false)
    {
        if (in_array($content_object->get_id(), $this->exported_content_objects))
        {
            return;
        }

        if ($content_object instanceof ContentObject)
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object->get_id(), $content_object->get_type());
        }

        //First we export the versions so the last version will always be imported last
        if ($content_object->is_latest_version())
        {
            $versions = $this->rdm->retrieve_content_object_versions($content_object, false);
            foreach ($versions as $version)
            {
                $this->render_content_object($version, true);
            }
        }
        else
        {
            if (! $is_version)
            {
                $this->render_content_object($content_object->get_latest_version());
                return;
            }
        }

        $this->exported_content_objects[] = $content_object->get_id();

        $doc = $this->doc;
        $root = $this->root;

        $lo = $doc->createElement('content_object');
        $root->appendChild($lo);

        $id = $doc->createAttribute('id');
        $lo->appendChild($id);

        $id_value = $doc->createTextNode('object' . $content_object->get_id());
        $id->appendChild($id_value);

        if ($content_object->is_latest_version())
        {
            $last_version = $doc->createAttribute('last_version');
            $lo->appendChild($last_version);

            $last_version_value = $doc->createTextNode('1');
            $last_version->appendChild($last_version_value);
        }

        $export_prop = array(ContentObject :: PROPERTY_TYPE, ContentObject :: PROPERTY_OBJECT_NUMBER, ContentObject :: PROPERTY_TITLE, ContentObject :: PROPERTY_DESCRIPTION, ContentObject :: PROPERTY_COMMENT, ContentObject :: PROPERTY_CREATION_DATE, ContentObject :: PROPERTY_MODIFICATION_DATE);

        $general = $doc->createElement('general');
        $lo->appendChild($general);

        foreach ($export_prop as $prop)
        {
            $property = $doc->createElement($prop);
            $general->appendChild($property);

            $text = $doc->createTextNode($content_object->get_default_property($prop));
            $text = $property->appendChild($text);
        }

        if ($this->export_categories)
        {
            $parent = $content_object->get_parent_id();
            if (! in_array($parent, $this->exported_categories) && $parent != 0)
            {
                $category = RepositoryDataManager :: get_instance()->retrieve_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $parent))->next_result();
                if (! $category)
                {
                    $parent = 0;
                }
                else
                {
                    $this->export_category($category);
                }
            }

            $parent_property = $doc->createElement('parent');
            $general->appendChild($parent_property);

            $cat_id = $doc->createTextNode('category' . $parent);
            $parent_property->appendChild($cat_id);
        }

        $this->export_additional_properties($content_object);

        $extended = $doc->createElement('extended');
        $lo->appendChild($extended);

        foreach ($content_object->get_additional_properties() as $prop => $value)
        {
            $property = $doc->createElement($prop);
            $extended->appendChild($property);
            $value = convert_uuencode($value);
            $text = $doc->createTextNode($value);
            $text = $property->appendChild($text);
        }

        //EXTRA: METADATA
        $metadata = $doc->createElement('content_object_metadata');
        $lo->appendChild($metadata);

        $metadata_values = MetadataDataManager::get_instance()->retrieve_content_object_metadata_property_values_as_array($content_object->get_id());

        foreach ($metadata_values as $element => $value)
        {
            $elements = $doc->createElement('metadata_property');
            $metadata->appendChild($elements);
            
             $name_id = $doc->createAttribute('name');
            $elements->appendChild($name_id);
            $name = $doc->createTextNode($element);
            $name_id->appendChild($name);

            $value_id = $doc->createAttribute('value');
            $elements->appendChild($value_id);
            $value = $doc->createTextNode($value);
            $value_id->appendChild($value);

            $this->render_metadata_element($element);
        }
        //TODO: include metadata namespace & element information





        //EXTRA: LINKED CONTENT OBJECTS & CONTEXT LINKS
        $cdm = ContextLinkerDataManager::get_instance();
        $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID , $content_object->get_id());
        $links = $cdm->retrieve_full_context_links($condition);
        if(count($links)>0)
        {

                $linked_items = $doc->createElement('linked_items');
                $lo->appendChild($linked_items);

        }
                  foreach ($links as $link)
            {
                $linked_item = $doc->createElement('linked_item');
                $linked_items->appendChild($linked_item);

                $id_ref = $doc->createAttribute('idref');
                $linked_item->appendChild($id_ref);

                $id_ref_value = $doc->createTextNode('object' . $link['alt_id']);
                $id_ref->appendChild($id_ref_value);

                $metadata_link= $doc->createAttribute('metadata_link');
                $linked_item->appendChild($metadata_link);
                
                $metadata_link_value = $doc->createTextNode($link['ns_prefix'].':'.$link['name']);
                $metadata_link->appendChild($metadata_link_value);

                $this->render_content_object($this->rdm->retrieve_content_object($link['alt_id']));
            }




        //EXTRA: CHILDREN

        $type = $doc->createAttribute('type');
        $lo->appendChild($type);

        //Complex children (subitems)
        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $text = $doc->createTextNode('complex');
            $type->appendChild($text);

            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $content_object->get_id(), ComplexContentObjectItem :: get_table_name());
            $children = $this->rdm->retrieve_complex_content_object_items($condition);

            if ($children->size() > 0)
            {
                $sub_items = $doc->createElement('sub_items');
                $lo->appendChild($sub_items);
            }

            while ($child = $children->next_result())
            {
                $sub_item = $doc->createElement('sub_item');
                $sub_items->appendChild($sub_item);

                $id_ref = $doc->createAttribute('idref');
                $sub_item->appendChild($id_ref);

                $id_ref_value = $doc->createTextNode('object' . $child->get_ref());
                $id_ref->appendChild($id_ref_value);

                $id = $doc->createAttribute('id');
                $sub_item->appendChild($id);

                $id_value = $doc->createTextNode($child->get_id());
                $id->appendChild($id_value);

                foreach ($child->get_additional_properties() as $prop => $value)
                {
                    $property = $doc->createAttribute($prop);
                    $sub_item->appendChild($property);

                    $text = $doc->createTextNode($value);
                    $text = $property->appendChild($text);
                }

                $this->render_content_object($this->rdm->retrieve_content_object($child->get_ref()));
            }
        }
        else
        {
            $text = $doc->createTextNode('simple');
            $type->appendChild($text);
        }

        //Attachments
        $condition = new EqualityCondition(ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID, $content_object->get_id());
        $content_object_attachments = RepositoryDataManager :: get_instance()->retrieve_content_object_attachments($condition);
        $attachments = $content_object->get_attached_content_objects();
        if ($content_object_attachments->size() > 0)
        {
            $attachments_element = $doc->createElement('attachments');
            $lo->appendChild($attachments_element);

            while ($content_object_attachment = $content_object_attachments->next_result())
            {
                $attachment = $content_object_attachment->get_attachment_object();

                $attachment_element = $doc->createElement('attachment');
                $attachments_element->appendChild($attachment_element);

                $type = $doc->createAttribute('type');
                $attachment_element->appendChild($type);

                $type_value = $doc->createTextNode($content_object_attachment->get_type());
                $type->appendChild($type_value);

                $id_ref = $doc->createAttribute('idref');
                $attachment_element->appendChild($id_ref);

                $id_ref_value = $doc->createTextNode('object' . $attachment->get_id());
                $id_ref->appendChild($id_ref_value);

                $this->render_content_object($attachment);
            }
        }

        //Includes
        $includes = $content_object->get_included_content_objects();
        if (count($includes) > 0)
        {
            $includes_element = $doc->createElement('includes');
            $lo->appendChild($includes_element);

            foreach ($includes as $include)
            {
                $include_element = $doc->createElement('include');
                $includes_element->appendChild($include_element);

                $id_ref = $doc->createAttribute('idref');
                $include_element->appendChild($id_ref);

                $id_ref_value = $doc->createTextNode('object' . $include->get_id());
                $id_ref->appendChild($id_ref_value);

                $this->render_content_object($include);
            }
        }
    }

    function export_category($category)
    {
        $cat = $this->doc->createElement('category');

        $id = $this->doc->createAttribute('id');
        $cat->appendChild($id);
        $id_value = $this->doc->createTextNode('category' . $category->get_id());
        $id->appendChild($id_value);

        $name = $this->doc->createAttribute('name');
        $cat->appendChild($name);
        $name_value = $this->doc->createTextNode($category->get_name());
        $name->appendChild($name_value);

        if (! in_array($category->get_parent(), $this->exported_categories) && $category->get_parent() != 0)
            $this->export_category(RepositoryDataManager :: get_instance()->retrieve_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $category->get_parent()))->next_result());

        $parent = $this->doc->createAttribute('parent');
        $cat->appendChild($parent);
        $parent_value = $this->doc->createTextNode('category' . $category->get_parent());
        $parent->appendChild($parent_value);

        $this->cat_root->appendChild($cat);

        $this->exported_categories[] = $category->get_id();
    }
}
?>