define([
    'backbone', 
    'models/chat/message/item'
], function (Backbone, Item) {
    var Collection = Backbone.Collection.extend({
        model: Item,
        comparator: function(message){
            return parseInt(message.get('time_send'));
        }
    });
    return Collection;
});
