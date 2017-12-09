define([
    'backbone',
    'models/collection', 
    'models/library/server/item', 
], function (Backbone, Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
