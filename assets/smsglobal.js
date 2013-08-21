jQuery(function($) {

    $("#subscription_form").submit(function(e) {

        var _e = $("#email");
        var _n = $("#name");
        var _m = $("#mobile");

        if(_n.val()=="")
        {
            $('#smsglobal_alertmessage').html("Please enter your full name.");
            _n.focus();
            return false;
        }
        else if(_e.val() =="")
        {
            $('#smsglobal_alertmessage').html("Please enter your email address.");
            _e.focus();
            return false;
        }
        else if(_e.val()!="" && (_e.val().indexOf("@",0)==-1 || _e.val().indexOf(".",0)==-1))
        {
            $('#smsglobal_alertmessage').html("Please enter a valid email address.");
            _e.focus();
            _e.select();
            return false;
        }
        else if(_m.val()=="")
        {
            $('#smsglobal_alertmessage').html("Please enter your mobile number.");
            _m.focus();
            return false;
        }

        $('#smsglobal_alertmessage').html("Sending...");
        var url = $(this).prop('action');

        $.ajax({
            url: url,
            type: 'POST',
            data : $('#subscription_form').serialize(),
            beforeSend: function ( xhr ) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            }
        }).done(function ( data ) {
            $('#smsglobal_alertmessage').html(data);
            if(data == "We have sent you a verification code to your mobile.")
            {
                $("#smsglobal_email").val("");
                $("#smsglobal_name").val("");
                $("#smsglobal_url").val("");
                $("#subscription_wrapper form").hide();
            }
        }).fail(function() {
            $('#smsglobal_alertmessage').innerHTML = 'There was a problem with the request.';
        });

        e.preventDefault();
        return false;
    });
});