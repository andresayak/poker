define([
], function () {
    
    function Service(profile){
        this.profile = profile;
        //this.token = $.cookie('token');
        this.token = null;
    };
    
    Service.prototype.is = function(){
        return (this.token!== undefined && this.token!== null && this.token!==false);
    };
    
    Service.prototype.logout = function(){
        this.token = null;
        this.profile.set('id', false);
        //$.removeCookie('token');
    };
    
    Service.prototype.login = function(token, data){
        this.token = token;
        this.profile.set(data);
        //$.cookie('token', token);
    };
    return Service;
});