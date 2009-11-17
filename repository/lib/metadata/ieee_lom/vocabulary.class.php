<?php
/**
 * $Id: vocabulary.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
/**
 * A Vocabulary field in IEEE LOM
 */
class IeeeLomVocabulary
{
    /**
     * The source
     */
    private $source;
    
    /**
     * The value
     */
    private $value;

    /**
     * Constructor
     * @param string|null $source
     * @param string|null $value
     */
    public function IeeeLomVocabulary($value = null, $source = IeeeLom :: VERSION)
    {
        $this->value = $value;
        $this->source = $source;
    }

    /**
     * Gets the source
     * @return string|null
     */
    public function get_source()
    {
        return $this->source;
    }

    /**
     * Gets the value
     * @return string|null
     */
    public function get_value()
    {
        return $this->value;
    }

}
?>
