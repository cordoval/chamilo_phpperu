(function($)
{
    var default_size = 20, tableElement, tableBody;
    var timer;
    var questionPattern = /\[([^[\]]*)\](?:\{([^[}]*)\})?/g;
    var answersPattern = /(?:([^,\n\r(\\=)]+)(?:\(([^,\n\r]+)\))?(?:=([0-9]+))?,?)+?/g;

    function answerChanged(ev, ui)
    {
        var value = $(".answer").attr('value');
        var data = $("input[name=answer_data]");
        var rows = $(".data_table > tbody > tr");
        var css_class = 'row_even';

        var i = 0;
        var index = 0;

        tableElement.css('display', 'block');

        while (question = questionPattern.exec(value))
        {
            var hint = (typeof question[2] == 'undefined') ? '' : question[2];

            while (answer = answersPattern.exec(question[1]))
            {
                var feedback = (typeof answer[2] == 'undefined') ? ''
                        : answer[2];
                var score = isNaN(answer[3]) ? 1 : answer[3];

                if (index < rows.size())
                {
                    update_row(
                            rows.eq(index),
                            i + 1,
                            answer[1],
                            feedback,
                            hint,
                            score,
                            css_class);
                }
                else
                {
                    insert_row(
                            i + 1,
                            answer[1],
                            feedback,
                            hint,
                            score,
                            css_class);
                }

                index++;
            }

            css_class = css_class == 'row_even' ? 'row_odd' : 'row_even';

            i++;
        }

        while (rows.size() > index)
        {
            rows.eq(rows.size() - 1).remove();
            rows = $(".data_table > tbody > tr");
        }

        if (index == 0)
        {
            tableBody.empty();
            tableElement.css('display', 'none');
        }

        return true;
    }

    function update_row(row, index, answer, feedback, hint, score, css_class)
    {
        row.attr('class', css_class);
        cells = $('td', row);
        cells.eq(0).text(index);
        cells.eq(1).text(answer.replace('\n', '<br/>'));
        cells.eq(2).html(feedback.replace('\n', '<br/>'));
        cells.eq(3).html(hint.replace('\n', '<br/>'));
        cells.eq(4).text(score);
    }

    function insert_row(index, answer, feedback, hint, score, css_class)
    {
        var html = '';
        html += '<tr class="' + css_class + '">';
        html += '<td>' + index + '</td>';
        html += '<td>' + answer + '</td>';
        html += '<td>' + feedback + '</td>';
        html += '<td>' + hint + '</td>';
        html += '<td>' + score + '</td>';
        html += '</tr>';
        tableBody.append(html);
    }

    $(document).ready(function()
    {
        tableElement = $("#answers_table");
        tableBody = $("tbody", tableElement);
        $(".answer").keypress(function()
        {
            // Avoid searches being started after every character
            clearTimeout(timer);
            timer = setTimeout(answerChanged, 750);
        });

        $(".add_matches").toggle();
    });

})(jQuery);