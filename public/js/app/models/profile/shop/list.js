define([
    'backbone',
    'models/collection', 
    'models/profile/shop/item', 
], function (Backbone, Collection, Item) {
    return Collection.extend({
        model: Item,
        initStatus: false
    });
});
