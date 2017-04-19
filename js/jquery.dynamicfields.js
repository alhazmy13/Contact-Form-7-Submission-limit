function addFormField() {

	var id = jQuery('#id').val();
	var formList = jQuery('#test').val();
	var formNamesArray = jQuery.parseJSON(formList);
		
		
	var row ="<li id ='row" + id + "'>" +
				"<div style='float: left;'>" +
					"<div style='float: left;'>" +
					"<label for='farfind" + id + "'>Form ID:</label>" +
					"<br />" +
					"<select  name='farfind["+ id +"]' id='farfind" + id + "'>" ;



	for(var i=0;i<formNamesArray.length;i++){
    	row += "<option name='farfind["+ id +"]' id='farfind"+ id +"' value='"+ formNamesArray[i]['ID']+ "'>" + formNamesArray[i]['post_title']+  "</option>";
    }


	row += "</select>"+
					"</div>" +
					"<br />"+

					"<div style='float: left;'>" +
					"<label for='limit" + id + "'>Limit:</label>" +
					"<br />" +
					"<input class='textbox' type='number'  name='limit["+ id +"]' id='limit" + id + "'></input>" +
					"</div>" +
					"<br />"+


					"<div style='float: left;'>" +
					"<label for='farreplace" + id + "'>Replace With:</label>" +
					"<br />" +
					"<textarea class='left' name='farreplace["+ id +"]' id='farreplace" + id + "'></textarea>" +
					"</div>" +
					"<br />"+

				"</div>" +
			"</li>";



		jQuery("#far_itemlist").append(row);

		id = (id - 1) + 2;
		document.getElementById("id").value = id;
		jQuery('html, body').animate( {	scrollTop: jQuery("#row"+(id-1)).offset().top }, 1000);
	
}

function removeFormField(id) {
	jQuery(id).remove();
}

jQuery(function() {
	jQuery( "#far_itemlist" ).sortable();
});