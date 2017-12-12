var geocoder;
var map;
var placeSearch, autocomplete;
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
        //unitSystem: google.maps.DirectionsUnitSystem.METRIC
    };

    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            directionsDisplay.suppressMarkers = true;
            //directionsDisplay.setOptions({polylineOptions:{strokeColor: '#008000'}, preserveViewport: true});
            getInfosRoutes(response);
        }
    });
}

function getInfosRoutes(response){
    var route = response.routes[0];
    var summaryPanel = document.getElementById('directions-panel');
    summaryPanel.innerHTML = '';
    //Pour chaque route afficher informations
    for (var i = 0; i < route.legs.length; i++) {
        var routeSegment = i + 1;
        summaryPanel.innerHTML += '<b>Infos route: ' + routeSegment + '</b><br>';
        summaryPanel.innerHTML += route.legs[i].start_address + ' à ';
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
    //Si onChangeHandler est appelé reinitialise la map
    var onChangeHandler = initialize();
    var addressDep = document.getElementById('addressDep').value;
    document.getElementById('addressDep').addEventListener('change', onChangeHandler);
    var addressDes = document.getElementById('addressDes').value;
    document.getElementById('addressDes').addEventListener('change', onChangeHandler);
    var addressesFix = [addressDep, addressDes];
    //Recuperer tous les input créer dynamiquement
    var container = document.getElementById('container').getElementsByTagName("input");
    //Tableau des adresses intermediaires
    var addressesInt = [];
    for(i=0;i<container.length;i++){
        addressesInt[i] = document.getElementById(container[i].id).value;
        document.getElementById(container[i].id).addEventListener('change', onChangeHandler);
    }
    //Tous mettre dans le tableau adresses
    var addresses = addressesFix.concat(addressesInt);
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
        } else {
            alert('Geocode n\'a pas abouti car : ' + status);
        }
    });
}

function initAutocomplete() {
    //Récupérer tous les input by name
    var acInputs = document.getElementsByClassName("autocomplete");
    for (var i = 0; i < acInputs.length; i++) {
        var autocomplete = new google.maps.places.Autocomplete(acInputs[i], {types: ['geocode']});
        autocomplete.inputId = acInputs[i].id;
    }
    autocomplete.setComponentRestrictions(
        {'country': ['fr']});
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
/**
 * Created by mohamedchakiri on 09/12/2017.
 */
function addFields(){
    var container = document.getElementById("container");
    //Boolean pour verifier si la div est remplie ou non
    var haschilds = container.hasChildNodes();
    var childs = container.childNodes;
    var num = childs.length/3;
    //texte
    container.appendChild(document.createTextNode("Etape " + (num+1)));
    var input = document.createElement("input");
    input.type = "text";
    input.name = "etape" + (num+1);
    input.id = "etape" + (num+1);
    input.className = "autocomplete";
    container.appendChild(input);
    container.appendChild(document.createElement("br"));
    //Autocomplete des input
    initAutocomplete();
}