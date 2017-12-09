define([
], function () {
    return {
        indexAction: function (app, params) {
            var self = this;
            var params = {
                serverSelect: false,
                profile: app.profile,
                langs: app.config.langs,
                playBtn: function (success) {
                    app.play({
                        success: function () {
                            success();
                            app.dispatch('index', 'home');
                        }
                    });
                },
                openWindow: function (name) {
                    app.window('index', name);
                }
            };
            return params;
        },
        homeAction: function (app, params) {
            return {
                user: app.profile.user,
                getRoomsRequest: function (p, options) {
                    app.getRoomsPokerRequest(p, options);
                },
                openWindow: function (name, callback) {
                    app.window('index', name, {
                        callback: callback
                    });
                },
                addMessage: function (message, callbacks) {
                    app.chatAddMessage(message, callbacks);
                }
            };
        },
        errorWindow: function (app, params) {
            return params;
        },
        settingsWindow: function (app) {
            return {
            };
        },
        trainingWindow: function (app) {
            return {
            };
        },
        notificationWindow: function (app) {
            return {
            };
        },
        shopWindow: function (app, params) {
            return {
                shopList: app.profile.shopList,
                shopRequest: function (callbacks) {
                    app.shopListRequest(callbacks);
                },
                buyBtn: function (id, count, callbacks) {
                    app.shopBuyRequest(id, count, callbacks);
                },
                callbacks: params.callback
            };
        },
        helpWindow: function (app) {
            return {
            };
        },
        situpWindow: function (app, params) {
            return {
                joinBtn: params.callback
            };
        },
        diceWindow: function (app) {
            return {
                getResultSlMachine: function (callback) {
                    app.getResultSlotMachine(callback);
                }
            };
        },
        promoWindow: function (app, params) {
            return {
                promo: params.callback.promo,
                userName: params.callback.user.get('name'),
                buyBtn: function (item, count, callbacks) {
                    app.shopBuyRequest(item, count, callbacks);
                }
            };
        },
        inviteWindow: function (app) {
            return {
                inviteFrFb: function (strFriendsId) {
                    app.inviteFriendsFb(strFriendsId);
                }
            };
        }
    };
});