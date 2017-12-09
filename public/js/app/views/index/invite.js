define(['views/view', 'konva'], function (View, Konva) {
    return View.extend({
        id: 'modal-invite',
        events: {
            'click #selectAllInvite': 'selectAll',
            'click #sendRequestInvite': 'sendRequestInvite',
            'click input[type=checkbox],#selectAllInvite': 'clickInput',
            'keyup #searchInput': 'search',
            'click #searchButton': 'search',
        },
        initialize: function (params) {
            this.params = params;
            this.counter = 0;
            this.selectAllParam = false;
            this.user = this.params.app.profile.user;
            this.template = this.getTemplate('window-invite');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
            }));
            var self = this;
            _.defer(function () {
                self.friendsInviteAll = self.getFriendsNameFb();
                self.addUser(0, self.friendsInviteAll);

                // var element = $('#nameUser');
                // element.scroll(function(){
                //     var posTop = Math.ceil($(element).scrollTop());
                //     console.log(posTop +" + " + element.height());
                //     if(posTop % (261) === 0) self.addUser(12, self.friendsInviteAll);
                // });
                // console.log(Object.keys(self.getFriendsNameFb()));

                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.loader.end();
                callback();
            });
            this.listenTo(this.params.app, 'resize', function() {
                self.resize();
            });
            this.$el.find('.modal').modal('show');
            this.$el.find('.modal').on('hidden.bs.modal', function (e) {
                self.remove();
            });
            return this;
        },
        resize: function(){
        },
        submit: function(e){
            e.preventDefault();
            return false;
        },
        addUser: function(count,userList){
            $(function() {
                var el0 = $('#userCol0');
                var el1 = $('#userCol1');
                var countList = Object.keys(userList).length;
                if(countList<count || count==0) count = countList;
                count = Math.ceil(count);

                for (i = 0, len = count; i < count; ++i) {
                    if(i%2==0) el0.append("<div class='user-box'> <table> <tr> <td> <label> <input type='checkbox' class='invite-check' value='"+userList[i]['id']+"'> <span></span> </td> <td> <img src='"+userList[i]['picture']+"' class='img-rounded' width='50' height='50'> </td> <td><b>"+userList[i]['name']+"</b></td> </tr> </table>");
                    if(i%2!=0) el1.append("<div class='user-box'> <table> <tr> <td> <label> <input type='checkbox' class='invite-check' value='"+userList[i]['id']+"'> <span></span> </td> <td> <img src='"+userList[i]['picture']+"' class='img-rounded' width='50' height='50'> </td> <td><b>"+userList[i]['name']+"</b></td> </tr> </table>");

                };
            });
        },
        getFriendsNameFb: function(){
            var self = this;
            var allFriends = self.user.get('allFrendsObj');
            // console.log(allFriends);
            var friendsList = {};
            for (var item in allFriends) {
                friendsList[item] = {
                    'id': allFriends[item]['id'],
                    'name': allFriends[item]['name'],
                    'picture': allFriends[item]['picture']['data']['url'],
                };
            }

            return friendsList;
        },
        selectAll: function(){
            ++this.counter;
            if(this.counter%2!=0) {
                $('label input').prop('checked', true);
                this.selectAllParam = true;
            }
            if(this.counter%2==0) {
                $('label input').prop('checked', false);
                this.selectAllParam = false;
            }
        },
        clickInput: function(){
            var length = $("input[class='invite-check']:checked").length;
            if(length!=0) $("#sendRequestInvite").prop("disabled",false);
            if(length==0) $("#sendRequestInvite").prop("disabled",true);
        },
        sendRequestInvite: function(){
            var self = this;
            var sendRequestAll = function(allSelect, friends){
                var s = '';
                var count = (Object.keys(friends).length) - 1;

                if(allSelect){
                    for (var i in friends) {
                        s+=friends[i]['id']+",";
                        if(i==count || i==25) {
                            console.log("send request when all select");
                            self.params.inviteFrFb(s);
                            s = '';
                        };
                    };
                };
            };

            var sendRequestChecked = function(allSelect, friends){
                if(!allSelect){
                    var s = '';
                    var selectedItems = [];
                    $("input[class='invite-check']:checked").each(function() {selectedItems.push($(this).val())});
                    var count = (selectedItems.length-1);

                    for (var i in selectedItems) {
                        s+=selectedItems[i]+",";
                        if(i==count || i==25) {
                            console.log("send request when selected items");
                            self.params.inviteFrFb(s);
                            s = '';
                        };
                    };
                };
            };

            sendRequestAll(this.selectAllParam, this.friendsInviteAll);
            sendRequestChecked(this.selectAllParam, this.friendsInviteAll);
        },
        search: function(){
            var userList = this.friendsInviteAll;
            var s = $("#searchInput").val();
            var el0 = $('#userCol0');
            var el1 = $('#userCol1');
            // console.log(s);
            $(".user-box").remove();
            var c = 0;
            for (var i in userList) {
                var n = userList[i]['name'].search(s);
                if(n!=-1) {
                    // console.log(this.friendsInviteAll[i]['name']);
                    if(c%2==0) el0.append("<div class='user-box'> <table> <tr> <td> <label> <input type='checkbox' class='invite-check' value='"+userList[i]['id']+"'> <span></span> </td> <td> <img src='"+userList[i]['picture']+"' class='img-rounded' width='50' height='50'> </td> <td><b>"+userList[i]['name']+"</b></td> </tr> </table>");
                    if(c%2!=0) el1.append("<div class='user-box'> <table> <tr> <td> <label> <input type='checkbox' class='invite-check' value='"+userList[i]['id']+"'> <span></span> </td> <td> <img src='"+userList[i]['picture']+"' class='img-rounded' width='50' height='50'> </td> <td><b>"+userList[i]['name']+"</b></td> </tr> </table>");
                    ++c;
                }
            }; 
        },

    });
});