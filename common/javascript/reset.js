function resetEditor()
{
    // If the API is not detected, there shouldn't be any editors
    if (typeof FCKeditorAPI === "undefined")
    {
    	return;
    }

    // Loop through all the editor's instances
    for (var sEditorName in FCKeditorAPI.__Instances)
    {
        // The initial value that was set when the form was created
        // is stored in a hidden <INPUT> with the same name as the
        // editor (the editor itself is in an <IFRAME> with ___Frame
        // appended to the name.  Check whether that INPUT exists
        if (document.getElementById(sEditorName))
        {
            // Get the initial value
            var sInitialValue = document.getElementById(sEditorName).value;

            // Overwrite the editor's current value
            FCKeditorAPI.__Instances[sEditorName].SetHTML(sInitialValue);
        }
    }
} 

function resetAdvancedMultiSelect()
{
	
}

function resetElements()
{
	resetEditor();
	resetAdvancedMultiSelect();
}