
<!DOCTYPE html>
<html>
<head>
    <title>Get Facebook Token</title>
    <meta charset="UTF-8">
</head>
<body>
<?php
$data = base64_decode($_GET['data']);
parse_str($data, $params);
?>
<script>

    var clientId = '<?php echo $params['client_id'] ?>';
    var clientSecret = '<?php echo $params['client_secret'] ?>';
    var accountId = '<?php echo $params['account_id'] ?>';
    window.fbAsyncInit = function() {
        FB.init({
            appId      : clientId,
            cookie     : true,  // enable cookies to allow the server to access// the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v3.2' // use graph api version 3.2
        });


        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
        FB.api('/me/accounts', 'GET', {},
            function(response) {
                alert(response);
            }
        );
    }


    function statusChangeCallback(response) {
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            var access_token =   FB.getAuthResponse()['accessToken'];

            window.location.replace('http://mfbac.com/accounts/'+accountId+'/generate_app_token?client_id='+
                clientId+'&client_secret='+clientSecret+'&fb_exchange_token='+access_token
            );

        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
            document.getElementById('status').innerHTML = 'Please log ' +
                'into this app.';
        } else {
            // The person is not logged into Facebook, so we're not sure if
            // they are logged into this app or not.
            document.getElementById('status').innerHTML = 'Please log ' +
                'into Facebook.';
        }
    }
</script>


<fb:login-button scope="pages_manage_cta,publish_actions,public_profile,email,manage_pages,publish_pages,read_insights,ads_management" onlogin="checkLoginState();">
</fb:login-button>

<div id="status"></div>

</body>
</html>

