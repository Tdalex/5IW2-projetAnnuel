//cherche dans la table stop les addresses pour faire de l'autocomplétion pour le champ départ
$('#icon_prefix').autocomplete({
    source: function(request, response) {
        //data-url contient le chemin vers la fonction qui cherche dans la base de données
        var url = $('#icon_prefix').attr('data-url');
        $.ajax({
            url: url,
            success: function (data) {
                var results = $.ui.autocomplete.filter($.map(data, function(value, key) {
                    return {
                        label: value.address,
                        value: value.address
                    }
                }), request.term);
                response(results);
            }
        });
    },
    minLength: 2,
    appendTo: "#form_search"
});

//cherche dans la table stop les addresses pour faire de l'autocomplétion pour le champ destination
$('#icon_telephone').autocomplete({
    source: function(request, response) {
        //data-url contient le chemin vers la fonction qui cherche dans la base de données
        var url = $('#icon_telephone').attr('data-url');
        $.ajax({
            url: url,
            success: function (data) {
                var results = $.ui.autocomplete.filter($.map(data, function(value, key) {
                    return {
                        label: value.address,
                        value: value.address
                    }
                }), request.term);
                response(results);
            }
        });
    },
    minLength: 2,
    appendTo: "#form_search"
});

$('#search_rt').click(function (e) {
    e.preventDefault();
    var dep = $('#icon_prefix').val();
    var dest = $('#icon_telephone').val();
    var url = $(this).attr('data-url');

    $.ajax({
        method: "POST",
        url: url,
        data: {'dep' : dep, 'dest' : dest},
        success: function(results) {
            var res = $.parseJSON(results.roadtrips);
            var tableResults = $('#results_search_roadtrip tbody');
            for (var i = 0; i<res.length; i++) {
                var rt = res[i];
                console.log(rt);
                tableResults.append('<tr><td>'+rt.title+'</td><td>'+rt.description+'</td><td><a class="btn btn-info" href="">Consulter</a></td></tr>');
            }
            $('#results_search').removeAttr('hidden');
        }
    });
});