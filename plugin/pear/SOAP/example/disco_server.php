<?php
/**
 * This example shows how to mark up a server object so that WSDL files may be
 * dynamicaly generated by the SOAP server.  This also provides access to a
 * generated DISCO document.  The fact that this example has an MP3 class is
 * in no way related to DISCO. ;)
 *
 * DISCO: http://msdn.microsoft.com/msdnmag/issues/02/02/xml/default.aspx
 *
 * Urls accessing this server would look like:
 * - http://localhost/disco_server.php?wsdl (generate WSDL file)
 * - http://localhost/disco_server.php (GET request generates DISCO)
 * - http://localhost/disco_server.php (POST for normal SOAP requests)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is available at
 * through the world-wide-web at http://www.php.net/license/2_02.txt.  If you
 * did not receive a copy of the PHP license and are unable to obtain it
 * through the world-wide-web, please send a note to license@php.net so we can
 * mail you a copy immediately.
 *
 * @category   Web Services
 * @package    SOAP
 * @author     Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

error_reporting(E_ALL);

/** SOAP_Server */
require_once 'SOAP/Server.php';

/**
 * MP3 database class.
 *
 * @package SOAP
 */
class MP3DB_Class
{

    var $__dispatch_map = array();
    var $__typedef = array();

    function __construct()
    {
        /* The only way to describe all methods in WSDL (messages,
         * PortType-operations and bindings) is to use __dispatch_map to
         * describe every method (even methods using simple data types in 'in'
         * and 'out' parameters...) */
        $this->__dispatch_map['SayHallo'] =
            array('in' => array('input' => 'string'),
                  'out' => array('return' => 'string'));
        $this->__dispatch_map['SayThisNTimes'] =
            array('in' => array('SayThis'=>'string','NTimes' => 'int'),
                  'out' => array('return' => '{urn:MP3DB}ArrayOfStrings'));
        $this->__dispatch_map['GetMP3Tracks'] =
            array('in' => array('query' => 'string'),
                  'out' => array('return' => '{urn:MP3DB}GetMP3TracksResult'));
        $this->__dispatch_map['AddMP3Track'] =
            array('in' => array('MP3Track' => '{urn:MP3DB}MP3Track'),
                  'out' => array('return' => '{urn:MP3DB}AddMP3TrackResult'));

        /* Use __typedef to describe userdefined Types in WSDL.  Structs and
         * one-dimensional arrays are supported.
         *
         * Struct example:
         * $this->__typedef['TypeName'] = array('VarName' => 'xsdType', ... );
         * or
         * $this->__typedef['TypeName'] = array('VarName' => '{namespace}SomeOtherType');
         *
         * Array example:
         * $this->__typedef['TypeName'] = array(array('item' => 'xsdType'));
         * or
         * $this->__typedef['TypeName'] = array(array('item' => '{namespace}SomeOtherType'));
         */

        /* Struct 'MP3Track'. */
        $this->__typedef['MP3Track'] =
            array('Title' => 'string',
                  'Artist' => 'string',
                  'Album' => 'string',
                  'Year' => 'int',
                  'Genre' => 'int',
                  'Comment' => 'string',
                  'Composer' => 'string',
                  'Orig_Artist' => 'string',
                  'URL' => 'string',
                  'Encoded_by' => 'string');

        /* MP3TracksArray - array of 'MP3Track' structs. */
        $this->__typedef['MP3TracksArray'] =
            array(array('item' => '{urn:MP3DB}MP3Track'));

        /* Struct 'MethodDebug'. */
        $this->__typedef['MethodDebug'] =
            array('rc' => 'boolean',
                  'ErrNo' => 'int',
                  'Error' => 'string');

        /* Return Struct of method GetMP3Tracks. */
        $this->__typedef['GetMP3TracksResult'] =
            array('MethodDebug' => '{urn:MP3DB}MethodDebug',
                  'MP3Tracks' => '{urn:MP3DB}MP3TracksArray');

        /* Return Struct of method AddMP3Track. */
        $this->__typedef['AddMP3TrackResult'] =
            array('MethodDebug' => '{urn:MP3DB}MethodDebug');

        /* Array of strings. */
        $this->__typedef['ArrayOfStrings'] =
            array(array('item'=>'string'));
    }

