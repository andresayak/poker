define([
    'backbone', 'services/Locator'
], function (Backbone, ServiceLocator) {
    var Model = Backbone.Model.extend({
        debug: false,
        _super: function(funcName){
            return this.constructor.prototype[funcName].apply(this, _.rest(arguments));
        },
        constructor: function() {
            this.init();
            Backbone.Model.apply(this, arguments);
        },
        init: function(){
            
        },
        sl: function(name){
            return ServiceLocator.get(name);
        },
        set: function () {
            var attrs = this.attributes;
            var self = this;
            var f = function(name, value){
                if(self[name] !== undefined 
                    && self[name] instanceof Backbone.Collection
                ){
                    _.each(value, function(item){
                        self[name].add(item);
                    });
                    return;
                }
                Backbone.Model.prototype.set.apply(self, [name, value]);
            };
            if(arguments.length == 2 && typeof(arguments[0]) == 'string'){
                f.apply(self, [arguments[0], arguments[1]]);
                return this;
            }else if(arguments.length){
                _.each(arguments[0], function(value, name){
                    f.apply(self, [name, value]);
                });
            }
            return this;
        },
    });
    return Model;
});