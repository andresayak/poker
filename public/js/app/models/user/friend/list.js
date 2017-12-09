define([
    'models/collection', 
    'models/user/friend/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
