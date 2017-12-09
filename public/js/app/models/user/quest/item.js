define([
    'models/model',
], function (Model) {
    return Model.extend({
        defaults: {
            code: null,
            name: null,
            host: null,
        },
    });
});