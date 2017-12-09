define([
    'models/profile'
], function (Profile) {
    
    function Service(){
        this.roles = {
            guest: {},
            member: {
                child: 'guest'
            },
            admin: {
                child: 'member'
            }
        };
        this.rules = {
            'default': {
                allow: ['guest'],
                child: ['home']
            },
            home: {
                allow: ['member']
            }
        };
    }
    
    Service.prototype.setRole = function(role){
        this.role = role;
    };
    
    Service.prototype.getRole = function(role){
        if(this.role === undefined){
            throw ErrorException('role not set');
        }
        return this.role;
    };
    
    Service.prototype.isAllow = function(resource){
        for(var ruleName in this.rules){
            if(this.isRule(resource, ruleName)){
                
            }
        }
        this.profile = profile;
    };
    
    Service.prototype.isRule = function(ruleName, resource){
        var rule = this.rules[ruleName];
        var role = this.getRole();
        for(var resourceName in this.re){
            for(var priv in rule.allow){
                if(rule.allow[priv] == role){
                    return true;
                }
            }
        }
    };
    return Service;
});