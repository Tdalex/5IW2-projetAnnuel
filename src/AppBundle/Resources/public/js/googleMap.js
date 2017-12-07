var geocoder;
var map;
function initialize() {
    geocoder = new google.maps.Geocoder();

    var latlng = new google.maps.LatLng(46.719208,1.474055);
    var mapOptions = {
        zoom: 7,
        center: latlng
    }
    map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);

    // Créer un nouveau style de map
    var styledMapType = new google.maps.StyledMapType(
        [
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
        ],
        {name: 'Styled Map'}
    );
    //Associer le style a la map et l'afficher.
    map.mapTypes.set('styled_map', styledMapType);
    map.setMapTypeId('styled_map');

    //Tableau des marqueurs
    var tabMarqueurs = [];
    var parcours = [];
    var tmp = [];
    //Les evenments  au clique
    google.maps.event.addListener(map, 'click', function(event) {
        //Créer le marqueur
        var marqueur = new google.maps.Marker({
            position: event.latLng,
            map: map,
            draggable: true
        });
        //Remplir le tableau parcours avec les coordonnées des marquers
        parcours.push(''+marqueur.getPosition().lat()+', '+marqueur.getPosition().lng()+'');
        console.log(parcours);

        //Le chemin du tracé
        var traceParcours = new Array();
        for(i=0;i<parcours.length;i++) {
            var point =new google.maps.LatLng(parcours[i].split(',')[0],parcours[i].split(',')[1]);
            traceParcours.push(point);
        }

        //Tracer l'itinéraire entre les marqueurs
        for(i=0;i<traceParcours.length;i++) {
            // trajet 1
            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer({ 'map': map });
            var request = {
                origin : traceParcours[i],
                destination: traceParcours[i+1],
                travelMode : google.maps.DirectionsTravelMode.DRIVING,
                unitSystem: google.maps.DirectionsUnitSystem.METRIC
            };

            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                    directionsDisplay.suppressMarkers = true;
                    directionsDisplay.setOptions({polylineOptions:{strokeColor: '#008000'}, preserveViewport: true});
                }
            });
        }

        //créer le polyline
        var roadTrip = new google.maps.Polyline({
            path: traceParcours,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        //Liée le tracé à lamap
        roadTrip.setMap(map);

        //Lier un evenement au clic du marquer
        google.maps.event.addListener(marqueur, 'click', function() {
            //Afficher l'adresse du marqueur lors du clique
            geocoder.geocode({'latLng': event.latLng});

            //Fenetre d'information
             /*var infowindow =  new google.maps.InfoWindow({
             title: "Titre",
             content: 'Hello World!',
             map: map,
             position: new google.maps.LatLng(48.856393, 2.343472)
             });
             infowindow.open(map, this);*/
        });

        //Aficher le coordonnées au deplacement d'un marqueur
        google.maps.event.addListener(marqueur, 'dragend', function(event) {
            //message d'alerte affichant la nouvelle position du marqueur
            alert("La nouvelle coordonnée du marqueur est : "+event.latLng);
        });
        tabMarqueurs.push(marqueur);

    });
}

//geocoder l'adresse saisie sur la map
function codeAddress() {
    var address = document.getElementById('address').value;
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}