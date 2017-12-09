define([
    'models/collection',
    'models/user/quest/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item,
    });
});
