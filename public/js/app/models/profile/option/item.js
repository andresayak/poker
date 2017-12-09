define([
    'models/model',
], function (Model) {
    return Model.extend({
        defaults: {
            name: null,
            value: null,
            type: null,
        },
    });
});