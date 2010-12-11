<?php
namespace common\libraries;

/**
 * This class represents a default Json response as provided
 * and used by the various AJAX calls throughout Chamilo
 *
 * @author Hans De Bisschop
 * @package common.libraries
 */
class JsonAjaxResult
{
    /**
     * Enter description here ...
     * @var array
     */
    private static $result_codes = Array(
            100 => 'Continue',
            101 => 'SwitchingProtocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'NonAuthoritativeInformation',
            204 => 'NoContent',
            205 => 'ResetContent',
            206 => 'PartialContent',
            207 => 'MultiStatus',
            300 => 'MultipleChoices',
            301 => 'MovedPermanently',
            302 => 'Found',
            303 => 'SeeOther',
            304 => 'NotModified',
            305 => 'UseProxy',
            306 => 'SwitchProxy',
            307 => 'TemporaryRedirect',
            400 => 'BadRequest',
            401 => 'Unauthorized',
            402 => 'PaymentRequired',
            403 => 'Forbidden',
            404 => 'NotFound',
            405 => 'MethodNotAllowed',
            406 => 'NotAcceptable',
            407 => 'ProxyAuthenticationRequired',
            408 => 'RequestTimeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'LengthRequired',
            412 => 'PreconditionFailed',
            413 => 'RequestEntityTooLarge',
            414 => 'RequestURITooLong',
            415 => 'UnsupportedMediaType',
            416 => 'RequestedRangeNotSatisfiable',
            417 => 'ExpectationFailed',
            422 => 'UnprocessableEntity',
            423 => 'Locked',
            424 => 'FailedDependency',
            425 => 'UnorderdCollection',
            426 => 'UpgradeRequired',
            449 => 'RetryWith',
            450 => 'BlockedByWindowsParentalControls',
            499 => 'ClientClosedRequest',
            500 => 'InternalServerError',
            501 => 'NotImplemented',
            502 => 'BadGateway',
            503 => 'ServiceUnavailable',
            504 => 'GatewayTimeout',
            505 => 'HttpVersionNotSupported',
            506 => 'VariantAlsoNegotiates',
            507 => 'InsufficientStorage',
            509 => 'BandwidthLimitExceeded',
            510 => 'NotExtended');

    /**
     * An HTTP status code
     * @var int
     */
    public $result_code;

    /**
     * A textual representation of the HTTP status code
     * @var string
     */
    public $result_message;

    /**
     * An array containing additional properties of the result
     *
     * @var array
     */
    public $properties = array();

    /**
     * JsonAjaxResult Constructor
     * @param int $result_code
     * @param array $properties
     */
    function __construct($result_code = 200, $properties = array())
    {
        $this->set_result_code($result_code);
        $this->set_properties($properties);
    }

    /**
     * Return the result message
     * @return string
     */
    function get_result_message()
    {
        return $this->result_message;
    }

    /**
     * Set the result message
     * @param string $result_message
     */
    function set_result_message($result_message)
    {
        $this->result_message = $result_message;
    }

    /**
     * Reset the result message to the default as defined by the result code
     */
    function reset_result_message()
    {
        $this->result_message = Translation :: get(self :: $result_codes[$this->get_result_code()]);
    }

    /**
     * Return the result code
     * @return int
     */
    function get_result_code()
    {
        return $this->result;
    }

    /**
     * Set the result code (and per extension the result message)
     * Data must be UTF-8 encoded
     * @param int $result_code
     */
    function set_result_code($result_code)
    {
        $this->result_code = $result_code;
        $this->result_message = Translation :: get(self :: $result_codes[$result_code]);
    }

    /**
     * Get a property
     * @param string $property
     * @return mixed:
     */
    function get_property($property)
    {
        return $this->properties[$property];
    }

    /**
     * Set a property
     * Data must be UTF-8 encoded
     * @param string $property
     * @param mixed $value
     */
    function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     * Get all properties
     * @return array:
     */
    function get_properties()
    {
        return $this->properties;
    }

    /**
     * Set all properties
     * @param array $properties
     */
    function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Return a Json representation of the current object
     * @return string
     */
    function encode()
    {
        return json_encode($this);
    }

    function display()
    {
        echo $this->encode();
        exit;
    }

    /**
     * Convert a JSON object string into a JsonAjaxResult
     * This function only works with UTF-8 encoded data
     *
     * @param string $json_string
     * @return JsonAjaxResult
     */
    static function decode($json_string)
    {
        $object = json_decode($json_string);
        return new self();
    }
}
?>