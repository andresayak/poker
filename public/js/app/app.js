define([
    'backbone', 'i18n', 
    'models/profile/item', 'models/library/item', 
    'services/templates', 'services/auth',
    'services/Listener', 'services/Locator',
    'models/poker/list'
],
    function (Backbone, i18n, Profile, Library, Templates, Auth, Listener, ServiceLocator, RoomList) {
        
        var gself;
        
        function App(config){
            _.extend(this, Backbone.Events);
            this.profile = new Profile();
            this.library = new Library();
            this.roomList = new RoomList();
            this.windows = {};
            this.images = {};
            this.timeDiff = 0;
            this.profile.lang = config.langDefault;
            this.auth = new Auth(this.profile);
            this.listener = new Listener();
            var self = this;
            this.profile.on('changeLang', function(code, callback){
                self.setLang(code, callback);
            });
            ServiceLocator.set('profile', this.profile);
            ServiceLocator.set('library', this.library);
            this.config = config;
            ServiceLocator.set('config', this.config);
            ServiceLocator.set('listener', this.listener);
            ServiceLocator.set('app', this);
            ServiceLocator.set('cards', config.cards);
            gself = this;
        }
        
        App.prototype.getTimeNow = function(){
            return new Date().getTime() - this.timeDiff;
        };
                
        App.prototype.init = function(){
            try {
                this.initAnimation();
                var self = this;
                this.initDefaults();
                this.initI18n(function () {
                    self.initTemplates(function(){
                        self.initProfile(function(){
                            self.preloadImages('config', function () {
                                self.requestLibrary(function(){
                                    self.shopListRequest({
                                        success: function () {
                                            self.getChatMessagesRequest(function(){
                                                self.dispatch('index', 'index', {}, function(){
                                                    $('#app .layout-screencompany').remove();
                                                });
                                            });
                                        }
                                    });
                                });
                            });
                        });
                    });
                });
            } catch (exception) {
                console.error(exception.message);
            }
        };
        
        App.prototype.initAnimation = function(){
            var requestId = 0;
            window.requestAnimFrame = (function () {
                return  window.requestAnimationFrame ||
                        window.webkitRequestAnimationFrame ||
                        window.mozRequestAnimationFrame ||
                        window.oRequestAnimationFrame ||
                        window.msRequestAnimationFrame ||
                        function (/* function */ callback) {
                             window.setTimeout(callback);
                        };
            })();
            var stop = false;
            var fps, startTime, now, then, elapsed;
            var fps = 30;
            var fpsInterval = 1000 / fps;
            then = Date.now();
            startTime = then;
            var self = this;
            var frame = 0;
            (function animloop() {
                if (stop) {
                    return;
                }
                requestAnimFrame(animloop);
                now = Date.now();
                elapsed = now - then;
                if (elapsed > fpsInterval) {
                    self.trigger('frame', ++frame);
                    then = now - (elapsed % fpsInterval);
                    //var sinceStart = now - startTime;
                    //var currentFps = Math.round(1000 / (sinceStart / ++frameCount) * 100) / 100;
                }
            })();
        };
        
        App.prototype.preloadImages = function (configName, callback) {
            var XHRFunction = function () {
                try {
                    return new ActiveXObject("Msxml2.XMLHTTP.6.0");
                } catch (err1) {
                }
                try {
                    return new ActiveXObject("Msxml2.XMLHTTP.3.0");
                } catch (err2) {
                }
                return null;
            };
            var XHR = typeof XMLHttpRequest === 'undefined' ? XHRFunction() : XMLHttpRequest;
            var self = this;
            var config = self.getConfig(configName);
            var images = config.images;
            var files = config.files;
            App.loadCount = 0;
            var max = Object.keys(images).length + files.length;
            var counter = function () {
                App.loadCount++;
                //var per = Math.floor(App.loadCount / max * 100);
                if (App.loadCount == max) {
                    ServiceLocator.set('images', self.images);
                    callback();
                }
            };
            var loading = function (i, type) {
                if (type == 'image') {
                    var key = images[i];
                    var url = config.img_path + key;
                    url = url.replace('__lang__', self.langName);
                    key = key.replace('__lang__', '');
                    self.images[i] = new Image();
                    self.images[i].crossOrigin = 'Anonymous';
                    self.images[i].onload = function () {
                        counter();
                    };
                    self.images[i].onerror = function () {
                        counter();
                    };
                    self.images[i].src = url;
                } else if (type == 'file') {
                    var xhr = new XHR();
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState !== 4)
                            return;
                        counter();
                    };
                    xhr.onerror = function (e) {
                        counter();
                    };
                    xhr.open('GET', window.location.origin + files[i], true);
                    xhr.send();
                }

            };
            var status = true;
            var n = 0;
            
            for (var i in images) {
                if (typeof (images[i]) == 'string') {
                    n++;
                    status = false;
                    loading(i, 'image');
                }
            }
            for (var i in files) {
                if (typeof (files[i]) == 'string') {
                    n++;
                    status = false;
                    loading(i, 'file');
                }
            }
            if (status) {
                callback();
            }
        };
        
        App.prototype.getConfig = function (configName) {
            if (!arguments.length) {
                configName = 'config';
            }
            return this[configName];
        };
        
        App.prototype.route = function(url){
            this.router.navigate(url, {trigger: true});
        };
        
        App.prototype.initDefaults = function(){
            var self = this;
            Backbone.Model.prototype.toJSON = function() {
                var json = _.clone(this.attributes);
                for(var attr in json) {
                  if((json[attr] instanceof Backbone.Model) || (json[attr] instanceof Backbone.Collection)) {
                    json[attr] = json[attr].toJSON();   
                  }
                }
                return json;
            };
            Backbone.Model.prototype.sync = function(method, model, options) {
                var store = model.localStorage || model.collection.localStorage;console.log('store', store);
                if(store){
                        var resp;
                        switch (method) {
                            case "read":
                                resp = model.id ? store.find(model) : store.findAll();
                                break;
                            case "create":
                                resp = store.create(model);
                                break;
                            case "update":
                                resp = store.update(model);
                                break;
                            case "delete":
                                resp = store.destroy(model);
                                break;
                        }

                        if (resp) {
                            options.success(resp);
                        } else {
                            options.error("Record not found");
                        }
                }else{
                    if(typeof(model.methodUrl) == 'function'){
                        var url = model.methodUrl(method.toLowerCase());
                        options = options || {};
                        options.type = 'POST';
                        options.error = function(e){
                            console.log('response error', e);
                        };
                        options.url = url;
                    }
                    Backbone.sync(method, model, options);
                }
            };
            _.template.escapeHtml = function(str){
                return $('<div/>').html(str).text();
            };
            _.template.nl2br = function(str){
                return str.replace(/\n/g, "<br/>");
            };
            _.template.date = function(unixtime, format){
                var midnight = new Date();
                midnight.setHours(0, 0, 0, 0);
                var date = new Date(unixtime * 1000);
                var hour = date.getHours();
                hour = ((hour < 10) ? '0' : '') + hour;
                var min = date.getMinutes();
                min = ((min < 10) ? '0' : '') + min;
                var year = date.getUTCFullYear();
                var month = date.getUTCMonth() + 1;
                month = ((month < 10) ? '0' : '') + month;
                var day = date.getUTCDate();
                day = ((day < 10) ? '0' : '') + day;
                var self = this;
                
                if(typeof(format) != 'function'){
                    if(format == 'min'){
                        var format = function (h, m, d, M, y){return h + ':' + m;}
                    }else{
                        var format = function (h, m, d, M, y){
                            if(date.getTime() >= midnight.getTime()){
                                return self.t('time.today in') + ' '+ h + ':' + m;
                            }else if(date.getTime() >= midnight.getTime() - (3600 * 24 * 1000)){
                                return self.t('time.yesterday in') + ' '+ h + ':' + m;
                            }else{
                                return h + ':' + m + ' ' + d + '-' + M + '-' + y;
                            }
                        };
                    }
                }
                return format(hour, min, day, month, year);
            };
            $(window).resize(function() {
                self.trigger('resize', {});
                $('#app').height($(window).height());
            });
        };
        
        App.prototype.initListener = function(){
            this.listener.init(this.config.socket);
        };
        
        App.prototype.window = function (controller, action, routerParams) {
            var self = this;
            this.controllerName = (controller !== undefined)?controller: 'index';
            this.actionName = (action !== undefined)?action: 'index';
            $.proxy(require(['controllers/'+this.controllerName], function(Controller) {
                var renderView = function(params){
                    if(typeof(params) == 'object'){
                        params.app = self;
                        var name = self.controllerName + '/'+self.actionName;
                        require(['views/' + self.controllerName + '/' + self.actionName], function (View) {
                            var windowView = new View(params);
                            windowView.loader.start();
                            windowView.layout = self.layout;
                            windowView.render(function(){
                            });
                            $("#app").append(windowView.el);
                            self.windows[name] = windowView;
                        });
                    }
                };
                var params = Controller[self.actionName+'Window'](self, routerParams);
                params = (params === undefined)?{}:params; 
                if(params!== false){
                    var renderAction = function(params){
                        renderView(params);
                    };
                    var rec = function(params){
                        if (typeof (params) == 'function') {
                            params(function(params){
                                rec(params);
                            });
                        } else {
                            renderAction(params);
                        }
                    };
                    rec(params);
                }
            }), this);
        };
        App.prototype.dispatch = function (controller, action, routerParams, callback) {
            routerParams = (routerParams === undefined)?{}:routerParams;
            var self = this;
            this.controllerName = (controller !== undefined)?controller: 'index';
            this.actionName = (action !== undefined)?action: 'index';
            $.proxy(require(['controllers/'+this.controllerName], function(Controller) {
                var renderView = function(params){
                    if(typeof(params) == 'object'){
                        params.app = self;
                        require(['views/'+self.controllerName + '/'+self.actionName], function(View) {
                            var old;
                            if (self.view !== undefined){
                                old = self.view;
                            }
                            self.view = new View(params);
                            self.view.layout = self.layout;
                            self.view.render(function(){
                                if(typeof(callback) == 'function'){
                                    callback();
                                    if(old!== undefined){
                                        old.remove();
                                    }
                                }
                                self.view.$el.css('visibility', 'visible');
                            });
                            self.view.$el.css('visibility', 'hidden');
                            $("#app").append(self.view.el);  
                        });
                    }
                };
                var params = Controller[self.actionName+'Action'](self, routerParams);
                params = (params === undefined)?{}:params; 
                if(params!== false){
                    var renderAction = function(params){
                        renderView(params);/*
                        var layoutName = (params.layoutName !== undefined)?params.layoutName:'default';
                        if (self.layout===undefined || self.layout.name != layoutName){
                            $.proxy(require(['views/layouts/'+layoutName], function(Layout) {
                                if(self.layout !== undefined){
                                    self.layout.undelegateEvents();
                                    self.layout.$el.removeData().unbind(); 
                                }
                                self.layout = new Layout({
                                    profile: self.profile
                                });
                                self.layout.app = self;
                                self.layout.render();
                                renderView(params);
                            }), this);
                        }else{
                            self.layout.render();
                            renderView(params);
                        }*/
                    };
                    var rec = function(params){
                        if (typeof (params) == 'function') {
                            params(function(params){
                                rec(params);
                            });
                        } else {
                            renderAction(params);
                        }
                    };
                    rec(params);
                }
            }), this);
        };
        
        App.prototype.getRoomsPokerRequest = function(p, options, loader){
            var self = this;
            this.request('poker.getRooms', {p: p}, function(data) {
                if(data.status){
                    self.roomList.set(data.list);
                    options.success(data);
                }
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader); 
        };
        
        App.prototype.getChatMessagesRequest = function(callback, loader){
            var self = this;
            this.request('chat.getList', {}, function(data) {
                if(data.status){
                    self.profile.chatsManager.setListByType(data.list, 'public');
                    callback(data);
                }
            }, function(jqXHR, textStatus) {
            }, loader); 
        };
        
        App.prototype.shopListRequest = function(callbacks, loader) {
            var self = this;
            this.request('shop.getList', {}, function(data) {
                if(data.status){
                    self.profile.shopList.initStatus = true;
                    self.profile.shopList.set(data.list);
                    callbacks.success(data);
                }else callbacks.error(data);
            }, function(jqXHR, textStatus) {
                callbacks.error();
            }, loader); 
        };
        
        App.prototype.shopBuyRequest = function(id, count, callbacks, loader) {
            var self = this;
            var authType = 'fb';
//            self.profile.user.objectList.set([
//                {code: 'chip', count: 100}
//            ]);
            if(authType == 'fb'){
                var params = {
                    method: 'pay',
                    action: 'purchaseitem',
                    product: window.location.origin+'/api/shop.getInfo?id='+id+'&lang='+self.langName,
                    quantity: count,
                };
                FB.ui(params, function(data) {
                    if(data === undefined || data.error_message!==undefined){
                        if(typeof(callbacks.error) == 'function'){
                            callbacks.error(data);
                        }
                    }else{
                        if(data.status === 'completed'){
                            self.request('shop.callback', {
                                payment_id: data.payment_id,
                            }, function(data) {
                                if(data.status){
                                    self.profile.user.objectList.set(data.objectList);
                                    callbacks.success(data);
                                }else callbacks.error(data);
                            }, function(jqXHR, textStatus) {
                                callbacks.error();
                            }, loader); 
                        }else{
                            if(typeof(callbacks.error) == 'function'){
                                callbacks.error(data);
                            }
                        }
                    }
                });
            }
        };
        
        App.prototype.joinPokerRequest = function(id, position, money, options, error, loader){
            this.requestGame(id, 'poker.join', {
                id: id,
                position: position,
                money: money
            }, function (data) {
                if (data.status) {
                    options.success(data);
                } else
                    options.error(data);
            }, function (jqXHR, textStatus) {
                options.error();
            }, loader); 
        };
        
        App.prototype.enterPokerRequest = function(id, options, loader){
            var self = this;
            var room = this.roomList.findWhere({
                id: id.toString()
            });
            if(!room){
                return;
            }
            var server = this.library.serverList.findWhere({
                'code': room.get('server_code')
            });
            if(!server){
                return;
            }
                this.listener.addConnection('poker', server.get('socket'), function(){
                    self.requestGame(id, 'poker.enter', {
                        id: id
                    },  function(data) {
                        if(data.status){
                            options.success(data);
                        }else error(data);
                    }, function(jqXHR, textStatus) {
                        options.error();
                    }, loader); 
                });
        };
        
        App.prototype.closePokerRequest = function(id, options, loader){
            var self = this;
            this.requestGame(id, 'poker.close', {
                id: id,
            },  function(data) {
                if(data.status){
                    self.listener.closeConnection('poker');
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        
        App.prototype.foldPokerRequest = function(id, options, loader){
            var self = this;
            this.requestGame(id, 'poker.fold', {
                id: id,
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        
        App.prototype.raisePokerRequest =  function(id, value, options, loader){
            var self = this;
            this.requestGame(id, 'poker.raise', {
                id: id,
                value: value
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        
        App.prototype.leavePokerRequest = function(id, options, loader){
            gself.requestGame(id, 'poker.leave', {
                id: id
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        App.prototype.checkPokerRequest = function(id, options, loader){
            var self = this;
            this.requestGame(id, 'poker.check', {
                id: id
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        App.prototype.callPokerRequest = function(id, options, loader){
            var self = this;
            this.requestGame(id, 'poker.call', {
                id: id
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
        
        App.prototype.allInPokerRequest = function(id, options, loader){
            var self = this;
            this.requestGame(id, 'poker.allIn', {
                id: id
            }, function(data) {
                if(data.status){
                    options.success(data);
                }else error(data);
            }, function(jqXHR, textStatus) {
                options.error();
            }, loader);
        };
                
        App.prototype.play = function(options){
            var self = this;
            var params = this.profile.getFacebookResponse();
            params['type'] = 'facebook';
            
            this.request('user.play', params, function(data){
                if(data.status){
                    self.profile.set({
                        access_token: data.access_token,
                        signkey: data.signkey
                    });
                    self.profile.user.set(data.user_row);
                    options.success(data);
                    self.initListener();
                }else{
                    options.error();
                }
            }, function(er){
                options.error();
            });
        };
        
        App.prototype.requestGame = function(room_id, methodName, params, done, error, post, host)
        {
            var methodType = (typeof(post) == 'undefined' || !post)?'GET':'POST';
            var self = this;
            var d = new Date();
            var room = this.roomList.findWhere({
                id: room_id.toString()
            });
            if(!room){
                return;
            }
            var server = this.library.serverList.findWhere({
                'code': room.get('server_code')
            });
            if(!server){
                return;
            }
            var url =  server.get('host')+'api/' + methodName;
            $.ajax({
                xhrFields: {
                    withCredentials: true
                },
                cache: false,
                type: methodType,
                dataType: 'json',
                data: params,
                crossDomain: true,
                url: url 
            }).done(function(data){
                if(typeof(data.time_request) != 'undefined'){
                    self.timeDiff =  d.getTime() - Math.ceil(parseFloat(data.time_request)*1000);
                }
                done(data);
            }).fail(function(jqXHR, textStatus){
                self.window('index', 'error', {
                    response: jqXHR.responseJSON,
                    resentBtn: function(){
                        self.request(methodName, params, done, error, post);
                    }
                });
                if (typeof (error) == 'function') {
                    error(jqXHR, textStatus);
                }
            });
        };
        App.prototype.request = function(methodName, params, done, error, post, host)
        {
            var methodType = (typeof(post) == 'undefined' || !post)?'GET':'POST';
            var self = this;
            var d = new Date();
            if(host === undefined || host===null || !host)
                host = (this.profile.getServer())?this.profile.getServer().get('host'):(window.location.origin+'/');
            
            if(this.profile.getServer()){
                params['token'] = this.profile.get('access_token');
            }
            var url =  host+'api/' + methodName;
            $.ajax({
                cache: false,
                type: methodType,
                dataType: 'json',
                data: params,
                crossDomain: true,
                url: url //+ ((url.indexOf('?')==-1)?'?':'&')
            }).done(function(data){
                if(typeof(data.time_request) != 'undefined'){
                    self.timeDiff =  d.getTime() - Math.ceil(parseFloat(data.time_request)*1000);
                }
                done(data);
            }).fail(function(jqXHR, textStatus){
                self.window('index', 'error', {
                    response: jqXHR.responseJSON,
                    resentBtn: function(){
                        self.request(methodName, params, done, error, post);
                    }
                });
                if (typeof (error) == 'function') {
                    error(jqXHR, textStatus);
                }
            });
        };
        
        App.prototype.requestLibrary = function(callback){
            var self = this;
            this.request('library.get', {}, function(data){
                if(data.status){
                    self.library.set(data.lists);
                    callback();
                }
            }, function(er){
            }, null);
        };
        App.prototype.chatAddMessage = function(params, callbacks, loader){
            console.log('chatAddMessage', params);
            if(params.room_id!==undefined && params.room_id!==null && params.room_id){
                this.requestGame(params.room_id, 'chat.say', params, function(data){
                    if(data.status){
                        callbacks.success();
                    }else{
                        callbacks.error(data.error);
                    }
                }, function(er){
                    callbacks.error();
                }, loader);
            }else{
                this.request('chat.say', params, function(data){
                    if(data.status){
                        callbacks.success();
                    }else{
                        callbacks.error(data.error);
                    }
                }, function(er){
                    callbacks.error();
                }, loader);
            }
        };
        
        App.prototype.upgradeServerList = function(callback){
            var self = this;
            this.getServerListRequest(function (data) {
                self.serverList.set(data.list);
                if (!self.serverList.length) {
                    FatalError('ServerList is empty');
                }
                callback();
            });
        };
        
        App.prototype.getServerListRequest = function(callback, errorCallback){
            this.request('index.getServerList', {}, callback, errorCallback);
        };
        
        App.prototype.initProfile = function(callback){
            this.profile.options.fetch();
            if(this.auth.is()){
                var self = this;
                this.profile.fetch({
                    success: function(model, r){
                        console.log('success',r);
                        callback();
                    },
                    error: function(model, r){
                        self.auth.logout();
                        console.log('error',r);
                        callback();
                    },
                });
            }else callback();
        };
        
        App.prototype.initTemplates = function(callback){
            this.request('index.templates', {}, function(data){
                ServiceLocator.set('templates',  new Templates(data.list));
                callback();
            });
        };
        
        App.prototype.initRouter = function(){
            if(this.config.routes === undefined){
                throw new Error('routes not set in config');
            }
            this.router = new (Router(this));
        };
        
        App.prototype.initI18n = function(callback){
            $.i18n.init({
                useCookie: false,
                lng: 'en',
                lng: this.config.langDefault,
                fallbackLng: 'dev',
                fallbackNS: [],
                fallbackToDefaultNS: false,
                fallbackOnNull: true,
                fallbackOnEmpty: false,
                load: 'all',
                preload: [],
                supportedLngs: [],
                lowerCaseLng: false,
                ns: 'translation',
                resGetPath: '/js/locales/__lng__/__ns__.json',
                resSetPath: '/jslocales/__lng__/new.__ns__.json',
                saveMissing: false,
                resStore: false,
                returnObjectTrees: false,
                interpolationPrefix: '__',
                interpolationSuffix: '__',
                postProcess: '',
                parseMissingKey: '',
                debug: false,
                objectTreeKeyHandler: null,
                lngWhitelist: null
            }, function(t) {
                _.template.tsprintf = function(str){
                    var original = $.t(str);
                    var args = Array.prototype.slice.call(arguments);
                    args.splice(0, 1, original);
                    return args.shift().replace(/%s/g, function(){
                        return args.shift();
                    });
                };
                _.template.t = function(str){
                    if(arguments.length>1){
                       return _.template.tsprintf.apply(this, arguments); 
                    }
                    return $.t(str);
                };
                callback();
            });
        };
        
        App.prototype.setLang = function(code, callback){
            $.i18n.setLng(code, function(err, t) {
                callback();
            });
        };
        
        App.prototype.requestLogout = function(callback){
            $.get('/api/user.logout', function(){
                callback();
            }, 'json');
        };
        
        App.prototype.requestCountryList = function(callback){
            $.get('/api/location.getCountryList', function(){
                callback();
            }, 'json');
        };
        
        App.prototype.getFbMe = function(callback) {
            var self = this;
            FB.api('/me/', function(response){
                self.profile.user.set({
                    name: response.name,
                    url: response.picture.data.url,
                    allFrendsObj: response.invitable_friends.data,
                });
                callback();
            }, {fields: 'id,gender,link,location,name,first_name,invitable_friends,picture.width(100).height(100)'});
        };

        App.prototype.inviteFriendsFb = function(strInvList) {
            FB.ui({
                method: 'apprequests',
                message: 'Come play Friend Smash with me!',
                to: strInvList,
            },
            function(){
                console.log("request ok");
            });
        };

        App.prototype.getResultSlotMachine = function(callback){
            var self = this;
            this.request('slotmachine.getResult', {}, function(data) {
                if(typeof data === "object"){
                    self.profile.user.set({
                        slotMachine: data,
                    });
                    callback(data);
                }
            });
        };

        return App;
    }
);
