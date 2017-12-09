define([
    'models/collection', 
    'models/user/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
