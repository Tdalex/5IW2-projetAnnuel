/**
 * Created by mohamedchakiri on 05/10/2017.
 */

var geocoder;
var map;
var autocomplete;
var infoWindow;
var lat, lng;
var itineraryBounds;
var markersPlace = [];

//Get global variable from service
//https://openclassrooms.com/courses/2763916-passez-des-variables-a-javascript-depuis-symfony2
var JsVars = jQuery('#js-var-env').data('vars');
var myGlobalEnvironnementVariable =JsVars.myGlobalEnvironnementVariable;

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
        // scrollwheel: false
    }
    map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);

    //Service Place
    places = new google.maps.places.PlacesService(map);

    // Créer un nouveau style de map
    var styledMapType = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
    //Associer le style a la map et l'afficher.
    map.mapTypes.set('styled_map', styledMapType);
    map.setMapTypeId('styled_map');

   /*var marqueurs = [];
   //Les evenments  au clique
    google.maps.event.addListener(map, 'click', function(event) {
        //Créer le marqueur
        var marqueur = createMarker(event, map);
        //Tableau des points des marqueurs dans la map
        var pointsMarqueurs = pointMarkersMap(marqueur, marqueurs);
        //Tracer l'itinéraire
        getItinerary(pointsMarqueurs, map);
        //tracé entre les marqueur
        var polyline = createPolyline(pointsMarqueurs);
        polyline.setMap(map);
        //Get coordonnées marqeur lors du dragend
        getInfoMarkerDragend(marqueur);

        //Lier un evenement au clic du marquer
        google.maps.event.addListener(marqueur, 'click', function() {
            //Afficher l'adresse du marqueur lors du clique
            //geocoder.geocode({'latLng': event.latLng});

            var infoWindow = infoWindowMarker(map);
            infoWindow.open(map, this);
        });
    });*/

    //Get html content
    /*infoWindow = new google.maps.InfoWindow({
        content: document.getElementById('info-content')
    });*/

    if (myGlobalEnvironnementVariable == "dev") makeGrid();

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
    infoWindow = new google.maps.InfoWindow({
        content: htmlcontent
    });
}

function createMarker(event, map){
    var marqueur = new google.maps.Marker({
        position: event.latLng,
        map: map,
        draggable: true
    });
    return marqueur;
}

//Tous les points map de tous les marqueurs
function pointMarkersMap(marqueur, marqueurs){
    //Remplir le tableau marqueurs avec les coordonnées de chaque marqueur
    marqueurs.push(''+marqueur.getPosition().lat()+', '+marqueur.getPosition().lng()+'');
    //Assigner les coordonnées des marqueurs a des points map et mettre dans un tableau
    var pointsMarqueurs = new Array();
    for(i=0;i<marqueurs.length;i++) {
        var point =new google.maps.LatLng(marqueurs[i].split(',')[0],marqueurs[i].split(',')[1]);
        pointsMarqueurs.push(point);
    }
    return pointsMarqueurs;
}

function createPolyline(pointsMarqueurs){
    //créer le polyline
    var polyline = new google.maps.Polyline({
        path: pointsMarqueurs,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });
    return polyline;
}

function getItinerary(pointsMarqueurs, map){
    //Tracer l'itinéraire entre les marqueurs
    var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer({ 'map': map });
    var waypoints = [];
    for(i=2;i<pointsMarqueurs.length;i++) {
        waypoints.push({
            location: pointsMarqueurs[i],
            stopover: true
        });
    }
    var request = {
        origin : pointsMarqueurs[0],
        destination: pointsMarqueurs[1],
        waypoints: waypoints/*[{location: pointsMarqueurs[2], stopover: false}, {location: "lyon, france", stopover: false}]*/,
        optimizeWaypoints: true,
        travelMode : google.maps.DirectionsTravelMode.DRIVING,
        unitSystem: google.maps.DirectionsUnitSystem.METRIC
    };

    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            directionsDisplay.suppressMarkers = true;
            clearResults('directionPanel');
            directionsDisplay.setPanel(document.getElementById('directionPanel'));
            //créer les boxes sur l'itinéraire
            createBoxes(response);
            //directionsDisplay.setOptions({polylineOptions:{strokeColor: '#008000'}, preserveViewport: true});
            getInfosRoutes(response);
        }
    });
}

