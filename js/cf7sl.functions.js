// Function to update total submitions after dropdown list changed
function getFormSelectValue(sel) {
    var id = getInputID(sel.name);
    if (id != -1) {
        jQuery('#submitions' + id).val(getTotalSubmition(sel.value));
    }
}

// Function to update form status after limit number changed
function updateStatusValue(input) {
    var id = getInputID(input.name);
    if (id != -1) {
        var current_total_submition = jQuery('#submitions' + id).val();
        var remining = input.value - current_total_submition;
        var status = jQuery('#status' + id).text("Remaining submissions = " + remining + " \n Form Status = ");
        status.html(status.html().replace(/\n/g, '<br/>'));
    }
}

// Function to get id from input name
function getInputID(name) {
    var matches = name.match(/\[(.*?)\]/);
    if (matches) {
        var submatch = matches[1];
        return submatch;
    }
    return -1;
}

// Function to get total number of submition for x form
function getTotalSubmition(formID) {
    var total_submitions = jQuery.parseJSON(jQuery('#total_submitions').val());
    total_submitions.forEach(function(item) {
        if (item['ID'] == formID) {
            return item['count'];
        }
    });
    return 0;
}