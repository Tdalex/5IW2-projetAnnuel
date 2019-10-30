/* JS for using data api elasticsearch */

var geocoder;
var map;
var autocomplete;
var lat, lng;
var itineraryBounds;
var markersPlace = [];

function initialize() {

    geocoder = new google.maps.Geocoder();

    var latlng = new google.maps.LatLng(46.719208,1.474055);

    var mapOptions = {
        zoom: 6,
        center: latlng,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        scaleControl: false,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: false,
        mapTypeControl: true,
        mapTypeControlOptions : {
            position: google.maps.ControlPosition.RIGHT_CENTER
        }
    }

    map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);

    //Service Place
    places = new google.maps.places.PlacesService(map);

    // Créer un nouveau style de map
    var styledMapType = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
    //Associer le style a la map et l'afficher.
    map.mapTypes.set('styled_map', styledMapType);
    map.setMapTypeId('styled_map');


    getInfoWindow();

}

//Get data for info places
function getInfoWindow(){
    var htmlcontent = '<div id="info-content">'+
        '<table>'+
        '<tr id="iw-url-row" class="iw_table_row">'+
        '<td id="iw-icon" class="iw_table_icon"></td>'+
        '<td id="iw-url"></td>'+
        '</tr>'+
        '<tr id="iw-address-row" class="iw_table_row">'+
        '<td class="iw_attribute_name">Address:</td>'+
        '<td id="iw-address"></td>'+
        '</tr>'+
        '<tr id="iw-phone-row" class="iw_table_row">'+
        '<td class="iw_attribute_name">Telephone:</td>'+
        '<td id="iw-phone"></td>'+
        '</tr>'+
        '<tr id="iw-rating-row" class="iw_table_row">'+
        '<td class="iw_attribute_name">Rating:</td>'+
        '<td id="iw-rating"></td>'+
        '</tr>'+
        '<tr id="iw-website-row" class="iw_table_row">'+
        '<td class="iw_attribute_name">Website:</td>'+
        '<td id="iw-website"></td>'+
        '</tr>'+
        '</table>'+
        '</div>';

    //windows info place
    var infoWindow = new google.maps.InfoWindow({
        content: htmlcontent
    });

    return infoWindow;
}

//Clear All markers
function clearMarkers() {
    if (markersPlace !== undefined) {
        for (var i = 0; i < markersPlace.length; i++) {
            if (markersPlace[i]) {
                markersPlace[i].setMap(null);
            }
        }
    }
}

//Clear data from list result
function clearResults(tbody) {
    var results = document.getElementById(tbody);
    while (results.childNodes[0]) {
        results.removeChild(results.childNodes[0]);
    }
}

//Get center of each box around the itinerary
function centerBoxes(boxes){
    var centerBounds =  [];
    for (var i = 0; i < boxes.length; i++) {
        centerBounds.push(boxes[i].getCenter());
    }
    return centerBounds;
}

//Get places from api and put it in map
function nearbyPlaces(){
    var nearbyPlaces = document.getElementsByClassName('nearbyPlaces');
    if (nearbyPlaces) {
        Array.prototype.forEach.call(nearbyPlaces, function(nearbyPlace, i) {
            nearbyPlace.addEventListener('click', function(e) {
                if (itineraryBounds !== undefined){
                    var bounds = centerBoxes(itineraryBounds);
                    var tbody = nearbyPlace.parentElement.getAttribute('data-tbody');
                    var type = nearbyPlace.parentElement.getAttribute('data-place');
                    var icon = "/bundles/app/images/markers/svg/" + nearbyPlace.parentElement.getAttribute('data-icon') + ".svg";

                    clearMarkers();
                    clearResults(tbody);

                    var data = {};
                    data.coordinates = {};

                    for (var i = 0; i < bounds.length; i++) {
                        data.coordinates[i] = {'lat': bounds[i].lat(), 'lon': bounds[i].lng()};
                    }

                    data.radius = 20;
                    var params = $.param(data);

                    $.ajax({
                        url:  window.location.origin + "/api/waypoint/by_distance?" + params + "&type=" + type,
                    }).done(function( response ) {

                        var markers = [];
                        var iconStd = icon;
                        for (var i = 0; i < response.count; i++) {
                            icon = iconStd;
                            var size = new google.maps.Size(25, 20);
                            if (response.data[i].is_sponsor == true){
                                icon = "/bundles/app/images/markers/svg/Rocket_7.svg";
                                size = new google.maps.Size(50, 45);
                            }
                            markers[i] = new google.maps.Marker({
                                position: {lat: response.data[i].coordinates.lat, lng: response.data[i].coordinates.lon},
                                animation: google.maps.Animation.DROP,
                                icon: {
                                    url: icon,
                                    scaledSize: size
                                }
                            });
                            // If the user clicks a hotel marker, show the details of that hotel
                            // in an info window.
                            markers[i].placeResult = response.data[i];
                            google.maps.event.addListener(markers[i], 'click', showInfoWindowApi);
                            setTimeout(
                                dropMarker(i, markers)
                                , i * 100);
                            addResult(markers[i].placeResult, i, markers, icon, tbody);
                        }
                        //Get all markers place in array
                        markersPlace = markersPlace.concat(markers);
                    });

                    $(document).ajaxStart(function() {
                        $("#loading"+ type).show();
                    }).ajaxStop(function() {
                        $("#loading"+ type).hide();
                    });
                }else {
                    alert("Veuillez choisir un itinéraire !")
                }
            })
        })
    }
}