function createBoxes(response){

    // Direction service for route boxer
    var routeboxer = new RouteBoxer();
    var distance = 10; // km

    // Box around the overview path of the first route
    var path = response.routes[0].overview_path;
    itineraryBounds = routeboxer.box(path, distance);

    if (myGlobalEnvironnementVariable == "dev") drawBoxes(itineraryBounds);
    centerBoxes(itineraryBounds);
}

// Draw the array of boxes as polylines on the map
function drawBoxes(boxes) {
    boxpolys = new Array(boxes.length);
    for (var i = 0; i < boxes.length; i++) {
        boxpolys[i] = new google.maps.Rectangle({
            bounds: boxes[i],
            fillOpacity: 0,
            strokeOpacity: 1.0,
            strokeColor: '#000000',
            strokeWeight: 3,
            map: map
        });
    }
}

function centerBoxes(boxes){
    var centerBounds =  [];
    for (var i = 0; i < boxes.length; i++) {
        centerBounds.push(boxes[i].getCenter());
    }
    return centerBounds;
}

function getInfosRoutes(response){
    var route = response.routes[0];
    /*var summaryPanel = document.getElementById('directions-panel');
    summaryPanel.innerHTML = '';*/
    var message;
    for (var i = 0; i < route.legs.length; i++) {
        var routeSegment = i + 1;
        message = 'Infos route ' + routeSegment + ' : ' + route.legs[i].distance.text + '<br>' + route.legs[i].start_address + ' à ' + route.legs[i].end_address;
        Materialize.toast(message, 5000);
        message  = '';
    }
}

function getInfoMarkerDragend(marqueur){
    //Aficher le coordonnées au deplacement d'un marqueur
    google.maps.event.addListener(marqueur, 'dragend', function(event) {
        //message d'alerte affichant la nouvelle position du marqueur
        alert("La nouvelle coordonnée du marqueur est : "+event.latLng);
    });
}

//geocoder les adresses saisie
function codeAddress() {
    //Si onChangeHandler est appelé reinitialise la map
    var onChangeHandler = initialize();
    var addressDep = document.getElementById('addressDep').value;
    var addressDepId = document.getElementById('addressDep').id;
    document.getElementById('addressDep').addEventListener('change', onChangeHandler);
    var addressDes = document.getElementById('addressDes').value;
    var addressDesId = document.getElementById('addressDes').id;
    document.getElementById('addressDes').addEventListener('change', onChangeHandler);
    var addressesFix = [addressDep, addressDes];
    var idFix = [addressDepId, addressDesId];
    //Recuperer tous les input créer dynamiquement
    var container = document.getElementById('container-stop').getElementsByTagName("section");
    //Tableau des adresses intermediaires
    var addressesInt = [];
    var idInt = [];
    for(i=1;i<container.length;i++){
        input = container[i].getElementsByClassName("autocomplete-field")[0];
        addressesInt[i-1] = document.getElementById(input.id).value;
        idInt[i-1] = document.getElementById(input.id).id;
        document.getElementById(input.id).addEventListener('change', onChangeHandler);
    }
    //Tous mettre dans le tableau adresses
    var addresses = addressesFix.concat(addressesInt);
    var ids = idFix.concat(idInt);
    for(i=0;i<addresses.length;i++){
        if(i >= 2) {
            ids[i] = ids[i].substring(0, ids[i].length-7);
        }
        geocodeAddress(addresses[i], ids[i]);
    }
    getItinerary(addresses, map);
    $('#roadtrip_submit').prop('disabled', false);
}

