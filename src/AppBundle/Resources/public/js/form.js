/**
 * Created by mohamedchakiri on 09/12/2017.
 */
$(document).ready(function(){
    var nbstop = parseInt($('#prototype-stop').attr('data-nbstop'));
    var nbClick = nbstop + 1;
    //Attention à bien vérifier que vos selecteurs correspondent à votre code
    $('#filldetails').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();

        var $prototypeStop = $('#prototype-stop').clone();
        $prototypeStop = $($prototypeStop.html().replace(/__name__/g, nbClick));

        var $linkDelete = $('<div class="center-align"><a href="#" style="text-decoration: none;"><i class="material-icons red-text text-lighten-1">delete_forever</i><a></div>');
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

    $('.delete-tome').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var idSection = $(this).parent().parent().attr('id');
        $('#'+idSection).remove();
    })
});