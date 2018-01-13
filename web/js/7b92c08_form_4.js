/**
 * Created by mohamedchakiri on 09/12/2017.
 */
$(document).ready(function(){
    var arraySections = [];
    var i = 1;
    var allSections = document.querySelectorAll('section');
    allSections.forEach(function (t) {
        if(t.hasAttribute('id')) {
            t.html().replace(/__name__/g, i);
            i++;
            arraySections.push(t);
        }
    });

    console.log(arraySections);
    var nbstop = parseInt($('#prototype-stop').attr('data-nbstop'));
    var nbClick = nbstop + 1;
    //Attention à bien vérifier que vos selecteurs correspondent à votre code
    $('#filldetails').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();

        var $prototypeStop = $('#prototype-stop').clone();
        $prototypeStop = $($prototypeStop.html().replace(/__name__/g, nbClick));

        var $linkDelete = $('<div><a href="#" class="btn btn-danger delete-tome">Supprimer</a></div>');
        $prototypeStop.append($linkDelete);

        $linkDelete.on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            $prototypeStop.remove();
        });

        $('#container-stop').append($prototypeStop);
        //$('#roadtrip_stops_'+nbClick+'_stopNumber').val(nbClick);
        initAutocomplete();
        nbClick++;
    });
});