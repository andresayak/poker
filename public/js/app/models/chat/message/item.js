define([
    'backbone'
], function (Backbone) {
    return Backbone.Model.extend({
        initialize: function() {
        },
        defaults: {
            id: null,
            link: null,
            type: null,
            user_id: null,
            moderator: null,
            color: null,
            message: null,
            time_send: null
        },
    });
});