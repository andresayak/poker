define([
    'backbone',
    'models/profile/option/list',
    'services/chatsManager',
    'models/user/item',
    'models/profile/shop/list',
], function (Backbone, OptionList, ChatsManager, UserItem, ShopList) {
    return Backbone.Model.extend({
        defaultOptions: {
            openChat: 'public'
        },
        constructor: function () {
            this.chatsManager = new ChatsManager();
            this.options = new OptionList();
            this.user = new UserItem();
            this.shopList = new ShopList();
            Backbone.Model.apply(this, []);
        },
        setFacebookResponse: function (data) {
            this.fb = data;
        },
        getFacebookResponse: function () {
            return this.fb;
        },
        setLang: function (lang, callback) {
            this.lang = lang;
            this.trigger('changeLang', lang, callback);
        },
        getLang: function () {
            return this.lang;
        },
        setServer: function (server) {
            this.server = server;
        },
        getServer: function () {
            return this.server;
        },
        defaults: {
            id: false
        },
        login: function (data) {
            this.set(data);
        },
        logout: function () {
            delete this.access_token;
            delete this.signkey;
        },
        getOption: function (name) {
            var option = this.options.findWhere({'name': name});
            if (!option) {
                var params = {'name': name};
                if (this.defaultOptions[name] !== undefined) {
                    params['value'] = this.defaultOptions[name];
                }
                option = this.profile.options.create(params);
            }
            option.save();
            return option.get('value');
        },
        setOption: function (name, value) {
            var option = this.options.findWhere({'name': 'test'});
            if (!option) {
                option = this.profile.options.create({
                    name: name,
                    value: value
                });
            } else {
                option.set('value', value);
            }
            option.save();
            return this;
        }
    });
});