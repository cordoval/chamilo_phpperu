<?php

class Chamilo1TranslationImporter extends TranslationImporter
{
    /*
	function scan_for_language_translations($file)
    {
    	$lang = array();
    	$fh = fopen($file, 'r');

    	while (!feof($fh))
    	{
        	$line = fgets($fh);
			$line = trim($line);
			if(substr($line, 0, 1) == '$')
			{

				$pos = strpos($line, ' ');
				$variable = substr($line, 1, $pos - 1);

				$line = substr($line, $pos);
				$pos1 = strpos($line, '"');
				$translation = substr($line, $pos1 + 1, -2);

				$translations[$variable] = $translation;

			}
   	 	}

    	fclose($fh);

    	return $translations;
    }
    */
    function scan_for_language_translations($file)
    {
        $i = -1;
        $statements = array();
        $fh = fopen($file, 'r');
        while (!feof($fh))
        {
            $line = fgets($fh);
            if ($i == 0)
            {
                $line = ltrim(str_replace('<?php', '', $line));
                if (substr($line, 0, 1) == '$')
                {
                    $i++;
                    $statements[$i] = $line;
                }
            }
            else
            {
                if (substr($line, 0, 1) == '$')
                {
                    $statements[$i] = rtrim($statements[$i]);
                    $i++;
                    $statements[$i] = $line;
                }
                elseif ($i > -1)
                {
                    $statements[$i] .= $line;
                }

            }
        }
        fclose($fh);

        if ($i >= 0)
        {
            $statements[$i] = rtrim($statements[$i]);
            $statements[$i] = rtrim(preg_replace('/\?>$/', '', $statements[$i]));
        }

        $translations = array();
        foreach ($statements as & $statement)
        {
            if (preg_match('/^\$([a-zA-Z_][a-zA-Z0-9_]*)/', $statement, $matches))
            {
                $variable = $matches[1];
                $translation = eval($statement . ' return $'.$variable.';');
                if ($translation !== false)
                {
                    if (strlen(trim($translation)) > 0) // Skip empty "translations"
                    {
                        // For the moment let us not trim, translators may put leading or trailing whitespace intentionaly.

                        $translation = str_replace(array("\t", "\n", "\r"), array('\t', '\n', '\r'), $translation);
                        $translations[$variable] = $translation;
                    }
                }
            }
        }

        return $translations;
    }
}

?>