$('#contact_send').click(function (e) {
    e.preventDefault();
    var nom = $('#contact_name').val();
    var prenom = $('#contact_fname').val();
    var email = $('#contact_mail').val();
    var sujet = $('#contact_subject').val();
    var message = $('#contact_message').val();
    var url = $(this).attr('data-url');

    $.ajax({
        method: 'POST',
        url: url,
        data: {'nom' : nom, 'prenom' : prenom, 'email' : email, 'sujet' : sujet, 'message' : message},
        success: function(){
            console.log('ok');
        }
    });
});