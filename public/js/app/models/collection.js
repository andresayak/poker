define([
    'backbone', 
], function (Backbone) {
    var Collection = Backbone.Collection.extend({
        constructor: function() {
            Backbone.Collection.apply(this, arguments);
        },
    });
    return Collection;
});
