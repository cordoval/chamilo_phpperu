( function($) 
{
	var default_size = 20, tableElement, tableBody;
	var timer;
    var pattern = /\[[^\[\]]*\]/g;
	
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
	
	function answerChanged(ev, ui){   
		var value = $(".answer").attr('value');
	    var gaps = value.match(pattern);
	    var data = $("input[name=answer_data]");
	    
		if(gaps && gaps.length > 0){  
			tableElement.css('display', 'block');	   
	    	rows = $(".data_table > tbody > tr"); 
	    	index = 0;
	    	css_class = 'row_even';
	    	for(var i = 0; i<gaps.length; i++){
	    		var gap = gaps[i];
	    		answers = gap.replace('[', '').replace(']', '');
			    answers = answers.split(',');
		    	for(var j = 0; j<answers.length; j++){
		    		answer = answers[j];
			    	parts = answer.split('=');
			    	score = parts.length>1 ? parts[1] : 1;
			    	score = isNaN(score) ? 1 : score;
			    	parts = parts[0].replace('(', '=').replace(')', '=').split('=');
			    	feedback = parts.length>1 ? parts[1] : '';
			    	answer = parts[0];
			    	if(index<rows.size()){
			    		update_row(rows.eq(index), i+1, answer, feedback, score, css_class);
			    	}else{
			    		insert_row(i+1, answer, feedback, score, css_class);
			    	}
			    	index++;
			    }
		    	css_class = css_class == 'row_even' ? 'row_odd' : 'row_even';
		    }   
	    	while(rows.size() > index){
	    		rows.eq(rows.size()-1).remove();	   
		    	rows = $(".data_table > tbody > tr"); 
	    	}

		}else{
		    tableBody.empty();
	    	tableElement.css('display', 'none');
		}
	      
	    return true;
	} 
	
	function update_row(row, index, answer, feedback, score, css_class){
		row.attr('class', css_class);
		cells = $('td', row);
		cells.eq(0).text(index);
		cells.eq(1).text(answer);
		cells.eq(2).text(feedback);
		cells.eq(3).text(score);
	}
	
	function insert_row(index, answer, feedback, score, css_class){
		var html = '';
		html += '<tr class="'+ css_class +'">';
		html += '<td>' + index + '</td>';
		html += '<td>' + answer + '</td>';
		html += '<td>' + feedback + '</td>';
		html += '<td>' + score + '</td>';
		html += '</tr>';
	    tableBody.append(html);
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
	});
	
})(jQuery);