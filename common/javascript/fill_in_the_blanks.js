function utf8_decode ( str_data ) {
    // Converts a UTF-8 encoded string to ISO-8859-1  
    // 
    // version: 905.3122
    // discuss at: http://phpjs.org/functions/utf8_decode
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Norman "zEh" Fuchs
    // +   bugfixed by: hitwork
    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: utf8_decode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'
    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    
    str_data += '';
    
    while ( i < str_data.length ) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if ((c1 > 191) && (c1 < 224)) {
            c2 = str_data.charCodeAt(i+1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i+1);
            c3 = str_data.charCodeAt(i+2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }

    return tmp_arr.join('');
}

function utf8_encode ( argString ) {
    // Encodes an ISO-8859-1 string to UTF-8  
    // 
    // version: 905.1217
    // discuss at: http://phpjs.org/functions/utf8_encode
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'
    var string = (argString+'').replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if((c1 > 127) && (c1 < 2048)) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}

( function($) 
{
	var default_size = 20, tableElement, tableBody;
	var timer;
	
	function getNewPositions(value)
	{
		var response = $.ajax({
			type: "POST",
			dataType: "json",
			url: "./common/javascript/ajax/positions.php",
			data: { text: value },
			async: false
		}).responseText;
		
		return response;
	}
	
	function imatch(string, regexp, flags, doubleReturn)
	{
		if(typeof(string)!="string" || !regexp)
		{
			return null;
		};
		
		flags = (flags && typeof(flags) == "string") ? flags : "";
		
		var re = (typeof(regexp) == "string") ? new RegExp(regexp, flags) : regexp;
		var matches = string.match(re);
		
		if(!matches)
		{
			return null;
		
		}
		var found = 0;
		var indexes = new Array(matches.length);
		
		for(var m = 0; m < matches.length; m++)
		{
			found = string.substring(0, found).length;
			indexes[m] = found + string.substring(found).search(re);
			found = indexes[m] + matches[m].length;
		}
		
		return (!doubleReturn)? indexes: [indexes, matches];
		/*keep this comment to use freely
		http://www.fullposter.com/?1 */
	}
	
	function answerChanged(ev, ui) 
	{   
		var value = $(".answer").attr('value');
	    var pattern = /\[[\'\"a-zA-Z0-9_êëûüôöîïéèà\s\-]*\]/g;
	    var result = value.match(pattern);
	    var data = $("input[name=answer_data]");
	    
	    var utf_value = utf8_encode(value);
	    
	    //alert(imatch(utf_value, pattern, "", true));
	    //alert(data.val());
	    //var res = getNewPositions(base64_encode(value));
	    //alert(res);
	    
	    tableBody.empty();
    	 
	    if(result && result.length > 0)
	    {  
	    	tableElement.css('display', 'block');	    	
	     
		    for(var i = 0; i < result.length; i++)
		    {
		    	add_match_to_table(result[i], i);
		    }
	    }
	    else
	    {
	    	tableElement.css('display', 'none');	    	
	    }
	      
	    return true;
	} 
	
	function add_match_to_table(match, matchnumber)
	{
		var displayNumber = matchnumber + 1;
		
		var string =	'<tr><td>' + displayNumber + '</td>';
			string +=   '<td>' + match + '<input type="hidden" name="match[' + matchnumber + ']" value="' + match + '" /></td>';
			string +=  	'<td><div style="display: inline;">';
			
			parameters = { "width" : "100%", "height" : "65", "toolbar" : "RepositoryQuestion", "collapse_toolbar" : true };
			editorName = 'comment[' + matchnumber + ']';
			
			string += 	renderHtmlEditor(editorName, parameters);
			string +=	'</div></td>';
			string +=	'<td><input size="2" name="match_weight[' + matchnumber + ']" type="text" value="1" /></td>';
			string +=   '<td><input size="2" name="size[' + matchnumber + ']" type="text" value="' + default_size + '" /></td></tr>';
		
		tableBody.append(string);
		
	}

	$(document).ready( function() 
	{
	    tableElement = $("#answers_table");
	    tableBody = $("tbody", tableElement);
		$(".answer").keypress( function() {
			// Avoid searches being started after every character
			clearTimeout(timer);
			timer = setTimeout(answerChanged, 750);
		});
		
		$(".add_matches").toggle();
		
		//$(':checkbox').iphoneStyle({ checkedLabel: getTranslation('On'), uncheckedLabel: getTranslation('Off')});
	});
	
})(jQuery);