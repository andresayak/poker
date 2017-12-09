define([
    'backbone',
    'models/collection', 
    'models/user/object/item', 
], function (Backbone, Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
