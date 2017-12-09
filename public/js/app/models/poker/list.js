define([
    'models/collection', 
    'models/poker/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
