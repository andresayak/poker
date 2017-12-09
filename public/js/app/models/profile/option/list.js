define([
    'backbone',
    'models/collection', 
    'models/profile/option/item', 
    'localstorage'
], function (Backbone, Collection, Item, localstorage) {
    return Collection.extend({
        model: Item,
        localStorage: new Backbone.LocalStorage('profile')
    });
});
