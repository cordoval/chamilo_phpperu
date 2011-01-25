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
			'method' : 'package',
			'package_identifier' : id
		});

		result = eval('(' + result + ')');
		result.properties.package.version;
		var row = $("<tr>").attr('id',
				'dependency_' + result.properties.package.id);
		row.append($("<td>").html(result.properties.package.name));
		row.append($("<td>").html(result.properties.package.version));
		var ajaxUri = getPath('WEB_PATH') + 'ajax.php';

		// compare options
		var compare_options = doAjaxPost(ajaxUri, {
			'context' : 'application\\package',
			'method' : 'package_compare_options'
		});
		compareResult = eval('(' + compare_options + ')');
		var compareTd = $("<td>");
		var compareSelect = $("<select>").attr('name',
				'compare_' + result.properties.package.id);
		$.each(compareResult.properties.compare, function(index, value) {
			compareSelect.append($("<option>").val(index).html(value));
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
				'severity_' + result.properties.package.id);
		$.each(severityResult.properties.severity, function(index, value) {
			severitySelect.append($("<option>").val(index).html(value));
		});
		severityTd.append(severitySelect);
		row.append(severityTd);

		$("table#dependencies_table tbody").append(row);
	}

	$(document).ready(function() {
		activeBox = $('#elf_dependency_active');
		$("a:not(.disabled, .category)", activeBox).live("click", removeDependency);
		inactiveBox = $('#elf_dependency_inactive');
		$("a:not(.disabled, .category)", inactiveBox).live("click", addDependency);
	});
});