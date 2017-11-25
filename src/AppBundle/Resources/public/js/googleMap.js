/**
 * Created by mohamedchakiri on 25/11/2017.
 */

function myMap() {
    var mapProp= {
        center:new google.maps.LatLng(46.719208,1.474055),
        zoom:5,
        mapTypeId: google.maps.MapTypeId.ROADMA
    };
    var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

    // //création du marqueur
    // var marqueur = new google.maps.Marker({
    //     position: new google.maps.LatLng(48.856393, 2.343472),
    //     map: map,
    //     draggable: true
    // });
    //
    // //chemin du tracé du polyline
    // var parcours = [
    //     new google.maps.LatLng(48.856393, 2.343472),
    //     new google.maps.LatLng(47.950028, 1.907178),
    //     new google.maps.LatLng(45.835296, 4.768425),
    //     new google.maps.LatLng(43.299219, 5.365324)
    // ];
    //
    // var raodtrip = new google.maps.Polyline({
    //     path: parcours,//chemin du tracé
    //     strokeColor: "#FF0000",//couleur du tracé
    //     strokeOpacity: 1.0,//opacité du tracé
    //     strokeWeight: 2//grosseur du tracé
    // });
    //
    // //lier le tracé à la carte
    // //ceci permet au tracé d'être affiché sur la carte
    // raodtrip.setMap(map);
    //
    // //Lier un evenement au clic du marquer
    // google.maps.event.addListener(marqueur, 'click', function() {
    //     //Fenetre d'information
    //     var infowindow =  new google.maps.InfoWindow({
    //         title: "Titre",
    //         content: 'Hello World!',
    //         map: map,
    //         position: new google.maps.LatLng(48.856393, 2.343472)
    //     });
    //     infowindow.open(map, this);
    // });

    //Tableau des marqueurs
    var tabMarqueurs = [];
    var parcours = [];
    var tmp = [];
    //Ajouter un marquer au clique
    google.maps.event.addListener(map, 'click', function(event) {
        //Créer le marqueur
        var marqueur = new google.maps.Marker({
            position: event.latLng,
            map: map,
            draggable: true
        });
        //Remplir le tableau parcours
        parcours.push(marqueur.getPosition().lat(),marqueur.getPosition().lng());
        console.log(parcours);

        //Lier un evenement au clic du marquer
        google.maps.event.addListener(marqueur, 'click', function() {
            //Fenetre d'information
            var infowindow =  new google.maps.InfoWindow({
                title: "Titre",
                content: 'Hello World!',
                map: map,
                position: new google.maps.LatLng(48.856393, 2.343472)
            });
            infowindow.open(map, this);
        });
        //Aficher le coordonnées au deplacement d'un marqueur
        google.maps.event.addListener(marqueur, 'dragend', function(event) {
            //message d'alerte affichant la nouvelle position du marqueur
            alert("La nouvelle coordonnée du marqueur est : "+event.latLng);
        });
        tabMarqueurs.push(marqueur);

    });

}