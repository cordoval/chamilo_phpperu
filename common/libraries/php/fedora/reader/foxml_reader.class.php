<?php
namespace common\libraries;

require_once  dirname(__FILE__) . '/xml_reader.class.php';
require_once  dirname(__FILE__) . '/xml_reader_empty.class.php';

/**
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FoxmlReader extends FedoraXmlReader
{
    protected function create_xpath(){
    	$result = parent::create_xpath();
    	$result->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    	$result->registerNamespace('dcterms', 'http://purl.org/dc/terms/');
    	$result->registerNamespace('chor_dcterms', 'http://purl.org/switch/terms/');
    	$result->registerNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
        return $result;
    }

	/**
	 * @return FoxmlReader
	 */
	public function get_objectProperties(){
		return $this->get('objectProperties');
	}

	/**
	 * @return string
	 */
    public function get_state(){
        $path = './def:property[@NAME="info:fedora/fedora-system:def/model#state"]';
        $item = $this->first($path);
        return $item->VALUE;
    }

	/**
	 * @return string
	 */
    public function get_label(){
        $path = './def:property[@NAME="info:fedora/fedora-system:def/model#label"]';
        $item = $this->first($path);
        return $item->VALUE;
    }

	/**
	 * @return string
	 */
    public function get_ownerId(){
        $path = './def:property[@NAME="info:fedora/fedora-system:def/model#ownerId"]';
        $item = $this->first($path);
        return $item->VALUE;
    }

	/**
	 * @return int
	 */
    public function get_createdDate(){
        $path = './def:property[@NAME="info:fedora/fedora-system:def/model#createdDate"]';
        $item = $this->first($path);
        return self::parse_date($item->VALUE);
    }

	/**
	 * @return int
	 */
    public function get_lastModifiedDate(){
        $path = './def:property[@NAME="info:fedora/fedora-system:def/view#lastModifiedDate"]';
        $item = $this->first($path);
        return self::parse_date($item->VALUE);
    }



























}