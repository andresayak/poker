define([
    'views/view', 'views/helpers/partials/rooms', 'konva', 'models/poker/item',
    'views/helpers/partials/background',
    'views/helpers/chats'
], function (View, RoomsTemplates, Konva, Poker, BackgroundHelper, ChatHelper) {

    var View = View.extend({
        className: 'content-inner',
        events: {
            'click #buyBtn': function () {
                this.params.openWindow('shop');
            },
            'click #fullscreenBtn': 'fullscreenToggle',
            'click #helpBtn': function () {
                this.params.openWindow('help');
            },
            'click #promoBtn': function () {
                this.params.openWindow('promo');
            },
            'click #settingsBtn': function () {
                this.params.openWindow('settings');
            },
            'click #notificationBtn': function () {
                this.params.openWindow('notification');
            },
            'click #playNowBtn': function () {
                this.playNow();
            },
            'click #toLobbyBtn': function () {
                var self = this;
                if (this.poker !== undefined && this.poker) {
                    this.params.app.closePokerRequest(this.poker.get('id'), {success: function () {
                            self.showRooms();
                            self.poker.closeGame();
                            self.chatHelper.leaveRoom();
                        }});
                }
            },
            'click #standUpBtn': function () {
                this.poker.standUpAction();
            },
            'click #trainingBtn': function () {
                this.params.openWindow('training');
            },
            'change .m_checkbox [type="checkbox"]': function (e) {
                var state = $(e.currentTarget);
                if (state.is(':checked')) {
                    state.parent().addClass('checked');
                } else {
                    state.parent().removeClass('checked');
                }
            },
            'click #diceButton': function () {
                this.params.openWindow('dice');
            },
            'click #inviteFriends': function () {
                this.params.openWindow('invite');
            },
            'change #btn-empty': function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    $('#game-poker-public-list').find('.empty').fadeOut();
                } else {
                    $('#game-poker-public-list').find('.empty').fadeIn();
                }
            },
            'change #btn-full': function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    $('#game-poker-public-list').find('.full').fadeOut();
                } else {
                    $('#game-poker-public-list').find('.full').fadeIn();
                }
            },
            'click #btn-call': function () {
                this.poker.callAction();
            },
            'click #btn-check': function () {
                this.poker.checkAction();
            },
            'click #btn-raise': function () {
                this.poker.raiseAction();
            },
            'click #btn-all-in': function () {
                this.poker.allInAction();
            },
            'click #btn-fold': function () {
                this.poker.foldAction();
            }
        },
        initialize: function (params) {
            var self = this;
            this.params = params;
            this.params.controllerName = 'index';
            self.template = self.getTemplate('page-index-home');
            this.chatHelper = new ChatHelper(params);
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
                user: this.params.user,
                countChips: this.countFilter(parseInt(this.params.user.getObjectCount('chip')))
            }));
            var self = this;
            this.pagination('#game-poker-public-list', function (callback, p) {
                self.params.getRoomsRequest(p, callback);
            }, self.renderPokerList);
            _.defer(function () {
                self.background = new BackgroundHelper({
                    el: self.$el.find('#bg-layout-home')[0]
                });
                self.background.render();
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                $('.footer').show();
                self.$el.find('.topPanel').animate({
                    top: 0
                }, 400);
                $("#FbLike").detach().appendTo('#likeContainer').show();

                self.showPromo();

                callback();
            });

            this.$el.find('#chatContainer').html(this.chatHelper.render().el);
            this.listenTo(this.params.user, 'change:level', function (model) {
                var level = model.get('level');
                self.$el.find('#userLevelTitle').text($.t('userlevel.level' + level));
                self.$el.find('#userLevelValue').text(level);
            });
            this.listenTo(this.params.user, 'change:exp', function (model) {
                var nextlevel = model.getNextLevel();
                var level = model.getLevel();
                var exp = parseInt(model.get('exp'));
                var nextup = parseInt(nextlevel.get('exp'));
                var prevup = parseInt(level.get('exp'));
                var diff = exp - prevup;
                var sum = nextup - prevup;
                self.$el.find('#userLevelDiff').text(diff + '/' + sum);
//                self.$el.find('#userLevelProgress').animate({
//                    width: Math.ceil((diff / sum) * 100) + '%'
//                }, 300);
            });
