/**
 * Created by mohamedchakiri on 05/07/2018.
 */

 var autocomplete;

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
