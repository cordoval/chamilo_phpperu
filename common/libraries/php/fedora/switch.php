<?php

namespace common\libraries;

/**
 * @link http://www.switch.ch/
 */

/**
 * Metadata used by the SWITCH collections implementation of Fedora.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class SWITCH_object_meta {

    /**
     * The name or title of the resource
     *
     * @var string
     */
    public $title = '';
    /**
     * Owner of the resource. Usually this is the Author.
     *
     * @var array
     */
    public $creator = array();
    /**
     * Who may read the content of the resource (preview, download). Access terms: Private, group (GMT  group-id), institution (aai attribute home organization), public.
     * Future extension: differentiate between metadata and content access rights.
     *
     * @var string
     */
    public $accessRights = 'private';
    /**
     * Who may modify the resource. Access terms: Private, group, institution, public.
     * Special case: the origin of the resource is an external application (LMS, SWITCHcast). If the resource can only be edited in the external application, then rights are not evaluated in the repository. A user who edits the resource is redirected to the external application (edit link).
     *
     * @var string
     */
    public $rights = 'private';
    /**
     *
     *
     * @var string
     */
    public $license = '';
    /**
     * Teaching discipline of the resource: Controlled SWITCHlor vocabulary. Code as defined in http://collection.switch.ch/spec/2008/disciplines
     *
     * @var array
     */
    public $discipline = array();
    /**
     * AAI ID of the creator of the resource in the repository. This person may be the creator, the contributor or another person. Technical access rights are associated to this person.
     *
     * @var string
     */
    public $aaiid = '';
    /**
     * Who contributes to the resource. Being listed here does not imply technical access rights (they are specified in rights).
     *
     * @var array
     */
    public $contributor = array();
    /**
     * Who makes the resource available. Originating university or SWITCH
     *
     * @var array
     */
    public $publisher = array();
    /**
     * Person or organization owning or managing the rights over the resource
     *
     * @var string
     */
    public $rightsHolder = '';
    /**
     * Table of contents (unformatted free text)
     *
     * @var string
     */
    public $tableOfContents = '';
    /**
     * Abstract of the resource
     *
     * @var string
     */
    public $abstract = '';
    /**
     * Topic of the resource: keywords, key phrases, classification codes (free text)
     *
     * @var array
     */
    public $subject = array();
    /**
     * An account of the content of the resource
     *
     * @var array
     */
    public $description = array();
    /**
     * Language(s) of the content. Vocabulary: ISO-639-2 alpha-3 code
     *
     * @var array
     */
    public $language = array();
    /**
     * Description of education or training context (free text)
     *
     * @var array
     */
    public $educationLevel = array();
    /**
     * The way how instructional material is presented
     *
     * @var array
     */
    public $instructionalMethod = array();
    /**
     * Publication date of the resource. (Creation and modification dates are part of FOXML)
     *
     * @var string
     */
    public $issued = '';
    /**
     * Alternative title, e.g. title abbreviations or title translations
     *
     * @var array
     */
    public $alternative = array();
    /**
     * Nature or gerne of the resource
     * (DCMI Vocabulary: “Dataset”, “InteractiveResource”, “MovingImage”, “Software”, “Sound”, “StillImage”, “Text”)
     * DCMI Subset defined in http://collection.switch.ch/spec/2008/types/
     *
     * @var string
     */
    public $type = '';
    /**
     * The duration of the resource in seconds.
     *
     * @var array
     */
    public $extent = array();
    /**
     * If the resource is derived from another original resource: Unique id of the original resource. If the resource has been created by an automatic export/ingest process, the source should be used to determine if an existing resource has to be updated, or if a new resource should be created.
     *
     * For a harvested resource, this is the URL where it can be downloaded.
     *
     * @var string
     */
    public $source = '';
    /**
     * Persistent identifier of the resource. If a persistent identifier is set, the object can't be deleted anymore.
     *
     * @var string
     */
    public $identifier = '';
    /**
     * Collections containing the object
     *
     * @var array
     */
    public $collections = array();

    /**
     * Transform binary content and metadata to a FOXML formated file that can be ingested into Fedora.
     *
     * @param $content binary content
     * @param $meta standard metadata used by fedora
     * @param $switch additional SWITCH collections metadata
     */
    public static function content_to_foxml($content, fedora_object_meta $meta, SWITCH_object_meta $switch = null) {
        $meta->lastModifiedDate = $date = time();

        $w = new FoxmlWriter();
        $o = $w->add_digitalObject($meta->pid);
        $w = $o->add_objectProperties();
        $w->add_state('Active');
        $w->add_label($meta->label, false);
        $w->add_ownerId($meta->owner, false);
        //$w->add_createdDate($meta->createdDate);
        //$w->add_lastModifiedDate($meta->lastModifiedDate);
        // SWITCH CHOR_DC
        $w = $o->add_datastream('CHOR_DC', 'A', 'X', true);
        $w = $w->add_datastreamVersion('CHOR_DC1.0', 'SWITCH CHOR_DC record for this object', $date, 'text/xml', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
        $w = $w->add_switch_dc();
        $w->add_dc_title($meta->label);
        $w->add_dc_aaiid($switch->aaiid);
        $w->add_dc_creator($switch->creator);
        $w->add_dc_license(trim($switch->license));
        $w->add_dc_discipline($switch->discipline);
        $w->add_dc_accessRights($switch->accessRights);
        $w->add_dc_rights($switch->rights);
        $w->add_dc_rightsHolder($switch->rightsHolder);
        $w->add_dc_publisher($switch->publisher);
        $w->add_dc_description($switch->description);
        $w->add_dc_source($switch->source);

        //RELS-EXT
        $w = $o->add_datastream('RELS-EXT', 'A', 'X', false);
        $w = $w->add_datastreamVersion('RELS-EXT1.0', 'Relationships to other objects', $date, 'application/rdf+xml', 'info:fedora/fedora-system:FedoraRELSExt-1.0');
        $w = $w->add_rels_ext();
        $w = $w->add_rel_description("info:fedora/{$meta->pid}");
        $w->add_hasModel('info:fedora/fedora-system:FedoraObject-3.0');
        $collections = $switch->collections;
        $collections = is_array($collections) ? $collections : array($switch->collections);
        $collections = array_merge($meta->collections, $collections);
        foreach ($collections as $collection) {
            $w->add_rel_isMemberOfCollection($collection);
        }
        $w->add_oai_itemID($meta->pid);
        $w->add_rel_conformsTo('fedora-system:ContentModel-3.0');
        $w->add_dc_publisher($switch->publisher);

        //ensure chor_dc data is indexed by resource index
        $w->add_dc_license(trim($switch->license), trim($switch->license));
        //REL-EXTS requires tag value content. It will not index values provided in attribute.
        $w->add_dc_accessRights($switch->accessRights, $switch->accessRights);
        $w->add_dc_rights($switch->rights, $switch->rights);
        $w->add_dc_discipline($switch->discipline, $switch->discipline);

        $w->add_dc_creator($switch->creator);
        $w->add_dc_description($switch->description);

        //Object
        if ($content) {
            $w = $o->add_datastream('DS1', 'A', 'M', true);
            $w = $w->add_datastreamVersion('DS11.0', $meta->label, $meta->lastModifiedDate, $meta->mime);
            $w->add_binaryContent($content);
        }

        //Thumbnail
        if ($meta->thumbnail) {
            $w = $o->add_datastream('THUMBNAIL', 'A', 'M', true);
            $w = $w->add_datastreamVersion('OBJECT1.0', $meta->thumbnail_label, $meta->lastModifiedDate, $meta->thumbnail_mime);
            $w->add_binaryContent($meta->thumbnail);
        }

        $result = $o->saveXML();
        return $result;
    }

    /**
     * Returns the RELS-EXT datastream's content formated as an XML string. Used for updates.
     *
     *
     * @param fedora_object_meta $meta
     * @param SWITCH_object_meta $switch
     * @return string
     */
    public static function get_rels_ext(fedora_object_meta $meta, SWITCH_object_meta $switch = null) {
        //RELS-EXT
        $w = new FoxmlWriter();
        $w = $w->add_rels_ext(false);
        $w = $w->add_rel_description("info:fedora/{$meta->pid}");
        $w->add_hasModel('info:fedora/fedora-system:FedoraObject-3.0');
        $collections = $switch->collections;
        $collections = is_array($collections) ? $collections : array($switch->collections);
        $collections = array_merge($meta->collections, $collections);
        foreach ($collections as $collection) {
            $w->add_rel_isMemberOfCollection($collection);
        }
        $w->add_oai_itemID($meta->pid);

        //ensure chor_dc data is indexed by resource index
        $w->add_dc_license(trim($switch->license), trim($switch->license));

        //REL-EXTS requires tag value content. It will not index values provided in attribute.
        $w->add_dc_accessRights($switch->accessRights, $switch->accessRights);
        $w->add_dc_rights($switch->rights, $switch->rights);
        $w->add_dc_discipline($switch->discipline, $switch->discipline);

        $w->add_dc_creator($switch->creator);
        $w->add_dc_description($switch->description);
        $w->add_dc_publisher($switch->publisher);
        //$w->save("C:\\Users\\lopprecht\\Desktop\\test.xml");

        return $w->saveXML();
    }

    /**
     * Returns the CHOR_DC datastream's content formated as an XML string. Used for updates.
     *
     *
     * @param fedora_object_meta $meta
     * @param SWITCH_object_meta $switch
     * @return string
     */
    public static function get_chor_dc(fedora_object_meta $meta, SWITCH_object_meta $switch = null) {
        $w = new FoxmlWriter();
        $w = $w->add_switch_dc(false);
        $w->add_dc_title($switch->title);
        $w->add_dc_creator($switch->creator);
        $w->add_dc_aaiid($switch->aaiid);
        $w->add_dc_license(trim($switch->license));
        $w->add_dc_discipline($switch->discipline);
        $w->add_dc_accessRights($switch->accessRights);
        $w->add_dc_rightsHolder($switch->rightsHolder);
        $w->add_dc_rights($switch->rights);
        $w->add_dc_description($switch->description);
        $w->add_dc_source($switch->source);
        $w->add_dc_publisher($switch->publisher);
        return $w->saveXML();
    }

}

