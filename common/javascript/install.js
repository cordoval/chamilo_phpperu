/*global $, document, jQuery, window */

$(function () {

	function selectall_clicked(evt, ui)
	{
		$('.application_check').attr('checked', true);
		$('.handle').css('left', '36px');
		$('.bg').css('left', '34px');
		$('.on').css('opacity', '1');
		$('.off').css('opacity', '0');
		return false;
	}
	
	function unselectall_clicked(evt, ui)
	{
		$('.application_check').attr('checked', false);
		$('.handle').css('left', '0px');
		$('.bg').css('left', '0px');
		$('.on').css('opacity', '0');
		$('.off').css('opacity', '1');
		return false;
	}
	
	$(document).ready(function ()
	{
		$("#tabs ul").css('display', 'block');
		$("#tabs h2").hide();
		$("#tabs").tabs();
		$('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
        $(':checkbox').iphoneStyle({ checkedLabel: 'On', uncheckedLabel: 'Off'});
        $('#selectbuttons').show();
        $('#selectall').live('click', selectall_clicked);
        $('#unselectall').live('click', unselectall_clicked);
        //Tim brouckaert 2010 03 11: added for refresh button
        jQuery('#refreshBtn').click(function(){
        	window.location.href=window.location.href; 
        	return false;
        });
	});

});

/*global $, document, jQuery, window */

2	

3	

$(function () {

4	

5	

        function selectall_clicked(evt, ui)

6	

        {

7	

                $('.application_check').attr('checked', true);

8	

                $('.handle').css('left', '36px');

9	

                $('.bg').css('left', '34px');

10	

                $('.on').css('opacity', '1');

11	

                $('.off').css('opacity', '0');

12	

                return false;

13	

        }

14	

        

15	

        function unselectall_clicked(evt, ui)

16	

        {

17	

                $('.application_check').attr('checked', false);

18	

                $('.handle').css('left', '0px');

19	

                $('.bg').css('left', '0px');

20	

                $('.on').css('opacity', '0');

21	

                $('.off').css('opacity', '1');

22	

                return false;

23	

        }

24	

        

25	

        $(document).ready(function ()

26	

        {

27	

                $("#tabs ul").css('display', 'block');

28	

                $("#tabs h2").hide();

29	

                $("#tabs").tabs();

30	

                $('#tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );

31	

        $(':checkbox').iphoneStyle({ checkedLabel: 'On', uncheckedLabel: 'Off'});

32	

        $('#selectbuttons').show();

33	

        $('#selectall').live('click', selectall_clicked);

34	

        $('#unselectall').live('click', unselectall_clicked);

35	


40	

        });

41	

});