//            setInterval(function () {
//                self.params.user.addExp(100);
//            }, 1000);
            this.listenTo(this.params.user.objectList, 'change:count add', function (model) {
                if (model.get('code') == 'chip') {
                    self.$el.find('#chipsCount').text(self.countFilter(parseInt(model.get('count'))));
                }
            });
            this.listenTo(this.params.app, 'frame', function () {
                self.animate();
            });
            this.listenTo(this.params.app, 'resize', function () {
                self.resize();
            });
            return this;
        },
        resize: function () {
            this.background.resize();
            if (this.poker !== undefined)
                this.poker.resize();
        },
        animate: function () {
            if (this.poker !== undefined)
                this.poker.animate();
        },
        showRooms: function () {
            this.$el.find('.middleContent .container-desc .playBtnsContainer')
                    .animate({
                        bottom: '-1000px'
                    }, 300);
            this.$el.find('.middleContent .container-desc .backContainer')
                    .animate({
                        left: '-1000px'
                    }, 300);
            var self = this;
            setTimeout(function () {
                self.$el.find('.middleContent .container-rooms').show();
                self.$el.find('.middleContent .container-desc').hide();
                self.$el.find('#chatContainer').removeClass('inRoom');
                self.loader.end();
                self.chatHelper.resize();
                $('.playBtnsContainer .checkbox label').tooltip();
            }, 300);
        },
        showDesc: function (data) {
            var self = this;
            console.log('showDesc', data);
            var blind = parseInt(data.blind);
            this.$el.find('#descRoomId').text(data.id);
            this.$el.find('#descRoomBlindes').text(blind + '/' + (2 * blind));
            this.$el.find('.middleContent .container-rooms').hide();
            this.$el.find('.middleContent .container-desc').show();
            this.$el.find('#chatContainer').addClass('inRoom');
            self.chatHelper.resize();
            this.$el.find('.middleContent .container-desc .playBtnsContainer').css('bottom', '-1000px')
                    .animate({
                        bottom: '0px'
                    }, 300);
            this.$el.find('.middleContent .container-desc .backContainer').css('left', '-1000px')
                    .animate({
                        left: '10px'
                    }, 300);
            this.poker = new Poker(data);
            this.poker.createGame({
                user: self.params.user,
                app: self.params.app,
                el: self.$el.find('#tablepoker')[0],
                openWindow: self.params.openWindow
            }, {
                fold: function (id, options, loader) {
                    self.params.app.foldPokerRequest(id, options, loader);
                },
                allin: function (id, options, loader) {
                    self.params.app.allInPokerRequest(id, options, loader);
                },
                raise: function (id, value, options, loader) {
                    self.params.app.raisePokerRequest(id, value, options, loader);
                },
                leave: function (id, options, loader) {
                    self.params.app.leavePokerRequest(id, options, loader);
                },
                join: function (id, position, money, options, error, loader) {
                    self.params.app.joinPokerRequest(id, position, money, options, error, loader);
                },
                call: function (id, options, loader) {
                    self.params.app.callPokerRequest(id, options, loader);
                },
                check: function (id, options, loader) {
                    self.params.app.checkPokerRequest(id, options, loader);
                },
                close: function (id, options, loader) {
                    self.params.app.closePokerRequest(id, options, loader);
                }
            });
            this.poker.render();
            this.chatHelper.joinRoom(this.poker);

            /* Test actions write */
//            this.poker.testJoin();
//            this.poker.testDealer();
//            this.poker.testAddText();
//            this.poker.testaddTextWinMoney();
//            this.poker.testaddCards();
//            this.poker.testaddTimer();
//            this.poker.testaddChips();
//            setTimeout(function () {
//                self.poker.test();
//            }, 4000);

            this.loader.end();
        },
        renderPokerList: function (data, p, force) {
            var self = this;
            if (Object.keys(data.list).length) {
                if (p === 1 && !force) {
                    this.roomsTemplate = new RoomsTemplates({
                        list: data.list,
                        getRoomsRequest: this.params.getRoomsRequest,
                        enterBtn: function (i) {
                            self.params.app.enterPokerRequest(i, {
                                success: function (data) {
                                    self.showDesc(data.game_row);
                                }
                            });

                        }
                    });
                    this.roomsTemplate.render();
                    this.$el.find('#game-poker-public-list').html(this.roomsTemplate.el);
                }
            } else if (p === 1 && !force) {
            }
            return this;
        },
        fullscreenToggle: function () {
            if (!document.fullscreenElement &&
                    !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        },
        playNow: function () {
            var is_play = this.params.user.get('room_id');
            if (is_play) {
                $('#game-poker-public-list tr').removeClass('selected');
                $('#game-poker-public-list').find('tr[data-id="' + is_play + '"]')
                        .addClass('selected');
                this.roomsTemplate.enter();
            } else {
                this.roomsTemplate.refreshList();
                var chips = this.params.user.getObjectCount('chip');
                var room, tmp, ids = [], blindes = [];
                var list = this.roomsTemplate.options.list;
                for (var i in list) {
                    room = list[i];
                    if (room.max_players != room.players) {
                        tmp = ((+room.blind) + room.blind * 2) * 10;
                        ids.push(room.id);
                        blindes.push(tmp);
                    }
                }
                if (blindes.length) {
                    if (chips > blindes[0] / 2) {
                        var id = ids[0];
                        var l = blindes.length;
                        while (l--) {
                            if (blindes[l] <= chips) {
                                id = ids[l];
                                break;
                            }
                        }
                        $('#game-poker-public-list tr').removeClass('selected');
                        $('#game-poker-public-list').find('tr[data-id="' + id + '"]')
                                .addClass('selected');
                        this.roomsTemplate.enter();
                    } else {
                        alert('Недостатньо фішок');
                    }
                }
            }
        },
        showPromo: function () {
            var min = 0, randIndex;
            var list = [];
            var shopList = this.params.app.profile.shopList;
            shopList.each(function (item) {
                if (item.get('type') == 'promo') {
                    list.push(item);
                }
            });
            if (list.length) {
                if (list.length - 1 > min) {
                    randIndex = Math.floor(min + Math.random() * ((list.length - 1) + 1 - min));
                } else {
                    randIndex = min;
                }
                var promo = list[randIndex];
                this.params.openWindow('promo', {
                    promo: promo,
                    user: this.params.user
                });
            }
        }
    });
    return View;

});