    function SayHallo($name)
    {
        return 'Hello, ' . $name;
    }


    function SayThisNTimes($SayThis, $NTimes)
    {
        for ($i = 0; $i < $NTimes; $i++) {
            $return[$i] = $SayThis . ' ' . $i;
        }
        return new SOAP_Value('return', '{urn:MP3DB}ArrayOfStrings', $return);
    }

    function GetMP3Tracks($query = '')
    {
        for ($i = 0; $i < 5; $i++) {
            $this->MP3Tracks[$i] = new SOAP_Value(
                'item',
                '{urn:MP3DB}MP3Track',
                array('Title'       => new SOAP_Value('Title', 'string', 'some track $i'),
                      'Artist'      => new SOAP_Value('Artist', 'string', 'some artist $i'),
                      'Album'       => new SOAP_Value('Album', 'string', 'some album $i'),
                      'Year'        => new SOAP_Value('Year', 'int', 1999),
                      'Genre'       => new SOAP_Value('Genre', 'int', 100),
                      'Comment'     => new SOAP_Value('Comment', 'string', 'blabla $i'),
                      'Composer'    => new SOAP_Value('Composer', 'string', ''),
                      'Orig_Artist' => new SOAP_Value('Orig_Artist', 'string', ''),
                      'URL'         => new SOAP_Value('URL', 'string', ''),
                      'Encoded_by'  => new SOAP_Value('Encoded_by', 'string', '')));
        }

        $MethodDebug['rc']    = new SOAP_Value('rc', 'boolean', true);
        $MethodDebug['ErrNo'] = new SOAP_Value('ErrNo', 'int', 0);
        $MethodDebug['Error'] = new SOAP_Value('Error', 'string', '');

        return new SOAP_Value(
            'return',
            '{urn:MP3DB}GetMP3TracksResult',
            array('MethodDebug' => new SOAP_Value('MethodDebug', '{urn:MP3DB}MethodDebug',$MethodDebug),
                  'MP3Tracks'   => new SOAP_Value('MP3Tracks', '{urn:MP3DB}MP3TracksArray',$this->MP3Tracks)));
    }

    function AddMP3Track($MP3Track)
    {
        /* Well, let's imagine here some code for adding given mp3track to db
         * or whatever... */
        $MethodDebug['rc']    = new SOAP_Value('rc', 'boolean', true);
        $MethodDebug['ErrNo'] = new SOAP_Value('ErrNo', 'int', 0);
        $MethodDebug['Error'] = new SOAP_Value('Error', 'string', '');

        return new SOAP_Value(
            'return',
            '{urn:MP3DB}AddMP3TrackResult',
            array('MethodDebug' => new SOAP_Value('MethodDebug', '{urn:MP3DB}MethodDebug', $MethodDebug)));
    }

    function __dispatch($methodname)
    {
        if (isset($this->__dispatch_map[$methodname])) {
            return $this->__dispatch_map[$methodname];
        }
        return null;
    }

}

$server = new SOAP_Server();
$server->_auto_translation = true;
$MP3DB_Class = new MP3DB_Class();
$server->addObjectMap($MP3DB_Class, 'urn:MP3DB');

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else {
    require_once 'SOAP/Disco.php';
    $disco = new SOAP_DISCO_Server($server, 'MP3DB');
    header('Content-type: text/xml');
    if (isset($_SERVER['QUERY_STRING']) &&
        strpos($_SERVER['QUERY_STRING'], 'wsdl') !== false) {
        echo $disco->getWSDL();
    } else {
        echo $disco->getDISCO();
    }
}
