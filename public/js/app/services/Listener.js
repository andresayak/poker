define([
    'backbone'
], function(Backbone) {
    function Helper(){
        _.extend(this, Backbone.Events);
        this.listeners = {};
        this.connections = {};
    };
    
    Helper.prototype.init = function(address, callback){
        this.addConnection('chat', address, callback);
    };
    
    Helper.prototype.addConnection = function(name, address, callback) {
        var self = this;
        window.WebSocket = window.WebSocket || window.MozWebSocket;
        if(this.connections[name]!==undefined){
            this.connections[name].close();
            delete this.connections[name];
        }
        this.connections[name] = new WebSocket(address);
        this.connections[name].onerror = function (error) {
        };
        this.connections[name].onclose = function (error) {
            if(error.code != 1000){
                setTimeout(function(){
                    self.addConnection(name, address, callback);
                }, 5000);
            }
        };
        
        $(window).on('beforeunload', function(){
            if(self.connections[name] !== undefined && typeof(self.connections[name].close) == 'function')
                self.connections[name].close();
        });
        this.connections[name].onmessage = function (message) {
            var data = JSON.parse(message.data);
            console.info('socket['+name+']', data);
            self.trigger(name+'.'+data.channel, data.messages);
        };
        this.connections[name].onopen = function (event) {
            if(typeof(callback) == 'function')
                callback();
        };
    };
    
    Helper.prototype.closeConnection = function(name) {
        this.connections[name].close();
        delete this.connections[name];
        console.info('closeConnection', name);
    };
    
    Helper.prototype.create = function(name, callback) {
        this.on(name, 'update', callback);
        
    };
    Helper.prototype.reset = function(){
        for(var i in this.connections){
            this.connections[i].close();
        }
        this.connections = {};
    };
    return Helper;
});