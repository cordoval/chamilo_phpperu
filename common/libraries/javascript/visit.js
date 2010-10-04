/**
 * @author Michael Kyndt
 * @author Hans De Bisschop
 */
(function($) {
	$(window).unload(function(e) {

		if (typeof tracker != 'undefined') {
			var response = $.ajax( {
				type : "POST",
				url : "./user/ajax/leave.php",
				data : {
					'tracker' : tracker
				}
			}).responseText;
		}
	});
})(jQuery);