function geocodeAddress(address, id ){
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                /*icon: "/bundles/app/images/markers/svg/Motel_3.svg"*/
            });
            lat = marker.getPosition().lat();
            lng = marker.getPosition().lng();
            $('#'+id+'lat').val(lat);
            $('#'+id+'lon').val(lng);
        } else {
            alert('Geocode n\'a pas abouti car : ' + status);
        }
    });
}

function initAutocomplete() {
    var options = {
        componentRestrictions: {country: "fr"},
        types: ['geocode']
    };
    //Récupérer tous les input by name
    var acInputs = document.getElementsByClassName("autocomplete");
    for (var i = 0; i < acInputs.length; i++) {
        var autocomplete = new google.maps.places.Autocomplete(acInputs[i], options);
        autocomplete.inputId = acInputs[i].id;
    }
}

function hotel(franceBounds){
    franceBounds = franceBounds || null;
    var bounds;
    if (franceBounds){
        bounds = franceBounds;
    }else{
        bounds = itineraryBounds;
    }
    var place =  'lodging';
    var icon = "/bundles/app/images/markers/svg/Motel_3.svg";
    var tbody = 'resultsHotels';
    clearMarkers();
    clearResults(tbody);
    if (bounds){
        for (var i = 0; i < bounds.length; i++) {
            (function (i) {
                setTimeout(function () {
                    searchPlaces(bounds[i], place, icon, tbody);
                }, 400 * i);
            }(i));
        }
    } else {
        alert("Veuillez choisir un itinéraire !");
    }
}

function food(franceBounds){
    franceBounds = franceBounds || null;
    var bounds;
    if (franceBounds){
        bounds = franceBounds;
    }else{
        bounds = itineraryBounds;
    }
    var place =  'restaurant';
    var icon = "/bundles/app/images/markers/svg/Food_6.svg";
    var tbody = 'resultsFoods';
    clearMarkers();
    clearResults(tbody);
    if (bounds){
        for (var i = 0; i < bounds.length; i++) {
            (function (i) {
                setTimeout(function () {
                        searchPlaces(bounds[i], place, icon, tbody);
                    }
                    , 400 * i);
            }(i));
        }
    } else {
        alert("Veuillez choisir un itinéraire !")
    }
}

function apiPlaces(bounds, type, icon, tbody) {
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

}

function searchPlaces(bound, place, icon, tbody) {
    var search = {
        bounds: bound,
        radius: '100',
        types: [place]
    };
    var limitDisplayPlacesBox = 2;

    places.nearbySearch(search, function(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            var markers = [];
            // Create a marker for each hotel found, and
            // assign a letter of the alphabetic to each marker icon.
            for (var i = 0; i < results.length /*&& i < limitDisplayPlacesBox*/; i++) {
                // Use marker animation to drop the icons incrementally on the map.
                markers[i] = new google.maps.Marker({
                    position: results[i].geometry.location,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: icon,
                        scaledSize: new google.maps.Size(25, 20)
                    }
                });
                // If the user clicks a hotel marker, show the details of that hotel
                // in an info window.
                markers[i].placeResult = results[i];
                google.maps.event.addListener(markers[i], 'click', showInfoWindow);
                setTimeout(
                    dropMarker(i, markers)
                    , i * 100);
                addResult(results[i], i, markers, icon, tbody);
            }
        }
        //Get all markers place in array
        markersPlace = markersPlace.concat(markers);
    });
}

function addResult(result, i, markers, icon, tbody) {
    var results = document.getElementById(tbody);
    var markerIcon = icon;

    var tr = document.createElement('tr');
    tr.style.backgroundColor = (i % 2 === 0 ? '#F0F0F0' : '#FFFFFF');
    tr.style.cursor = 'pointer';
    tr.onclick = function() {
        google.maps.event.trigger(markers[i], 'click');
    };

    var iconTd = document.createElement('td');
    var nameTd = document.createElement('td');
    var icon = document.createElement('img');
    icon.src = markerIcon;
    icon.setAttribute('class', 'placeIcon');
    icon.setAttribute('className', 'placeIcon');
    var name = document.createTextNode(result.name);
    iconTd.appendChild(icon);
    nameTd.appendChild(name);
    tr.appendChild(iconTd);
    tr.appendChild(nameTd);
    results.appendChild(tr);
}

