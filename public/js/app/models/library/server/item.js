define([
    'models/model',
], function (Model) {
    return Model.extend({
        defaults: {
            code: null,
            socket: null,
            host: null,
        },
    });
});