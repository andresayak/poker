require.config({
    urlArgs: 'v=' + appOptions.ver,
    waitSeconds: 15,
    paths: {
        jquery: '../lib/jquery-1.10.2',
        hammer: '../lib/hammer',
        backbone: '../lib/backbone-min',
        bootstrap: '../lib/bootstrap.min',
        underscore: '../lib/underscore-min',
        'jquery.cookie': '../lib/jquery.cookie',
        'jquery.emoticons': '../lib/jquery.emoticons',
        'jquery.hammer': '../lib/jquery.hammer',
        'facebook': 'https://connect.facebook.net/en_US/all',
        'mailru': '//connect.mail.ru/js/loader',
        'vkontakte': '//vk.com/js/api/xd_connection',
        'odnoklassniki': '//api.odnoklassniki.ru/js/fapi5',
        'jquery.mb': '../lib/jquery.mb.audio',
        'backbone-validation': '../lib/backbone-validation-min',
        localstorage: "../lib/backbone.localStorage",
        i18n: '../lib/i18next.min',
        konva: '../lib/konva.min',
        'gsap.KonvaPlugin': '../lib/KonvaPlugin',
        'TweenMax': '../lib/TweenMax.min',
        html2canvas: '../lib/html2canvas'
    },
    shim: {
        'jquery': {
            exports: '$'
        },
        'facebook': {
            exports: 'FB'
        },
        'jquery.emoticons': {
            deps: ['jquery'],
            exports: 'jQuery.fn.emoticons'
        },
        'jquery.mb': {
            deps: ['jquery']
        },
        'jquery.hammer': {
            deps: ['jquery']
        },
        'underscore': {
            deps: ['jquery'],
            exports: '_'
        },
        'backbone': {
            deps: ['jquery', 'underscore'],
            exports: 'Backbone'
        },
        'i18n': {
            deps: ['jquery']
        },
        'localstorage': {
            deps: ['backbone']
        },
        'bootstrap': {
            deps: ['jquery']
        },
        'gsap.KonvaPlugin': {
            deps: ['TweenMax']
        },
        'TweenMax': {
            exports: 'TweenMax'
        }
    }
});

if (!console) console = {log: function () {}, debug: function () {}};
/*window.onerror = function (errorMsg, file, line, column, errorObj) {
 if (typeof (errorMsg) === 'object' && errorMsg.srcElement && errorMsg.target) {
 if (errorMsg.srcElement == '[object HTMLScriptElement]' && errorMsg.target == '[object HTMLScriptElement]') {
 errorMsg = 'Error loading script';
 } else {
 errorMsg = 'Event Error - target:' + errorMsg.target + ' srcElement:' + errorMsg.srcElement;
 }
 }
 //errorMsg = encodeURIComponent(errorMsg.toString());
 console.log(errorMsg);
 /*if (errorObj !== undefined) {
 $.get(window.location.protocol + '//' + window.location.hostname + '/api/index.error', {
 'message': errorMsg,
 'stack': ((errorObj !== undefined && errorObj) ? errorObj.stack : null),
 'url': window.location.href,
 'file': file,
 'line': line
 }, null, 'json');
 } else {
 $.get(window.location.protocol + '//' + window.location.hostname + '/api/index.error', {
 'message': errorMsg,
 'url': window.location.href,
 'file': file,
 'line': line
 }, null, 'json');
 }*/
//return true;
//};
function processError(message) {
    throw new Error(message);
}
function FatalError(message) {
    processError(message);
}
require(
        ['app', 'jquery', 'bootstrap', 'config'],
        function (App, $, _b, config) {
            function getQueryParams(qs) {
                qs = qs.split("+").join(" ");
                var params = {}, tokens,
                        re = /[?&]?([^=]+)=([^&]*)/g;
                while (tokens = re.exec(qs)) {
                    params[decodeURIComponent(tokens[1])]
                            = decodeURIComponent(tokens[2]);
                }
                return params;
            }
            var query = getQueryParams(document.location.search);
            if (query.auth_user_id !== undefined) {
                var app = new App(config);
                app.setTestResponse({
                    user_id: query.auth_user_id
                });
                app.init();
                return;
            }
            if (config.auth_type == 'default') {
                var app = new App(config);
                app.init();
            }
            if (config.auth_type == 'fb') {
                //FB.Canvas.setAutoGrow(false);
                function onLoginFb(response) {
                    var start = function () {
                        var app = new App(config);
                        app.profile.setFacebookResponse(response.authResponse);
                        app.getFbMe(function () {
                            app.init();
                        });
                    };

                    if (query.request_ids !== undefined) {
                        FB.api(query.request_ids, function (r) {
                            if (r.from !== undefined)
                                response.authResponse.user_from_id = r.from.id;
                            start();
                        });
                    } else
                        start();
                }
                if (appOptions.testAuth !== undefined && appOptions.testAuth.length) {
                    var app = new App(config);
                    app.profile.setFacebookResponse(appOptions.testAuth);
                    app.init();
                } else {
                    require(['fb'], function () {
                        FB.getLoginStatus(function (authresponse) {
                            if (authresponse.status === 'connected') {
                                FB.api('/me/permissions', function (response) {
                                    var statusall = true;
                                    for (var i in config.facebook.permission) {
                                        var status = false;
                                        var p = config.facebook.permission[i];
                                        for (var j in response.data)
                                            if (response.data[j].permission == p && response.data[j].status == 'granted')
                                                status = true;
                                        if (!status)
                                            statusall = false;
                                    }
                                    if (statusall) {
                                        onLoginFb(authresponse);
                                    } else {
                                        FB.login(function (authresponse) {
                                            onLoginFb(authresponse);
                                        }, {scope: config.facebook.permission});
                                    }
                                });
                            } else {
                                FB.login(function (authresponse) {
                                    onLoginFb(authresponse);
                                }, {scope: config.facebook.permission});
                            }
                        }, function () {
                            console.log('fb error')
                        });
                    }, function (e) {
                        $('#startlog').text('Facebook disconected');
                        /*setTimeout(function(){
                         window.location.reload();
                         }, 3000);*/
                    });
                }
            }
        }
);
