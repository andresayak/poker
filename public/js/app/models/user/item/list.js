define([
    'models/collection',  
    'models/user/item/item'
], function (Collection, Item) {
    return Collection.extend({
        model: Item
    });
});
