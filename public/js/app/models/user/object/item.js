define([
    'models/model',
], function (Model) {
    return Model.extend({
        defaults: {
            code: null,
            count: null,
        },
    });
});