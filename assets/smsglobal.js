var http_req = false;
function sgSubscriptionPOSTRequest(url, parameters) {
    http_req = false;
    if (window.XMLHttpRequest) {
        http_req = new XMLHttpRequest();
        if (http_req.overrideMimeType) { http_req.overrideMimeType('text/html');}
    } else if (window.ActiveXObject) { // IE
        try {
            http_req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    if (!http_req) {
        alert('Cannot create XMLHTTP instance');
        return false;
    }

    http_req.onreadystatechange = sgSubscriptionContents;
    http_req.open('POST', url, true);
    http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http_req.setRequestHeader("Content-length", parameters.length);
    http_req.setRequestHeader("Connection", "close");
    http_req.send(parameters);
}

function sgSubscriptionContents()
{
    if (http_req.readyState == 4)
    {
        if (http_req.status == 200)
        {
            if(http_req.responseText != "Name and Mobile are invalid.")
            {
                result = http_req.responseText;
                document.getElementById('smsglobal_alertmessage').innerHTML = result;
            }
            else
            {
                result = http_req.responseText;
                document.getElementById('smsglobal_alertmessage').innerHTML = result;
                document.getElementById("smsglobal_email").value = "";
                document.getElementById("smsglobal_name").value = "";
                document.getElementById("smsglobal_mobile").value = "";
                document.getElementById("smsglobal_url").value = "";
                document.getElementById("subscription_wrapper").innerHTML = "";
            }
        }
        else
        {
            document.getElementById('smsglobal_alertmessage').innerHTML = 'There was a problem with the request.';
        }
    }
}

function smsglobal_subscription_submit(obj,url)
{


    _e=document.getElementById("smsglobal_email");
    _n=document.getElementById("smsglobal_name");
    _m=document.getElementById("smsglobal_mobile");

    if(_n.value=="")
    {
        document.getElementById('smsglobal_alertmessage').innerHTML = "Please enter your full name.";
        _n.focus();
        return false;
    }
    else if(_e.value=="")
    {
        document.getElementById('smsglobal_alertmessage').innerHTML = "Please enter your email address.";
        _e.focus();
        return false;
    }
    else if(_e.value!="" && (_e.value.indexOf("@",0)==-1 || _e.value.indexOf(".",0)==-1))
    {
        document.getElementById('smsglobal_alertmessage').innerHTML = "Please enter a valid email address.";
        _e.focus();
        _e.select();
        return false;
    }
    else if(_m.value=="")
    {
        document.getElementById('smsglobal_alertmessage').innerHTML = "Please enter your mobile number.";
        _m.focus();
        return false;
    }

    document.getElementById('smsglobal_alertmessage').innerHTML = "Sending...";

    var str = "name=" + encodeURI( document.getElementById("smsglobal_name").value ) + "&email=" + encodeURI( document.getElementById("smsglobal_email").value ) + "&mobile=" + encodeURI( document.getElementById("smsglobal_mobile").value ) + "&url=" + encodeURI( document.getElementById("smsglobal_url").value)  + "&email=" + encodeURI( document.getElementById("smsglobal_email").value);

    sgSubscriptionPOSTRequest(url+'/scripts/subscriptionSave.php', str);
}