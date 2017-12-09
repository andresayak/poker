define([
    'backbone',
    'models/chat/message/list'
], function (Backbone, MessagesCollection) {
    
    function Service(){
        _.extend(this, Backbone.Events);
        this.messages = {
            public: new MessagesCollection(),
            system: new MessagesCollection(),
            room: new MessagesCollection()
        };
    }
    
    Service.prototype.setListByTypes = function(arrays)
    {
        for(var type in arrays){
            this.setListByType(arrays[type], type);
        }
    };
    
    Service.prototype.setListByType = function(list, type)
    {
        if (this.messages[type] === undefined) {
            this.messages[type] = new MessagesCollection();
        }
        this.messages[type].set(list);
    };
    
    Service.prototype.getLastMessages = function(limit, callback)
    {
        var all = new MessagesCollection();
        for(var type in this.messages){
            all.add(this.messages[type].toJSON(), {silent : true});
            
        }
        var sorted = all.sort().last(limit);
        _.each(sorted, function(model){
            callback(model);
        });
    };
    
    Service.prototype.getListByType = function(type)
    {
        if(this.messages[type] === undefined){
            this.messages[type] = new MessagesCollection();
        }
        return this.messages[type];
    };
    
    return Service;
});