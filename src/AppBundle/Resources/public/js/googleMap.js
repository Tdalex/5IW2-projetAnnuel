var geocoder;
var map;
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

    places = new google.maps.places.PlacesService(map);

    // Créer un nouveau style de map
    var styledMapType = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
    //Associer le style a la map et l'afficher.
    map.mapTypes.set('styled_map', styledMapType);
    map.setMapTypeId('styled_map');


    var marqueurs = [];
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

    });


}


function searchPlaces(bound) {
    var search = {
        bounds: bound,
        radius: '100',
        types: ['lodging']
    };

    var MARKER_PATH = 'https://developers.google.com/maps/documentation/javascript/images/marker_green';
    var markers = [];

    places.nearbySearch(search, function(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            clearMarkers(markers);
            // Create a marker for each hotel found, and
            // assign a letter of the alphabetic to each marker icon.
            for (var i = 0; i < results.length; i++) {
                var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
                var markerIcon = MARKER_PATH + markerLetter + '.png';
                // Use marker animation to drop the icons incrementally on the map.
                markers[i] = new google.maps.Marker({
                    position: results[i].geometry.location,
                    animation: google.maps.Animation.DROP,
                    icon: markerIcon
                });
                // If the user clicks a hotel marker, show the details of that hotel
                // in an info window.
                markers[i].placeResult = results[i];
                google.maps.event.addListener(markers[i], 'click', showInfoWindow);
                setTimeout(dropMarker(i, markers), i * 100);
                addResult(results[i], i);
            }
        }
    });
}

function showInfoWindow() {
    var infoWindow = new google.maps.InfoWindow({
        content: document.getElementById('info-content')
    });

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

function addResult(result, i) {
    var MARKER_PATH = 'https://developers.google.com/maps/documentation/javascript/images/marker_green';
    var results = document.getElementById('results');
    var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
    var markerIcon = MARKER_PATH + markerLetter + '.png';

    var tr = document.createElement('tr');
    tr.style.backgroundColor = (i % 2 === 0 ? '#F0F0F0' : '#FFFFFF');
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


function dropMarker(i, markers) {
    return function() {
        markers[i].setMap(map);
    };
}

function clearMarkers(markers) {
    for (var i = 0; i < markers.length; i++) {
        if (markers[i]) {
            markers[i].setMap(null);
        }
    }
}

function clearResults() {
    var results = document.getElementById('results');
    while (results.childNodes[0]) {
        results.removeChild(results.childNodes[0]);
    }
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
    //for(i=0;i<pointsMarqueurs.length;i++) {
        // trajet 1
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
            //unitSystem: google.maps.DirectionsUnitSystem.METRIC
        };

        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.suppressMarkers = true;
                //créer les boxes sur l'itinéraire
                createBoxes(response);
                //directionsDisplay.setOptions({polylineOptions:{strokeColor: '#008000'}, preserveViewport: true});
                getInfosRoutes(response);
            }
        });
    //}
}

function createBoxes(response){
    // Direction service for route boxer
    var routeboxer = new RouteBoxer();
    var distance = 20; // km

    // Box around the overview path of the first route
    var path = response.routes[0].overview_path;
    var bounds = routeboxer.box(path, distance);
    drawBoxes(bounds);
    for (var i = 0; i < bounds.length; i++) {
        (function (i) {
            setTimeout(function () {
                searchPlaces(bounds[i]);
            }, 400 * i);
        }(i));
    }
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

function getInfosRoutes(response){
    var route = response.routes[0];
    var summaryPanel = document.getElementById('directions-panel');
    summaryPanel.innerHTML = '';
    //Pour chaque route afficher informations
    for (var i = 0; i < route.legs.length; i++) {
        var routeSegment = i + 1;
        summaryPanel.innerHTML += '<b>Infos route: ' + routeSegment + '</b><br>';
        summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
        summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
    }
}


function getInfoMarkerDragend(marqueur){
    //Aficher le coordonnées au deplacement d'un marqueur
    google.maps.event.addListener(marqueur, 'dragend', function(event) {
        //message d'alerte affichant la nouvelle position du marqueur
        alert("La nouvelle coordonnée du marqueur est : "+event.latLng);
    });
}

function infoWindowMarker(map){
    //Fenetre d'information
    var infowindow =  new google.maps.InfoWindow({
         title: "Titre",
         content: 'Hello World!',
         map: map,
         position: new google.maps.LatLng(48.856393, 2.343472)
     });
    return infowindow;
}

//geocoder les adresses saisie
function codeAddress() {
    var addressDep = document.getElementById('addressDep').value;
    var addressDes = document.getElementById('addressDes').value;
    var addressesFix = [addressDep, addressDes];
    //Recuperer tous les input créer dynamiquement
    var container = document.getElementById('container').getElementsByTagName("input");
    //Tableau des adresses intermediaires
    var addressesInt = [];
    for(i=0;i<container.length;i++){
        addressesInt[i] = document.getElementById(container[i].id).value;
    }
    //Tous mettre dans le tableau adresses
    var addresses = addressesFix.concat(addressesInt);
    //Vider la liste des hotels
    clearResults();
    //addresses.push(addressInt);
    for(i=0;i<addresses.length;i++){
        geocodeAddress(addresses[i]);
    }
    getItinerary(addresses, map);
}

function geocodeAddress(address){
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
            //search hotels for place
            var place = results[0];
            map.panTo(place.geometry.location);
            // map.setZoom(15);
            //searchPlaces(map.getBounds());
        } else {
            alert('Geocode n\'a pas abouti car : ' + status);
        }
    });
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