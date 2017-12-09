define([], function () {
    
    function Service(list){
        this.list = list;
    }
    
    Service.prototype.get = function(name){
        return this.list[name];
    };
    
    return Service;
});