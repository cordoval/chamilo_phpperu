CKEDITOR.dialog.add('chamiloHandbookLink',
function(a) {
    page1 = {
        type: 'html'
    };
    page1.html = "<h1>Handboek ID</h1><input id='hid' value='id van handboek'><h1>Handboek-item ID</h1><input id='hsid' value='id van handboek'>";
    page2 = {
        type: 'html'
    };
    page2.html = "<h2>Tab 2</h2>";
    return {
        title: "Link naar Handbook-item",
        onOk: function() {
            var mySelection = this.getParentEditor().getSelection();

            if (CKEDITOR.env.ie) {
                mySelection.unlock(true);
                selectedText = mySelection.getNative().createRange().text;
            } else {
                selectedText = mySelection.getNative();
            }
				 this.getParentEditor().insertHtml('<a href="/CHAMILO_HG/run.php?go=handbook_viewer&amp;application=handbook&amp;hid='+document.getElementById('hid').value+'&amp;hsid='+document.getElementById('hsid').value+'" target="_blank">'+selectedText+'( hsid='+document.getElementById('hsid').value+', hid='+document.getElementById('hid').value+')</a>');
        },
        contents: [
        {
            id: 'Tab1',
            label: 'Tab 1',
            expand: true,
            elements: [page1]
        },
        {
            id: 'Tab2',
            label: 'Tab 2',
            elements: [page2]
        }
        ],
        buttons: [
        CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton
        ]
    };
});




