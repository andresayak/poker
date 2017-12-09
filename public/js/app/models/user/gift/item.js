define([
    'models/model',
], function (Model) {
    return Model.extend({
        initialize: function() {
        },
        defaults: {
            code: null,
            name: null,
            host: null,
        },
    });
});