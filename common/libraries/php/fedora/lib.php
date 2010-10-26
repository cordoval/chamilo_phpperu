<?php
namespace common\libraries;

/**
 * Main entry point to use the library.
 */

require_once dirname(__FILE__) . '/fedora_proxy.class.<?php
namespace common\libraries;';
require_once dirname(__FILE__) . '/writer/foxml_writer.class.<?php
namespace common\libraries;';
require_once dirname(__FILE__) . '/reader/foxml_reader.class.<?php
namespace common\libraries;';
require_once dirname(__FILE__) . '/switch.<?php
namespace common\libraries;';

require_once(dirname(__FILE__).'/../mime/mime_type.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fs/lib.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/util/util.<?php
namespace common\libraries;');


/**
 * Returns the Fedora owner ID.
 *
 * @return If the idnumber field is set for the current use returns its value. Otherwise returns the email address.
 */
/*
function get_fedora_owner_id(){
	global $USER;
	if(!empty($USER->idnumber)){
		return $USER->idnumber;
	}else{
		return $USER->email;
	}
}*/

/**
 * Standard metadata used by Fedora.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_object_meta{

	public $pid=0;
	public $mime = '';
	public $label = '';
	public $owner = '';
	public $createdDate; //auto-assigned by fedora
	public $lastModifiedDate; //auto-assigned by fedora
	public $is_collection = false;
	public $collections = array();
	public $thumbnail = '';
	public $thumbnail_label = '';
	public $thumbnail_mime = '';

	public function __construct(){
		$this->createdDate = $this->lastModifiedDate = time();
	}

}

function file_to_foxml($file_path, $owner, $pid=0){
	$label = basename($file_path);
	$ext = pathinfo($file_path, PATHINFO_EXTENSION);
	$mime = ext_to_mimetype($ext);
	$content = file_get_contents($file_path);
	return content_to_foxml($pid, $content, $mime, $label, $owner);
}

function fedora_content_to_foxml($content, fedora_object_meta $meta){
	$w = new FoxmlWriter();
	$o = $w->add_digitalObject($meta->pid);
	$w = $o->add_objectProperties();
	$w->add_property('info:fedora/fedora-system:def/model#state', 'Active');
	$w->add_property('info:fedora/fedora-system:def/model#label', $meta->label);
	$w->add_property('info:fedora/fedora-system:def/model#ownerId', $meta->owner);
	$date = time();
	$w->add_property('info:fedora/fedora-system:def/model#createdDate', $w->format_datetime($meta->createdDate));
	$w->add_property('info:fedora/fedora-system:def/view#lastModifiedDate',  $w->format_datetime($meta->lastModifiedDate));

	//Object
	if($content){
		$w = $o->add_datastream('OBJECT', 'A', 'M', true);
		$w = $w->add_datastreamVersion('OBJECT1.0', $meta->label, $meta->lastModifiedDate, $meta->mime);
		$w->add_binaryContent($content);
	}

	//Thumbnail
	if($meta->thumbnail){
		$w = $o->add_datastream('THUMBNAIL', 'A', 'M', true);
		$w = $w->add_datastreamVersion('OBJECT1.0', $meta->thumbnail_label, $meta->lastModifiedDate, $meta->thumbnail_mime);
		$w->add_binaryContent($meta->thumbnail);

	}

	//RELS-EXT
	$w = $o->add_datastream('RELS-EXT', 'A', 'X', false);
	$w = $w->add_datastreamVersion('RELS-EXT1.0', 'Relationships to other objects', $date, 'application/rdf+xml', 'info:fedora/fedora-system:FedoraRELSExt-1.0');
	$w = $w->add_rels_ext();
	$w = $w->add_rel_description("info:fedora/{$meta->pid}");
	$w->add_hasModel('info:fedora/fedora-system:FedoraObject-3.0');
	$w->add_hasModel('info:fedora/LORmodel:object');
	$w->add_hasModel('info:fedora/LORmodel:collection');
	$collections = $meta->collections;
	$collections = is_array($collections) ? $collections : array();
	foreach($collections as $collection){
		$w->add_rel_isMemberOfCollection($collection);
	}

	$w->add_oai_itemID($meta->pid);

	return $o->saveXML();

}








