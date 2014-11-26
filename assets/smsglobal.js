jQuery(function($) {
    $("#subscription_verification_form").hide();
    $("#subscription_form").submit(function(e) {

        var _n = $("#name");
        var _m = $("#mobile");

        if(_n.val()=="")
        {
            $('#smsglobal_alertmessage').html(smsglobalL10n.fullname);
            _n.focus();
            return false;
        }
        else if(_m.val()=="")
        {
            $('#smsglobal_alertmessage').html(smsglobalL10n.mobilenumber);
            _m.focus();
            return false;
        }

        $('#smsglobal_alertmessage').html(smsglobalL10n.sending);
        var url = $(this).prop('action');

        $.ajax({
            url: url,
            type: 'POST',
            data : $('#subscription_form').serialize(),
            beforeSend: function ( xhr ) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            },
            dataType: "json"
        }).done(function ( data ) {
            $('#smsglobal_alertmessage').html(data.msg);
            if(data.error == 0)
            {
                $("#subscription_form").hide();
                $("#subscription_verification_form [name=mobile]").val($("#mobile").val());
                $("#subscription_verification_form").show();
            }
        }).fail(function() {
            $('#smsglobal_alertmessage').html(smsglobalL10n.requestproblem);
        });

        e.preventDefault();
        return false;
    });

    $("#subscription_verification_form").submit(function(e) {

        var _c = $("#code");
        var _mV = $("#mobile_verify");

        if($('#code').val()=="")
        {
            $('#smsglobal_alertmessage').html(smsglobalL10n.verificationcode);
            _c.focus();
            return false;
        }
        if(_mV.val()=="")
        {
            $('#smsglobal_alertmessage').html(smsglobalL10n.verificationmobile);
            _mV.focus();
            return false;
        }

        $.ajax({
            url: $("#subscription_verification_form").prop('action'),
            type: 'POST',
            data : $('#subscription_verification_form').serialize(),
            beforeSend: function ( xhr ) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            },
            dataType: "json"
        }).done(function ( data ) {
            $('#smsglobal_alertmessage').html(data.msg);
            if(data.error == 0)
            {
                $("#subscription_verification_form").hide();
            }
        }).fail(function() {
            $('#smsglobal_alertmessage').html(smsglobalL10n.requestproblem);
        });

        e.preventDefault();
        return false;
    });
});
