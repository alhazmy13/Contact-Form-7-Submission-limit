function addFormField() {

	var id = jQuery('#id').val();
	var formList = jQuery('#form_list').val();
	var formNamesArray = jQuery.parseJSON(formList);
		
		
	var row ="<li id ='row" + id + "'>" +
				"<div style='float: left;'>" +
					"<div style='float: left;'>" +
					"<label for='cf7slfind" + id + "'>Form ID:</label>" +
					"<br />" +
					"<select  name='cf7slfind["+ id +"]' id='cf7slfind" + id + "'>" ;



	for(var i=0;i<formNamesArray.length;i++){
    	row += "<option name='cf7slfind["+ id +"]' id='cf7slfind"+ id +"' value='"+ formNamesArray[i]['ID']+ "'>" + formNamesArray[i]['post_title']+  "</option>";
    }


	row += "</select>"+
					"</div>" +
					"<br />"+

					"<div style='float: left;'>" +
					"<label for='limit" + id + "'>Limit:</label>" +
					"<br />" +
					"<input class='textbox' type='number'  name='limit["+ id +"]' id='limit" + id + "' value='-1'></input>" +
					"</div>" +
					"<br />"+


					"<div style='float: left;'>" +
					"<label for='cf7slreplace" + id + "'>Replace With:</label>" +
					"<br />" +
					"<textarea class='left' name='cf7slreplace["+ id +"]' id='cf7slreplace" + id + "'></textarea>" +
					"</div>" +
					"<br />"+

				"</div>" +
			"</li>";



		jQuery("#cf7sl_itemlist").append(row);

		id = (id - 1) + 2;
		document.getElementById("id").value = id;
		jQuery('html, body').animate( {	scrollTop: jQuery("#row"+(id-1)).offset().top }, 1000);
	
}

function removeFormField(id) {
	jQuery(id).remove();
}

jQuery(function() {
	jQuery( "#cf7sl_itemlist" ).sortable();
});