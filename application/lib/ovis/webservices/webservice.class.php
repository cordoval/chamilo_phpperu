<?php

/*
 * This file is part of Ovis.
 * 
 * Ovis is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * Ovis is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * Ovis. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The Web service.
 * 
 * @author Tim De Pauw
 * @package ovis
 */

require dirname(__FILE__) . '/facade.class.php';
require dirname(__FILE__) . '/exception.class.php';

$ws = new OvisWebService();
$ws->run();

class OvisWebService {
	private $uri;

	function __construct($uri = null) {
		$this->set_uri($uri);
	}

	function get_uri() {
		return $this->uri;
	}

	function set_uri($uri) {
		$this->uri = (is_null($uri) ? self::detect_uri() : $uri);
	}

	function run() {
		if (strtolower($_SERVER['QUERY_STRING']) == 'wsdl') {
			$this->print_wsdl();
		}
		else {
			$server = new SoapServer(null, array('uri' => $this->get_uri()));
			$facade = new OvisWebServiceFacade();
			$facade->service = $this;
			$server->setObject($facade);
			$server->handle();
		}
	}

	function registerUpload($username, $password, $filename, $segments) {
		//verify upload account
                $dm = OvisDataManager :: get_instance();

                //create clips
                foreach($segments as $segment)
                {
                    $title      = $segment->title;
                    $start_time = $segment->startTime;
                    $end_time   = $segment->endTime;

                    //TODO:Jens-->also implement ovisClip
                    $clip = new StreamingVideoClip();
                    $clip->set_title($title);

                    if($clip->create())
                    {
                        //create new transcoding record
                        $transcoding = new Transcoding();
                        $transcoding->set_clip_id($clip_id);
                        $transcoding->set_start_time($start_time);
                        $transcoding->set_source_file($source_file);
                        try
                        {
                            $transcoding->create();
                        }
                        catch (Exception $e)
                        {
                            throw new OvisWebServiceException($e->getMessage());
                        }
                    }
                    
                }
	}

	function version() {
		return 1;
	}

	protected function print_wsdl() {
		header('Content-Type: text/xml; charset=UTF-8');
		$url = htmlspecialchars($this->get_uri());
		echo <<<END
<?xml version="1.0" encoding="UTF-8"?><definitions
	xmlns="http://schemas.xmlsoap.org/wsdl/"
	xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	targetNamespace="http://ovis.pwnt.be/"
	xmlns:tns="http://ovis.pwnt.be/">
	
	<types>
		<schema xmlns="http://www.w3.org/2001/XMLSchema"
		targetNamespace="http://ovis.pwnt.be/"
		xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
		xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">
			<complexType name="Segment">
				<all>
					<element name="title" type="xsd:string"/>
					<element name="startTime" type="xsd:long" minOccurs="0"/>
					<element name="endTime" type="xsd:long" minOccurs="0"/>
				</all>
			</complexType>
 			<complexType name="SegmentArray">
				<complexContent>
					<restriction base="soapenc:Array">
						<attribute ref="soapenc:arrayType"
						wsdl:arrayType="tns:Segment[]"/>
					</restriction>
				</complexContent>
			</complexType>
		</schema>
	</types>

	<message name="VersionResponse">
		<part name="version" type="xsd:int"/>
	</message>

	<message name="UploadRegistrationRequest">
		<part name="username" type="xsd:string"/>
		<part name="password" type="xsd:string"/>
		<part name="filename" type="xsd:string"/>
		<part name="segments" type="tns:SegmentArray"/>
	</message>

	<message name="UploadRegistrationResponse">
		<part name="clipCount" type="xsd:int"/>
	</message>

	<portType name="PortType">
		<operation name="version">
			<output message="tns:VersionResponse"/>
		</operation>
		<operation name="registerUpload">
			<input message="tns:UploadRegistrationRequest"/>
			<output message="tns:UploadRegistrationResponse"/>
		</operation>
	</portType>

	<binding name="Binding" type="tns:PortType">
		<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
		<operation name="version">
			<soap:operation soapAction="version"/>
			<output>
				<soap:body
					encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
					namespace="http://ovis.pwnt.be/" use="literal"/>
			</output>
		</operation>
		<operation name="registerUpload">
			<soap:operation soapAction="registerUpload"/>
			<input>
				<soap:body
					encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
					namespace="http://ovis.pwnt.be/" use="literal"/>
			</input>
			<output>
				<soap:body
					encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
					namespace="http://ovis.pwnt.be/" use="literal"/>
			</output>
		</operation>
	</binding>

	<service name="Service">
		<port name="Port" binding="tns:Binding">
			<soap:address location="{$url}"/>
		</port>
	</service>
</definitions>
END;
	}

	protected static function detect_uri() {
		if (!empty($_SERVER['HTTPS'])) {
			$protocol = 'https';
			$default_port = 443;
		}
		else {
			$protocol = 'http';
			$default_port = 80;
		}
		$host = $_SERVER['HTTP_HOST'];
		if ($_SERVER['SERVER_PORT'] != $default_port) {
			$host .= ':' . $_SERVER['SERVER_PORT'];
		}
		$path = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
		return $protocol . '://' . $host . $path;
	}
}

?>