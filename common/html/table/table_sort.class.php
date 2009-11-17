<?php
/**
 * @package common.html.table
 */
// $Id: table_sort.class.php 128 2009-11-09 13:13:20Z vanpouckesven $ 

define('SORT_DATE', 3);
define('SORT_IMAGE', 4);
class TableSort
{

    /**
     * String to lowercase (keep accents).
     * @param string $txt The string to convert
     * @author Ren� Haentjens
     * This  function is 8859-1 specific and should be adapted when Chamilo is
     * used with other charsets. 
     */
    function strtolower_keepaccents($txt)
    {
        return strtolower(strtr($txt, "������������������������������", "������������������������������"));
    }

    /**
     * String to lowercase.
     * @param string $txt The string to convert
     * @author Ren� Haentjens
     * This  function is 8859-1 specific and should be adapted when Chamilo is
     * used with other charsets.
     */
    function strtolower_eorlatin($txt)
    {
        return str_replace(array("�", "�"), array("ae", "ss"), strtr(TableSort :: strtolower_keepaccents($txt), "�����������������������������", "aaaaaaceeeeiiiidnoooooouuuuyy"));
        // do not replace "�" by "th", leave it at the end of the alphabet...
    // do not replace any of "$&��������", though they resemble letters...
    }

    /**
     * Create a string to use in sorting.
     * @param string $txt The string to convert
     * @author Ren� Haentjens
     * This  function is 8859-1 specific and should be adapted when Chamilo is
     * used with other charsets. 
     * See http://anubis.dkuug.dk/CEN/TC304/EOR/eorhome.html 
     * Function 'orderingstring' can be used to implement EOR level 1 ordering,
     * for 8859- 1.
     */
    function orderingstring($txt)
    {
        return ereg_replace("[^0-9a-z�]", "", TableSort :: strtolower_eorlatin($txt));
    }

    /**
     * Sort 2-dimensional table.
     * @param array $data The data to be sorted.
     * @param int $column The column on which the data should be sorted (default =
     * 0)
     * @param string $direction The direction to sort (SORT_ASC (default) or
     * SORT_DESC)
     * @param constant $type How should data be sorted (SORT_REGULAR, SORT_NUMERIC,
     * SORT_STRING,SORT_DATE,SORT_IMAGE)
     * @return array The sorted dataset
     * @author digitaal-leren@hogent.be
     */
    function sort_table($data, $column = 0, $direction = SORT_ASC, $type = SORT_REGULAR)
    {
        if (! is_array($data) or count($data) == 0)
        {
            return array();
        }
        if ($column != strval(intval($column)))
        {
            return $data;
        } //probably an attack
        if (! in_array($direction, array(SORT_ASC, SORT_DESC)))
        {
            return $data;
        } // probably an attack
        $compare_function = '';
        
        switch ($type)
        {
            case SORT_REGULAR :
                if (TableSort :: is_image_column($data, $column))
                {
                    return TableSort :: sort_table($data, $column, $direction, SORT_IMAGE);
                }
                elseif (TableSort :: is_date_column($data, $column))
                {
                    return TableSort :: sort_table($data, $column, $direction, SORT_DATE);
                }
                elseif (TableSort :: is_numeric_column($data, $column))
                {
                    return TableSort :: sort_table($data, $column, $direction, SORT_NUMERIC);
                }
                
                return TableSort :: sort_table($data, $column, $direction, SORT_STRING);
            case SORT_NUMERIC :
                $compare_function = 'strip_tags($el1) > strip_tags($el2)';
                break;
            case SORT_IMAGE :
                $compare_function = 'strnatcmp(TableSort::orderingstring(strip_tags($el1,"<img>")),TableSort::orderingstring(strip_tags($el2,"<img>"))) > 0';
                break;
            case SORT_DATE :
                $compare_function = 'strtotime(strip_tags($el1)) > strtotime(strip_tags($el2))';
            case SORT_STRING :
            default :
                $compare_function = 'strnatcmp(TableSort::orderingstring(strip_tags($el1)),TableSort::orderingstring(strip_tags($el2))) > 0';
                break;
        }
        $function_body = '$el1 = $a[' . $column . ']; $el2 = $b[' . $column . ']; return (' . $direction . ' == SORT_ASC ? (' . $compare_function . ') : !(' . $compare_function . '));';
        // Sort the content
        usort($data, create_function('$a,$b', $function_body));
        return $data;
    }

    /**
     * Checks if a column of a 2D-array contains only numeric values
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only dates, false otherwise
     * @todo Take locale into account (eg decimal point or comma ?)
     * @author digitaal-leren@hogent.be
     */
    function is_numeric_column($data, $column)
    {
        $is_numeric = true;
        foreach ($data as $index => $row)
        {
            $is_numeric &= is_numeric(strip_tags($row[$column]));
        }
        return $is_numeric;
    }

    /**
     * Checks if a column of a 2D-array contains only dates (GNU date syntax)
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only dates, false otherwise
     * @author digitaal-leren@hogent.be
     */
    function is_date_column($data, $column)
    {
        $is_date = true;
        foreach ($data as $index => $row)
        {
            if (strlen(strip_tags($row[$column])) != 0)
            {
                $check_date = strtotime(strip_tags($row[$column]));
                // strtotime Returns a timestamp on success, FALSE otherwise. 
                // Previous to PHP 5.1.0, this function would return -1 on failure. 
                $is_date &= ($check_date != - 1 && $check_date != false);
            }
            else
            {
                $is_date &= false;
            }
        }
        return $is_date;
    }

    /**
     * Checks if a column of a 2D-array contains only images (<img src="
     * path/file.ext" alt=".."/>)
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only images, false otherwise
     * @author digitaal-leren@hogent.be
     */
    function is_image_column($data, $column)
    {
        $is_image = true;
        foreach ($data as $index => $row)
        {
            $is_image &= strlen(trim(strip_tags($row[$column], '<img>'))) > 0; // at least one img-tag
            $is_image &= strlen(trim(strip_tags($row[$column]))) == 0; // and no text outside attribute-values
        }
        return $is_image;
    }
}
?>