<?php

abstract class SurveyAnalyzer
{
        
   	private $question;
    private $answers = array();
	
    public function SurveyAnalyzer($question, $answers)
    {
        $this->question = $question;
        $this->answers = $answers;
    }
  
    function get_answers()
    {
    	return $this->answers;
    }
    
    function get_question(){
    	return $this->question;
    }

    /**
     * Gets the supported analyse types for Analyzer
     * @return array Array containig all supported analyse types (keys and values
     * are the same)
     */
    public static function get_supported_analyse_types()
    {
        $directories = Filesystem :: get_directory_content(dirname(__FILE__), Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
            $type = basename($directory);
            if ($type != '.svn')
            {
                $types[$type] = $type;
            }
        }
        return $types;
    }

    public static function type_supported($type)
    {
        return in_array($type, self :: get_supported_filetypes());
    }

    /**
     * Factory function to create an instance of an analyzer class
     * @param string $type One of the supported analyse types returned by the
     * get_supported_analyse_types function.
     * @param string $answers The answers to analyse
     * (extension will be automatically added depending on the given $analyse type)
     * @param int $comlex_question_id question_id to analyse
     */
    public static function factory($analyse_type, $question, $answers)
    {
        
    	
    	$file = dirname(__FILE__) . '/' . $analyse_type . '/' . $analyse_type .'_analyzer.class.php';
    	$class = 'Survey'.Utilities :: underscores_to_camelcase($analyse_type) . 'Analyzer';
        
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($question, $answers);
        }
    }

    /**
     * @return ReportingData
     */
    abstract function analyse();
}
?>