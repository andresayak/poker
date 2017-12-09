define([
    'models/model',
], function (Model) {
    return Model.extend({
        defaults: {
            level: null,
            exp: null,
        },
    });
});