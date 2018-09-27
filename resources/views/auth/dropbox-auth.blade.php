<body>
    @if (isset($company) && $company['dbx_token'])
        You have successfully granted access to your Dropbox Account.
    @else 
        ....redirecting
    @endif    
</body>
<script>
@if (isset($company))
    window.opener.location.reload();
    window.close();
@else
    (function(window){
        window.utils = {
            parseQueryString: function(str) {
                var ret = Object.create(null);

                if (typeof str !== 'string') {
                    return ret;
                }

                str = str.trim().replace(/^(\?|#|&)/, '');

                if (!str) {
                    return ret;
                }

                str.split('&').forEach(function (param) {
                    var parts = param.replace(/\+/g, ' ').split('=');
                    // Firefox (pre 40) decodes `%3D` to `=`
                    // https://github.com/sindresorhus/query-string/pull/37
                    var key = parts.shift();
                    var val = parts.length > 0 ? parts.join('=') : undefined;

                    key = decodeURIComponent(key);

                    // missing `=` should be `null`:
                    // http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
                    val = val === undefined ? null : decodeURIComponent(val);

                    if (ret[key] === undefined) {
                    ret[key] = val;
                    } else if (Array.isArray(ret[key])) {
                    ret[key].push(val);
                    } else {
                    ret[key] = [ret[key], val];
                    }
                });

                return ret;
            }
        };
    })(window);

    // Parses the url and gets the access token if it is in the urls hash
    function getAccessTokenFromUrl() {
        return utils.parseQueryString(window.location.hash).access_token;
    }
    // If the user was just redirected from authenticating, the urls hash will
    // contain the access token.
    function isAuthenticated() {
        return !!getAccessTokenFromUrl();
    }
    if (isAuthenticated()) {
        var accessToken = getAccessTokenFromUrl();
        var form = document.createElement('form')
        form.setAttribute('method', 'post')
        form.setAttribute('action', '/dropbox-auth')
        var hiddenFieldcsrfToken = document.createElement('input')
        hiddenFieldcsrfToken.setAttribute('type', 'hidden')
        hiddenFieldcsrfToken.setAttribute('name', '_token')
        hiddenFieldcsrfToken.setAttribute('value', '{{csrf_token()}}')
        form.appendChild(hiddenFieldcsrfToken)
        var hiddenFieldAccessToken = document.createElement('input')        
        hiddenFieldAccessToken.setAttribute('type', 'hidden')
        hiddenFieldAccessToken.setAttribute('name', 'accessToken')
        hiddenFieldAccessToken.setAttribute('value', accessToken)
        form.appendChild(hiddenFieldAccessToken)
        document.body.appendChild(form)
        form.submit()
    } else {
    }
@endif
</script>