function clearResults(tbody) {
    var results = document.getElementById(tbody);
    while (results.childNodes[0]) {
        results.removeChild(results.childNodes[0]);
    }
}

//Show info for palces from api
function showInfoWindowApi() {
    var marker = this;
    infoWindow.open(map, marker);
    buildIWContentApi(marker.placeResult);
}

// Get the place details for a hotel. Show the information in an info window,
// anchored on the marker for the hotel that the user selected.
function showInfoWindow() {
    var marker = this;
    places.getDetails({placeId: marker.placeResult.place_id},
        function(place, status) {
            if (status !== google.maps.places.PlacesServiceStatus.OK) {
                return;
            }
            infoWindow.open(map, marker);
            buildIWContent(place);
        });
}

// load inforamtion in window with data Api.
function buildIWContentApi(place) {
    if (place.icon) {
        document.getElementById('iw-icon').innerHTML = '<img class="hotelIcon" ' +
            'src="' + place.icon + '"/>';
    }
    document.getElementById('iw-url').innerHTML = '<b>' + place.name + '</b>';
    document.getElementById('iw-address').textContent = place.address;

    if (place.phone) {
        document.getElementById('iw-phone-row').style.display = '';
        document.getElementById('iw-phone').textContent =
            place.phone;
    } else {
        document.getElementById('iw-phone-row').style.display = 'none';
    }

    // Assign a five-star rating to the hotel, using a black star ('&#10029;')
    // to indicate the rating the hotel has earned, and a white star ('&#10025;')
    // for the rating points not achieved.
    if (place.rating) {
        var ratingHtml = '';
        for (var i = 0; i < 5; i++) {
            if (place.rating < (i + 0.5)) {
                ratingHtml += '&#10025;';
            } else {
                ratingHtml += '&#10029;';
            }
            document.getElementById('iw-rating-row').style.display = '';
            document.getElementById('iw-rating').innerHTML = ratingHtml;
        }
    } else {
        document.getElementById('iw-rating-row').style.display = 'none';
    }

    // The regexp isolates the first part of the URL (domain plus subdomain)
    // to give a short URL for displaying in the info window.
    if (place.website) {
        var hostnameRegexp = new RegExp('^https?://.+?/');
        var fullUrl = place.website;
        var website = hostnameRegexp.exec(place.website);
        if (website === null) {
            website = 'http://' + place.website + '/';
            fullUrl = website;
        }
        document.getElementById('iw-website-row').style.display = '';
        document.getElementById('iw-website').textContent = website;
    } else {
        document.getElementById('iw-website-row').style.display = 'none';
    }
}

// Load the place information into the HTML elements used by the info window.
function buildIWContent(place) {
    document.getElementById('iw-icon').innerHTML = '<img class="hotelIcon" ' +
        'src="' + place.icon + '"/>';
    document.getElementById('iw-url').innerHTML = '<b><a href="' + place.url +
        '">' + place.name + '</a></b>';
    document.getElementById('iw-address').textContent = place.vicinity;

    if (place.formatted_phone_number) {
        document.getElementById('iw-phone-row').style.display = '';
        document.getElementById('iw-phone').textContent =
            place.formatted_phone_number;
    } else {
        document.getElementById('iw-phone-row').style.display = 'none';
    }

    // Assign a five-star rating to the hotel, using a black star ('&#10029;')
    // to indicate the rating the hotel has earned, and a white star ('&#10025;')
    // for the rating points not achieved.
    if (place.rating) {
        var ratingHtml = '';
        for (var i = 0; i < 5; i++) {
            if (place.rating < (i + 0.5)) {
                ratingHtml += '&#10025;';
            } else {
                ratingHtml += '&#10029;';
            }
            document.getElementById('iw-rating-row').style.display = '';
            document.getElementById('iw-rating').innerHTML = ratingHtml;
        }
    } else {
        document.getElementById('iw-rating-row').style.display = 'none';
    }

    // The regexp isolates the first part of the URL (domain plus subdomain)
    // to give a short URL for displaying in the info window.
    if (place.website) {
        var hostnameRegexp = new RegExp('^https?://.+?/');
        var fullUrl = place.website;
        var website = hostnameRegexp.exec(place.website);
        if (website === null) {
            website = 'http://' + place.website + '/';
            fullUrl = website;
        }
        document.getElementById('iw-website-row').style.display = '';
        document.getElementById('iw-website').textContent = website;
    } else {
        document.getElementById('iw-website-row').style.display = 'none';
    }
}


