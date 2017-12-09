define([
    'models/model',
], function (Model) {
    return Model.extend({
        initialize: function() {
        },
        defaults: {
            code: null,
            count: null,
        },
    });
});