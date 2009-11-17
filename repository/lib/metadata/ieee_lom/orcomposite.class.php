<?php
/**
 * $Id: orcomposite.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
/**
 * An OrComposite field used in IEEE LOM
 * 4. Technical > 4.4 Requirement > 4.4.1 >OrComposite
 */
class OrComposite
{
    /**
     * The type
     */
    private $type;
    /**
     * The name
     */
    private $name;
    /**
     * The minimum version
     */
    private $minimum_version;
    /**
     * The maximum version
     */
    private $maximum_version;

    /**
     * Constructor
     * @param IeeeLomVocabulary|null $type
     * @param Vocabylary|null $name
     * @param string|null $minimum_version
     * @param string|null $maximum_version
     */
    function OrComposite($type = null, $name = null, $minimum_version, $maximum_version)
    {
        $this->type = $type;
        $this->name = $name;
        $this->minimum_version = $minimum_version;
        $this->maximum_version = $maximum_version;
    }

    /**
     * Gets the type
     * @return IeeeLomVocabulary|null
     */
    function get_type()
    {
        return $this->type;
    }

    /**
     * Gets the name
     * @return IeeeLomVocabulary|null
     */
    function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the minumum version
     * @return string|null
     */
    function get_minimum_version()
    {
        return $this->minimum_version;
    }

    /**
     * Gets the maximum version
     * @return string|null
     */
    function get_maximum_version()
    {
        return $this->maximum_version;
    }
}
?>