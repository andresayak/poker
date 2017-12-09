define([
    'models/model', 'models/library/userLevel/list', 
    'models/library/server/list'
], function (Model, UserLevelList, ServerList) {
    return Model.extend({
        defaultOptions: {
            openChat: 'public'
        },
        init: function() {
            this.userLevelList = new UserLevelList();
            this.serverList = new ServerList();
        }
    });
});