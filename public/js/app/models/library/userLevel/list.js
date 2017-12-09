define([
    'backbone',
    'models/collection', 
    'models/library/userLevel/item', 
], function (Backbone, Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
