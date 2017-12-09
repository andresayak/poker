define([
    'models/collection', 
    'models/user/gift/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
