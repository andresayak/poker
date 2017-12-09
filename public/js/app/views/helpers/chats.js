define([
    'views/view',
    'services/Locator',
    'jquery.emoticons'
], function (View, ServiceLocator, jemoticons) {
    return View.extend({
        className: 'content-inner',
        noreadCount: {
            public: 0,
            room: 0,
            system: 0
        },
        room: false,
        definition: {smile:{title:"Smile",codes:[":)",":=)",":-)"]},"sad-smile":{title:"Sad Smile",codes:[":(",":=(",":-("]},"big-smile":{title:"Big Smile",codes:[":D",":=D",":-D",":d",":=d",":-d"]},cool:{title:"Cool",codes:["8)","8=)","8-)","B)","B=)","B-)","(cool)"]},wink:{title:"Wink",codes:[":o",":=o",":-o",":O",":=O",":-O"]},crying:{title:"Crying",codes:[";(",";-(",";=("]},sweating:{title:"Sweating",codes:["(sweat)","(:|"]},speechless:{title:"Speechless",codes:[":|",":=|",":-|"]},kiss:{title:"Kiss",codes:[":*",":=*",":-*"]},"tongue-out":{title:"Tongue Out",codes:[":P",":=P",":-P",":p",":=p",":-p"]},blush:{title:"Blush",codes:["(blush)",":$",":-$",":=$",':">']},wondering:{title:"Wondering",codes:[":^)"]},sleepy:{title:"Sleepy",codes:["|-)","I-)","I=)","(snooze)"]},dull:{title:"Dull",codes:["|(","|-(","|=("]},"in-love":{title:"In love",codes:["(inlove)"]},"evil-grin":{title:"Evil grin",codes:["]:)",">:)","(grin)"]},talking:{title:"Talking",codes:["(talk)"]},yawn:{title:"Yawn",codes:["(yawn)","|-()"]},puke:{title:"Puke",codes:["(puke)",":&",":-&",":=&"]},"doh!":{title:"Doh!",codes:["(doh)"]},angry:{title:"Angry",codes:[":@",":-@",":=@","x(","x-(","x=(","X(","X-(","X=("]},"it-wasnt-me":{title:"It wasn't me",codes:["(wasntme)"]},party:{title:"Party!!!",codes:["(party)"]},worried:{title:"Worried",codes:[":S",":-S",":=S",":s",":-s",":=s"]},mmm:{title:"Mmm...",codes:["(mm)"]},nerd:{title:"Nerd",codes:["8-|","B-|","8|","B|","8=|","B=|","(nerd)"]},"lips-sealed":{title:"Lips Sealed",codes:[":x",":-x",":X",":-X",":#",":-#",":=x",":=X",":=#"]},hi:{title:"Hi",codes:["(hi)"]},call:{title:"Call",codes:["(call)"]},devil:{title:"Devil",codes:["(devil)"]},angel:{title:"Angel",codes:["(angel)"]},envy:{title:"Envy",codes:["(envy)"]},wait:{title:"Wait",codes:["(wait)"]},bear:{title:"Bear",codes:["(bear)","(hug)"]},"make-up":{title:"Make-up",codes:["(makeup)","(kate)"]},"covered-laugh":{title:"Covered Laugh",codes:["(giggle)","(chuckle)"]},"clapping-hands":{title:"Clapping Hands",codes:["(clap)"]},thinking:{title:"Thinking",codes:["(think)",":?",":-?",":=?"]},bow:{title:"Bow",codes:["(bow)"]},rofl:{title:"Rolling on the floor laughing",codes:["(rofl)"]},whew:{title:"Whew",codes:["(whew)"]},happy:{title:"Happy",codes:["(happy)"]},smirking:{title:"Smirking",codes:["(smirk)"]},nodding:{title:"Nodding",codes:["(nod)"]},shaking:{title:"Shaking",codes:["(shake)"]},punch:{title:"Punch",codes:["(punch)"]},emo:{title:"Emo",codes:["(emo)"]},yes:{title:"Yes",codes:["(y)","(Y)","(ok)"]},no:{title:"No",codes:["(n)","(N)"]},handshake:{title:"Shaking Hands",codes:["(handshake)"]},skype:{title:"Skype",codes:["(skype)","(ss)"]},heart:{title:"Heart",codes:["(h)","<3","(H)","(l)","(L)"]},"broken-heart":{title:"Broken heart",codes:["(u)","(U)"]},mail:{title:"Mail",codes:["(e)","(m)"]},flower:{title:"Flower",codes:["(f)","(F)"]},rain:{title:"Rain",codes:["(rain)","(london)","(st)"]},sun:{title:"Sun",codes:["(sun)"]},time:{title:"Time",codes:["(o)","(O)","(time)"]},music:{title:"Music",codes:["(music)"]},movie:{title:"Movie",codes:["(~)","(film)","(movie)"]},phone:{title:"Phone",codes:["(mp)","(ph)"]},coffee:{title:"Coffee",codes:["(coffee)"]},pizza:{title:"Pizza",codes:["(pizza)","(pi)"]},cash:{title:"Cash",codes:["(cash)","(mo)","($)"]},muscle:{title:"Muscle",codes:["(muscle)","(flex)"]},cake:{title:"Cake",codes:["(^)","(cake)"]},beer:{title:"Beer",codes:["(beer)"]},drink:{title:"Drink",codes:["(d)","(D)"]},dance:{title:"Dance",codes:["(dance)","\\o/","\\:D/","\\:d/"]},ninja:{title:"Ninja",codes:["(ninja)"]},star:{title:"Star",codes:["(*)"]},mooning:{title:"Mooning",codes:["(mooning)"]},finger:{title:"Finger",codes:["(finger)"]},bandit:{title:"Bandit",codes:["(bandit)"]},drunk:{title:"Drunk",codes:["(drunk)"]},smoking:{title:"Smoking",codes:["(smoking)","(smoke)","(ci)"]},toivo:{title:"Toivo",codes:["(toivo)"]},rock:{title:"Rock",codes:["(rock)"]},headbang:{title:"Headbang",codes:["(headbang)","(banghead)"]},bug:{title:"Bug",codes:["(bug)"]},fubar:{title:"Fubar",codes:["(fubar)"]},poolparty:{title:"Poolparty",codes:["(poolparty)"]},swearing:{title:"Swearing",codes:["(swear)"]},tmi:{title:"TMI",codes:["(tmi)"]},heidy:{title:"Heidy",codes:["(heidy)"]},myspace:{title:"MySpace",codes:["(MySpace)"]},malthe:{title:"Malthe",codes:["(malthe)"]},tauri:{title:"Tauri",codes:["(tauri)"]},priidu:{title:"Priidu",codes:["(priidu)"]}},
        events: {
            'keyup #chats-max .inputLine [name="message"]': function(e){
                if(e.keyCode == 13){
                    this.submit();
                }
            },
            'click .tabs li': function(e){
                var type = $(e.currentTarget).data('type');
                if(type == 'system'){
                    this.$el.find('#chats-max').addClass('showSystemTab');
                }else{
                    this.$el.find('#chats-max').removeClass('showSystemTab');
                }
                this.showTab(type);
            },
            'click #chats-mini': 'showChat',
            'click #chats-max .bn-close': 'hideChat',
            'click .smileIcon': function(){
                if (!$('.popover-smiles').is(':visible')) {
                    this.$el.find('.smileIcon').popover('show');
                }
            }
        },
        showTab: function(tabName)
        {
            this.$el.find('#chats-max .tabs li').removeClass('active');
            $('#chats-max .tabs li[data-type="'+tabName+'"]').addClass('active');
            this.$el.find('#chats-max .chat-list').hide().removeClass('active');
            this.$el.find('#chats-max .chat-list-'+tabName).show().addClass('active');
            this.$el.find('#chats-max .chat-list-'+tabName+' .chat-list .list').css({
                'padding-top': '0px'
            });
            this.noreadCount[tabName] = 0;
            this.$el.find('#chat-tab-'+tabName+'-count').hide().empty;  
            this.resizeChat();
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-chats');
            var self = this;
            $.emoticons.define(self.definition);
            this.listenTo(ServiceLocator.get('listener'), 'chat.public poker.room poker.system chat.private chat.system', function(data) {
                for (var i in data) {
                    if(data[i].type == 'poker'){
                        self.renderPokerMessage(data[i], data[i].type);
                    }else{
                        self.renderMessage(data[i], data[i].type);
                    }
                }
            });
        },
        addCount: function(type){
            var el = this.$el.find('#chats-max');
            if(!el.find('.chat-list-'+type).hasClass('active')){
                this.noreadCount[type]++;
                el.find('#chat-tab-'+type+'-count').show().text('+' + this.noreadCount[type]);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
            }
            return this;
        },
        inputMulty: function(){
            var el = this.$el.find('#chats-max .input-block .inputElement');
            var val = $(el).val();
            var res = val.split("\n");
            if (res.length > 1) {
                this.$el.find('#chats-max .input-block').addClass('input-block-multy');
            } else {
                this.$el.find('#chats-max .input-block').removeClass('input-block-multy');
            }
        },
        submit: function(){
            var el = this.$el.find('#chats-max .inputLine [name="message"]');
            var message = el.val();
            var self = this;
            if (message == '') {
                this.inputError('error.Input is empty');
            } else {
                var type = this.$el.find('#chats-max .tabs li.active').attr('data-type');
                el.addClass('send-message-progress');
                this.params.addMessage({
                    message: message,
                    type: type,
                    room_id: ((self.room)?self.room.get('id'):null)
                }, {
                    success: function(){
                        el.focus();
                        self.loader.end();
                        el.removeClass('send-message-progress');
                    },
                    error: function(error){
                        
                        //self.inputError(error);
                    },
                    done: function(){
                        
                        
                    }
                });
                el.val('');
            }
        },
        inputError: function(error){
            var errorMessage = '';
            if(!error){
                errorMessage = $.t('error.Server error');
            }else if(typeof(error) == 'object'){
                for(var i in error){
                    for(var error_code in error[i]){
                        errorMessage+= "<div>" + $.t('error.'+error_code)+ "</div>";
                    }
                }
            }
            this.$el.find('#chats-max .input-block').popover({
                placement: 'top',
                html: true,
                trigger: 'manual',
                title: $.t('main.Error'),
                content: errorMessage
            }).popover('show');
        },
        render: function () {
            var self = this;
            
            this.$el.html(this.template({
                t: $.t,
                user: this.params.user,
                chatsManager: this.params.app.profile.chatsManager,
                escapeMessage: function(message){
                    return $.emoticons.replace(_.template.escapeHtml(message));
                }
            }));
            this.$el.on('click', '.popover-smiles .smile-item', function(){
                var name = $(this).data('code');
                self.takeSmile(name);
            });
            _.defer(function () {
                self.$el.find('.smileIcon').popover({
                    'html': true,
                    content: '<div class="popover-smiles">'+$.emoticons.toString()+'</div>',
                    container: self.el,
                    placement: 'top',
                    trigger: 'focus'
                });
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
            });
            this.listenTo(this.params.app, 'resize', function() {
                self.resize();
            });
            return this;
        },
        renderMessage: function(data, type){
            var self = this;
            if(type == 'room' && (data.room_id === undefined || !this.room || data.room_id!= this.room.get('id'))){
                return this;
            }
            this.addCount(type);
            var user_id = self.params.user.get('id');
            var message = $.emoticons
                .replace(_.template.escapeHtml(data.message));
            var el = self.$el.find('#chats-max .chat-list-'+type+' .scroll, #chats-mini .chat-list .scroll');
            $.each(el, function(){
                if(data.html!==undefined){
                    $(this).append('<div class="item item-type-'+type+'">'
                            + data.html
                        + '</div>');
                }else{
                    var username = _.template.escapeHtml(data.social_name);
                    var date = _.template.date(data.time_send, 'min');
                    var user = (data.user_id)?'<a href="'+data.social_link+'" target="_blank" class="username">'+username+'</a>':'';
                    $(this).append('<div class="item '+((data.user_id == user_id)?'item-my ':'')+'item-type-'+type+((type=='room')?' item-room item-room-'+data.room_id:'')+'" >'
                            + '<div class="innerItem">'
                                + '<span class="time">'+date+'</span>'
                                + ((data.user_id)?'<span data-user_id="'+data.user_id+'">'+user+': </span>':'')
                                + '<span class="message">'+message+'</span>'
                            + '</div>'
                        + '</div>');
                }
            });
            this.resizeChat(500);
        },
        renderPokerMessage: function(data){
            var self = this;
            if((data.room_id === undefined || !this.room || data.room_id!= this.room.get('id'))){
                return this;
            }
            var messageData = false;
            if(data.name == 'start'){
                this.renderMessage({
                    html: '<hr/>',
                }, 'system');
                messageData = {
                    message: _.template.t('game.Start_game'),
                    time_send: this.params.app.getTimeNow(),
                    user_id: false,
                };
            }
            if(data.name == 'leave'){
                messageData = {
                    message: _.template.t('game.Leave', data.leavetype),
                    time_send: this.params.app.getTimeNow(),
                    user_id: false,
                };
            }
            if(data.name == 'stepEnd'){
                messageData = {
                    message: _.template.t('game.Round_end', self.countFilter(data.bank)),
                    time_send: this.params.app.getTimeNow(),
                    user_id: false,
                };
            }
            if(data.name == 'join'){
                messageData = {
                    message: _.template.t('game.Join', self.countFilter(data.user.money)),
                    time_send: this.params.app.getTimeNow(),
                    user_id: data.user.id,
                    social_id: data.user.social_id,
                    social_link: data.user.social_link,
                    social_name: data.user.social_name
                };
            }
            if(data.name == 'call'){
                messageData = {
                    message: _.template.t('game.Call', self.countFilter(data.value)),
                    time_send: this.params.app.getTimeNow(),
                    user_id: data.user.id,
                    social_id: data.user.social_id,
                    social_link: data.user.social_link,
                    social_name: data.user.social_name
                };
            }
            if(data.name == 'raise'){
                messageData = {
                    message: _.template.t('game.Raise to ', self.countFilter(data.value)),
                    time_send: this.params.app.getTimeNow(),
                    user_id: data.user.id,
                    social_id: data.user.social_id,
                    social_link: data.user.social_link,
                    social_name: data.user.social_name
                };
            }
            if(data.name == 'fold'){
                messageData = {
                    message: _.template.t('game.Fold'),
                    time_send: this.params.app.getTimeNow(),
                    user_id: data.user.id,
                    social_id: data.user.social_id,
                    social_link: data.user.social_link,
                    social_name: data.user.social_name
                };
            }
            if(data.name == 'check'){
                messageData = {
                    message: _.template.t('game.Check'),
                    time_send: this.params.app.getTimeNow(),
                    user_id: data.user.id,
                    social_id: data.user.social_id,
                    social_link: data.user.social_link,
                    social_name: data.user.social_name
                };
            }
            if(data.name == 'win'){
                for(var i in data.wins){
                    for(var position in data.wins[i].positions){
                        var item = data.wins[i].positions[position];
                        this.renderMessage({
                            message: _.template.t('game.Have_ranking', item.ranking, self.countFilter(item.money)),
                            time_send: this.params.app.getTimeNow(),
                            user_id: item.user.id,
                            social_id: item.user.social_id,
                            social_link: item.user.social_link,
                            social_name: item.user.social_name
                        }, 'system');
                    }
                }
            }
            if(messageData)
                this.renderMessage(messageData, 'system');
        },
        resize: function(){
            this.resizeChat();
        },
        resizeChat: function(duration){
            duration = (duration === undefined)?false:duration;
            var scrollBlock = this.$el.find('.chats.active .chat-list.active .scroll');
            if(!$(scrollBlock).size()){
                throw new Error('scroll not init');
                return this;
            }
            var outerHeight = 0;
            $(scrollBlock).css({'padding-top':0});
            var scrollHeight = $(scrollBlock)[0].scrollHeight;
            scrollBlock.find('.item').each(function() {
                outerHeight += $(this).outerHeight();
            });
            if(outerHeight == 0){
                return this;
            }
            if(scrollHeight>outerHeight){
                $(scrollBlock).css({
                    'padding-top': (scrollHeight-outerHeight)+'px'
                });
            }else{
                if(scrollHeight!=$(scrollBlock)[0].scrollTop){
                    if(duration){
                        $(scrollBlock).clearQueue().animate({
                            scrollTop: scrollHeight
                        }, duration);
                    }else{
                        $(scrollBlock).scrollTop(scrollHeight);
                    }
                }
            }
        },
        showChat: function(){
            var self = this;
            self.$el.find('#chats-max').show().addClass('active');
            self.$el.find('#chats-mini').hide().removeClass('active');
            var scrollBlock = this.$el.find('#chats-max .scroll');
            $(scrollBlock).scrollTop($(scrollBlock)[0].scrollHeight);
            this.$el.find('#chats-max [name="message"]').focus();
            this.resizeChat();
        },
        hideChat: function(){
            var self = this;
            self.$el.find('#chats-max').hide().removeClass('active');
            self.$el.find('#chats-mini').show().addClass('active');
            this.resizeChat();
        },
        joinRoom: function(room){
            var self = this;
            self.$el.find('#chatRoomTab').show();
            self.$el.find('#chatRoomId').text(room.get('id')).show();
            this.showTab('room');
            this.room = room;
            return this;
        },
        leaveRoom: function(){
            var self = this;
            self.$el.find('#chatRoomTab').hide();
            self.$el.find('#chatRoomId').hide();
            this.noreadCount['room'] = 0;
            this.$el.find('#chats-max .chat-tab-room-count').hide().text('0');
            this.$el.find('.chat-list .item-room').remove();
            this.showTab('public');
            this.room = false;
        },
        takeSmile: function(name){
            var el = this.$el.find('#chats-max .inputLineInner .form-control');
            var value = el.val();
            el.val(value+' '+name).focus();
            this.$el.find('.smileIcon').popover('hide');
        }
    });
});

