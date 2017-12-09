define([
    'models/model', 
    'models/user/friend/list',
    'models/user/object/list',
    'services/Locator'
], function (Model, FriendList, ObjectList, ServiceLocator) {
    return Model.extend({
        debug: true,
        init: function() {
            this.friendList = new FriendList();
            this.objectList = new ObjectList();
        },
        defaults: {
            id: null,
            code: null,
            name: null,
            url: null,
            host: null,
            exp: null,
            level: null,
        },
        getObjectCount: function(code){
            var item = this.objectList.findWhere({
                code: code
            });
            return (item)?item.get('count'):0;
        },
        updateObject: function(code, count){
            var item = this.objectList.findWhere({
                code: code
            });
            if(item){
                item.set({
                    count: count
                });
            }else{
                this.objectList.add({
                    code: code,
                    count: count
                });
            }
        },
        getLevel: function(){
            var exp = parseInt(this.get('exp'));
            var level = false;
            ServiceLocator.get('library').userLevelList.each(function(model){
                if(parseInt(model.get('exp'))<exp){
                    level = model;
                    return true;
                }
            });
            return level;
        },
        getNextLevel: function(){
            var level = this.getLevel();
            var nextlevel = false;
            ServiceLocator.get('library').userLevelList.each(function(model){
                if(parseInt(level.get('level'))+1 == parseInt(model.get('level'))){
                    nextlevel = model;
                    return true;
                }
            });
            return nextlevel;
        },
        addExp: function(value){
            var exp = parseInt(this.get('exp'))+value;
            this.set('exp', exp);
            var level = this.getLevel();
            if(level){
                this.set('level', level.get('level'));
            }
        }
    });
});