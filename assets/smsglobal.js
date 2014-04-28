jQuery(function($) {
    $("#subscription_verification_form").hide();
    $("#subscription_form").submit(function(e) {

        var _n = $("#name");
        var _m = $("#mobile");

        if(_n.val()=="")
        {
            $('#smsglobal_alertmessage').html("Please enter your full name.");
            _n.focus();
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
                $("#subscription_form").hide();
                $("#subscription_verification_form [name=mobile]").val($("#mobile").val());
                $("#subscription_verification_form").show();
            }
        }).fail(function() {
            $('#smsglobal_alertmessage').html('There was a problem with the request.');
        });

        e.preventDefault();
        return false;
    });

    $("#subscription_verification_form").submit(function(e) {
        if($('#code').val()=="")
        {
            $('#smsglobal_alertmessage').html("Please enter the verification code.");
            _n.focus();
            return false;
        }
        if($('#mobile_verification').val()=="")
        {
            $('#smsglobal_alertmessage').html("Please enter your mobile for verification purpose.");
            _n.focus();
            return false;
        }

        $.ajax({
            url: $("#subscription_verification_form").prop('action'),
            type: 'POST',
            data : $('#subscription_verification_form').serialize(),
            beforeSend: function ( xhr ) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            }
        }).done(function ( data ) {
            $('#smsglobal_alertmessage').html(data);
            if(data == "Your subscription has been verified sucessfully.")
            {
                $("#subscription_verification_form").hide();
            }
        }).fail(function() {
            $('#smsglobal_alertmessage').html('There was a problem with the request.');
        });

        e.preventDefault();
        return false;
    });
});