<!doctype html>
<html class="uk-height-1-1" lang="en" data-base="@base('/')" data-route="@route('/')">
<head>
    <meta charset="UTF-8">
    <title>@lang('Authenticate Please!')</title>
    <link rel="icon" href="@base('/favicon.ico')" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    {{ $app->assets($app['app.assets.base'], $app['cockpit/version']) }}
    {{ $app->assets(['assets:lib/uikit/js/components/form-password.min.js'], $app['cockpit/version']) }}

    <style>

        html, body {
            background: #0e0f19;
        }

    </style>

    <script type="text/javascript" src="https://cdn.auth0.com/js/lock/10.20.0/lock.min.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {

            var $logout = App.$('#logout');

            var lock = new Auth0Lock('<?=$app['config/auth0/secret']?>', '<?=$app['config/auth0/domain']?>', {
                defaultDatabaseConnection: '<?=$app['config/auth0/database']?>',
                allowedConnections: ['Username-Password-Authentication'],
                container: 'login-container',
                allowSignUp: false
            });

            function login(token) {

                App.request('/api/auth0/loginByAccessToken', {
                    token: token
                }).then(function(user) {

                    App.reroute('/');

                }).catch(function() {

                    if (token) {
                        $logout.removeClass('uk-hidden');
                    }
                });
            }

            function logout() {
                localStorage.removeItem('cockpit.auth0.accesstoken');

                lock.logout({
                    returnTo: '<?=$app->getSiteUrl(true)?>/auth/login'
                });
            }

            // Listen for the authenticated event and get profile
            lock.on("authenticated", function(authResult) {

                lock.getUserInfo(authResult.accessToken, function(error, profile) {

                    if (error) {
                        return;
                    }

                    localStorage['cockpit.auth0.accesstoken'] = authResult.accessToken;
                    login(authResult.accessToken);
                });
            });

            lock.show();

            if (localStorage['cockpit.auth0.accesstoken']) {

                if (location.search.indexOf('logout=1') > -1) {
                    logout();
                } else {
                    login(localStorage['cockpit.auth0.accesstoken']);
                }

            }

            $logout.on('click', function() {
                logout();
            });

            window.lock = lock;
        });

    </script>

</head>
<body class="login-page uk-height-viewport uk-flex uk-flex-middle uk-flex-center">

    <div>
        <div id="login-container"></div>

        <div class="uk-text-center uk-margin">
            <button id="logout" class="uk-button uk-button-large uk-button-outline uk-text-danger uk-hidden" type="button" name="button">Logout</button>
        </div>
    </div>

</body>
</html>
