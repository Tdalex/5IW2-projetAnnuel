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
            alert(results[0].geometry.location);
            var lat = results[0].geometry.location.lat;
            var lng = results[0].geometry.location.lng;
        } else {
            alert('Geocode n\'a pas abouti car : ' + status);
        }
    });
}