function dropMarker(i, markers) {
    return function() {
        markers[i].setMap(map);
    };
}

function clearMarkers() {
    for (var i = 0; i < markersPlace.length; i++) {
        if (markersPlace[i]) {
            markersPlace[i].setMap(null);
        }
    }
}

//Grille de la france
function makeGrid() {
    var marker1;
    var marker2;
    var rectangleLng = [];
    var franceBounds = [];

    marker1 = new google.maps.Marker({
        position: new google.maps.LatLng(42.837042,-4.51328),
        map: map,
        title: 'marker1'
    });
    marker2 = new google.maps.Marker({
        position: new google.maps.LatLng(50.73645528205696,7.731628249999978),
        map: map,
        title: 'marker2'
    });

    var leftSideDist = marker2.getPosition().lng() - marker1.getPosition().lng();
    var belowSideDist = marker2.getPosition().lat() - marker1.getPosition().lat();

    var dividerLat = 20;
    var dividerLng = 20;
    var excLat = belowSideDist / dividerLat;
    var excLng = leftSideDist / dividerLng;

    var m1Lat = marker1.getPosition().lat();
    var m1Lng = marker1.getPosition().lng();

    for (var i = 0; i < dividerLat; i++) {
        if (!rectangleLng[i]) rectangleLng[i] = [];
        for (var j = 0; j < dividerLng; j++) {
            if (!rectangleLng[i][j]) rectangleLng[i][j] = {};
            var franceBound = new google.maps.LatLngBounds(
                new google.maps.LatLng(m1Lat + (excLat * i), m1Lng + (excLng * j)),
                new google.maps.LatLng(m1Lat + (excLat * (i + 1)), m1Lng + (excLng * (j + 1)))
            );
            rectangleLng[i][j] = new google.maps.Rectangle({
                strokeColor: '#180000',
                strokeWeight: 1,
                map: map,
                bounds: franceBound
            });
            franceBounds.push(franceBound);
        }
    }
    //hotel(franceBounds);
    //food(franceBounds);
}


var nearbyPlaces = document.getElementsByClassName('nearbyPlaces')
if (nearbyPlaces) {
    Array.prototype.forEach.call(nearbyPlaces, function(nearbyPlace, i) {
        nearbyPlace.addEventListener('click', function(e) {
            if (itineraryBounds !== undefined){
                var bounds = centerBoxes(itineraryBounds);
                var tbody = nearbyPlace.parentElement.getAttribute('data-tbody');
                var place = nearbyPlace.parentElement.getAttribute('data-place');
                var icon = "/bundles/app/images/markers/svg/" + nearbyPlace.parentElement.getAttribute('data-icon') + ".svg";

                clearMarkers();
                clearResults(tbody);

                apiPlaces(bounds, place, icon, tbody);
            }else {
                alert("Veuillez choisir un itinéraire !")
            }
        })
    })
}

var styles = [
    {
        "featureType": "administrative",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#6195a0"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#f2f2f2"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#ffffff"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#e6f3d6"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "lightness": 45
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#f4d2c5"
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "labels.text",
        "stylers": [
            {
                "color": "#4e4e4e"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#f4f4f4"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#787878"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "color": "#eaf6f8"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#eaf6f8"
            }
        ]
    }
];