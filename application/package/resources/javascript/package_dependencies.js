$(function() {
	function removeDependency(e, ui) {
		var id = $(this).attr('id');
		$("tr#" + id).remove();
		if($("table#dependencies_table tbody tr").size() == 0){
			$("table#dependencies_table").parent().hide();
		}
		
	}
	function addDependency(e, ui) {
		$("table#dependencies_table").parent().show();
		var id = $(this).attr('id');
		var ajaxUri = getPath('WEB_PATH') + 'ajax.php';
		var result = doAjaxPost(ajaxUri, {
			'context' : 'application\\package',
			'method' : 'dependency',
			'dependency_identifier' : id
		});

		result = eval('(' + result + ')');
		result.properties.dependency.version;
		var row = $("<tr>").attr('id',
				'dependency_' + result.properties.dependency.id);
		row.append($("<td>").html(result.properties.dependency.name));
		row.append($("<td>").html(result.properties.dependency.version));
		
		var ajaxUri = getPath('WEB_PATH') + 'ajax.php';

		// compare options
		var compare_options = doAjaxPost(ajaxUri, {
			'context' : 'application\\package',
			'method' : 'package_compare_options'
		});
		compareResult = eval('(' + compare_options + ')');
		var compareTd = $("<td>");
		var compareSelect = $("<select>").attr('name',
				'compare_' + result.properties.dependency.id);
		$.each(compareResult.properties.compare, function(index, value) {
			var compareOption = $("<option>");
			compareSelect.append(compareOption.val(index).html(value));
			if(index == parseInt(result.properties.dependency.compare))
				compareOption.attr('selected', 'true');			
		});
		compareTd.append(compareSelect);                    
		row.append(compareTd);

		// severity options
		var severity_options = doAjaxPost(ajaxUri, {
			'context' : 'application\\package',
			'method' : 'package_severity_options'
		});
		severityResult = eval('(' + severity_options + ')');
		var severityTd = $("<td>");
		var severitySelect = $("<select>").attr('name',
				'severity_' + result.properties.dependency.id);
		$.each(severityResult.properties.severity, function(index, value) {
			var severityOption = $("<option>") 
			severitySelect.append(severityOption.val(index).html(value));
			if(index == parseInt(result.properties.dependency.severity))
				severityOption.attr('selected', 'true');	
		});
		severityTd.append(severitySelect);
		row.append(severityTd);
		
		// type options
		var type_options = doAjaxPost(ajaxUri, {
			'context' : 'application\\package',
			'method' : 'package_type_options'
		});
		typeResult = eval('(' + type_options + ')');
		var typeTd = $("<td>");
		var typeSelect = $("<select>").attr('name',
				'type_' + result.properties.dependency.id);
		$.each(typeResult.properties.type, function(index, value) {
			var typeOption = $("<option>") 
			typeSelect.append(typeOption.val(index).html(value));
			if(index == parseInt(result.properties.dependency.type))
				typeOption.attr('selected', 'true');	
		});
		typeTd.append(typeSelect);
		row.append(typeTd);
		
		$("table#dependencies_table tbody").append(row);
	}

	$(document).ready(function() {
		activeBox = $('#elf_dependency_active');
		$("a:not(.disabled, .category)", activeBox).live("click", removeDependency);
		inactiveBox = $('#elf_dependency_inactive');
		$("a:not(.disabled, .category)", inactiveBox).live("click", addDependency);
	});
});