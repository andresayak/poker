define([
    'models/model'
], function (Model) {
    return Model.extend({
        defaults: {
            translate_code: null,
            id: null,
            price: null,
            price_old: null,
            title: null,
            icon_filename: null,
            discount: null,
            object_rowset: {}
        },
        getObjectCount: function (code) {
            var item;
            var object_rowset = this.get('object_rowset');
            for (var i in object_rowset) {
                if (i == code) {
                    item = object_rowset[i];
                }
            }
            return (item) ? item : 0;
        }
    });
});