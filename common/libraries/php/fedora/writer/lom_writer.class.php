<?php
//this file had been commented out by systho <systho@gmail.com> because it is a full
// duplicate of common/libraries/php/ims/common/writer/lom_writer.class.php
// and it causes a name clash.
// TODO refactor  common/libraries/php/ims/common/writer and common/libraries/php/fedora/writer
// because they are duplication


//namespace common\libraries;
//
//require_once  dirname(__FILE__) .'/xml_writer_base.class.php';
//
///**
// * Utility class used to generate LOM IEEE 1484.12.1 XML schemas.
// * Basic implementation.
// *
// * @copyright (c) 2010 University of Geneva
// * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
// * @author laurent.opprecht@unige.ch
// *
// */
//class LomWriter extends XmlWriterBase{
//
//    function __construct($writer=null, $prefix = ''){
//    	parent::__construct($writer, $prefix);
//    }
//
//    public function get_format_name(){
//    	return 'LOM';
//    }
//
//    public function get_format_version(){
//    	return 'IEEE 1484.12.1';
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_general(){
//    	$result = $this->add_element('general');
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_title($text, $lang = 'x-none'){
//    	$result = $this->add_element('title');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_description($text, $lang = 'x-none'){
//    	$result = $this->add_element('description');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_keyword($text, $lang = 'x-none'){
//    	$result = $this->add_element('keyword');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_coverage($text, $lang = 'x-none'){
//    	$result = $this->add_element('coverage');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_langstring($text, $lang = 'x-none'){
//    	$result = $this->add_element('langstring', $text);
//    	$result->set_attribute('xml:lang', $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_identifier($catalog, $entry, $lang = 'x-none'){
//    	$result = $this->add_element('identifier');
//    	$result->add_catalog($catalog, $lang);
//    	$result->add_entry($entry, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_catalog($text, $lang = 'x-none'){
//    	$result = $this->add_element('catalog');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_entry($text, $lang = 'x-none'){
//    	$result = $this->add_element('entry');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_lifecycle(){
//    	$result = $this->add_element('lifecycle');
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_version($text, $lang = 'x-none'){
//    	$result = $this->add_element('version');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_status($source = 'LOMv1.0', $value = 'final', $lang = 'x-none'){
//    	$result = $this->add_element('status');
//    	$result->add_source($source, $lang);
//    	$result->add_value($value, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_source($text, $lang = 'x-none'){
//    	$result = $this->add_element('source');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//    /**
//  	 * @return LomWriter
//     */
//    public function add_value($text, $lang = 'x-none'){
//    	$result = $this->add_element('value');
//    	$result->add_langstring($text, $lang);
//    	return $result;
//    }
//
//
//}
//?>