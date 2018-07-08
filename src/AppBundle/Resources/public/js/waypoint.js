/**
 * Created by mohamedchakiri on 05/07/2018.
 */

var autocomplete;
var geocoder;

function initAutocomplete() {
    var options = {
        componentRestrictions: {country: "fr"},
        types: ['geocode']
    };
    //Récupérer tous les input by name
    var waypointInputs = document.getElementById('waypoint_address');
    var autocomplete = new google.maps.places.Autocomplete(waypointInputs, options);
    autocomplete.inputId = waypointInputs.id;
}

function geocodeAddress(){
    var address = document.getElementById('waypoint_address').value;
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            //alert(results[0].geometry.location);
            lat = results[0].geometry.location.lat;
            lng = results[0].geometry.location.lng;
            //sendLatLng(lat, lng);
        } else {
            alert('Geocode n\'a pas abouti car : ' + status);
        }
    });
}

/*$('#send_partner').click(function (e) {
    $.ajax({
        method: 'POST',
        url: "/partner",
        data: {nom : 'erf'},
        success: function(){
            console.log('ok');
            alert('okb');
        },
        error: function (request, error) {
            alert(error);
            console.log(error);
        }
    });

});*/

$('#waypoint_address').change(function (e) {
    $.ajax({
        type: 'POST',
        url: "/partner",
        data: {'nom' : 'erf'},
        success: function(){
            console.log('ok');
            alert('okb');
        },
        error: function (request, error) {
            alert(error);
            console.log(error);
        }
    });

});
