define([
    'models/model', 'views/helpers/partials/table',
    'models/poker/game', 'services/Locator',
], function (Model, TableHelper, Game, ServiceLocator) {
    return Model.extend({
        init: function () {

        },
        defaults: {
            id: null,
            max_players: null,
            blind: null,
            players: null,
            time_update: null,
            time_start: null,
            user_id: null,
            buyin_max: null,
            buyin_min: null,
            data: {},
            listeners: [],
            seats: [],
            server_code: null
        },
        testJoin: function () {
            var self = this;
            this.game.join(this.game.createPlayer({
                id: 100,
                username: 'Test 1',
                link: 'http://google.com',
                img: 'https://scontent.xx.fbcdn.net/hprofile-xtp1/v/t1.0-1/c0.25.200.190/p200x200/10628306_1393589364264738_8619683075405033989_n.jpg?oh=b30e3f9226f966d117acac30f4b42059&oe=579DEDD1',
                money: 1000,
                fold: false,
                allin: false,
                play: false
            }), 2);

            setTimeout(function () {
                self.game.join(self.game.createPlayer({
                    id: 102,
                    username: 'Test 2',
                    link: 'http://google.com',
                    img: 'https://scontent.xx.fbcdn.net/hprofile-xtp1/v/t1.0-1/c0.25.200.190/p200x200/10628306_1393589364264738_8619683075405033989_n.jpg?oh=b30e3f9226f966d117acac30f4b42059&oe=579DEDD1',
                    fold: false,
                    allin: false,
                    play: false,
                    money: 1200
                }), 4);
            }, 4000);
        },
        test: function () {
            var self = this;
            self.view.showWelcomeAlert();
            this.game.join(this.game.createPlayer({
                id: 100,
                social_name: 'Test 1',
                social_link: 'http://google.com',
                social_id: 1585844448372561,
                money: 2000,
                fold: false,
                allin: false,
                play: false
            }), 7);

            this.game.join(this.game.createPlayer({
                id: 101,
                social_name: 'Test 2',
                social_link: 'http://google.com',
                social_id: 1585844448372561,
                money: 3000,
                fold: false,
                allin: false,
                play: false
            }), 9);

            this.game.start({
                dealerPosition: 7,
                users: {
                    7: {play: true},
                    9: {play: true}
                },
                blindes: [
                    {
                        user_id: 100,
                        position: 7,
                        value: 5
                    },
                    {
                        user_id: 101,
                        position: 9,
                        value: 10
                    }
                ]
            });
            this.game.setPublicCards({
                cards: [
                    {suit: 'hearts', value: 4},
                    {suit: 'hearts', value: 2},
                    {suit: 'hearts', value: 5},
                    {suit: 'hearts', value: 3},
                    {suit: 'hearts', value: 6}
                ]
            });
            this.game.setPrivateCards({
                cards: [
                    {suit: 'hearts', value: 4},
                    {suit: 'hearts', value: 5}
                ]
            });
            setTimeout(function () {
                self.game.raise(self.game.users[7], 100);
            }, 4000);
            setTimeout(function () {
                self.game.allin(self.game.users[9], 1000);
            }, 4000);
            setTimeout(function () {
                self.view.showWin({
                    0: {
                        bank: 1998,
                        positions: {
                            4: {
                                money: 1998,
                                private: [2],
                                public: [2, 4],
                                ranking: 'one_pair',
                                weight: 38415
                            }
                        }
                    },
                    1: {
                        bank: 800,
                        positions: {
                            2: {
                                money: 400,
                                private: [1, 2],
                                public: [3, 4],
                                ranking: 'royal_flush',
                                weight: 38415
                            }
                        }
                    }
                }, {
                    2: [
                        {
                            suit: "spades",
                            value: "ace",
                            weight: 14
                        },
                        {
                            suit: "hearts",
                            value: "queen",
                            weight: 12
                        }
                    ],
                    4: [
                        {
                            suit: "clubs",
                            value: 6,
                            weight: 6
                        },
                        {
                            suit: "hearts",
                            value: 4,
                            weight: 4
                        }
                    ]
                }, self.game.users);
            }, 4000);
        },
        testDealer: function () {
            var self = this;
            setInterval(function () {
                var position = Math.floor(1 + Math.random() * (10 + 1 - 1));
                self.view.changeDealerPosition(position);
            }, 1000);
        },
        testAddText: function () {
            var self = this;
            var types = ['win', 'fold', 'call', 'check', 'raise', 'allin', 'timeout'];
            setTimeout(function () {
                var position = Math.floor(1 + Math.random() * (10 + 1 - 1));
                var type = types[Math.floor(Math.random() * (types.length))];
                self.view.addText(10, type);
            }, 10000);
        },
        testaddTextWinMoney: function () {
            var self = this;
            var money = ['100', '300', '4000', '10000', '14555', '20', '3456'];
            setTimeout(function () {
                var position = Math.floor(1 + Math.random() * (10 + 1 - 1));
                var sum = money[Math.floor(Math.random() * (money.length))];
                self.view.addTextWinMoney(10, sum);
            }, 10000);
        },
        testaddCards: function () {
            var self = this;
            setTimeout(function () {
                var cardsPublic = [
                    {suit: 'hearts', value: 4},
                    {suit: 'hearts', value: 2},
                    {suit: 'hearts', value: 5},
                    {suit: 'hearts', value: 3},
                    {suit: 'hearts', value: 6}
                ];
                self.view.addCards(cardsPublic, null);
                var cards = [
                    {suit: 'hearts', value: 4},
                    {suit: 'hearts', value: 5}
                ];
                self.view.addCards(cards, 6);
            }, 4000);
        },
        testaddTimer: function () {
            var self = this;
            setInterval(function () {
                var position = Math.floor(1 + Math.random() * (10 + 1 - 1));
                self.view.timerSeat(20000, position, -11100, function () {

                });
            }, 2000);
            self.view.timerRemove();
        },
        testaddChips: function () {
            for (var i in this.view.chipsPos) {
                var sum = Math.floor(Math.random() * (2999 - 2001 + 1)) + 2001;
                this.view.addChips(i, sum);
                this.view.layer.draw();
            }
        },
        /*
         *
         *
         * game
         *
         *
         */
        closeGame: function () {
            this.stopListening();
            this.off();
        },
        createGame: function (params, requests) {
            this.params = params;
            this.requests = requests;
            this.game = new Game(this, function () {
                return params.app.getTimeNow();
            });
            var userId = params.user.get('id');
            this.game.my_id = userId;
            this.view = new TableHelper(params);
            var self = this;

            $('#toLobbyBtn').addClass('btn-tolobby-' + userId);
            $('#standUpBtn').addClass('btn-standup-' + userId);

            this.listenTo(ServiceLocator.get('listener'), 'poker.room poker.private chat.private', function (data) {
                for (var i in data) {
                    self.game.proxy(data[i]);
                }
            });
            this.on('join', function (position) {
                var user = self.game.users[position];
                self.view.beforeJoin(position, user.info.id, user.balance);
                self.view.join(user, position);
            });
            this.on('leave', function (params) {
                var userPos = self.game.getPostionByPlayer(self.params.user.get('id'));
                self.view.leave(params.position, params.leavetype, params.money, userPos, function (position) {
                    self.seatAction(position);
                });
            });
            this.on('timeout', function (position) {
                self.view.addText(position, 'timeout', function () {
                });
            });
            this.on('bet', function (params) {
                self.view.addBet(params.player, params.money, params.value, params.position, params.type);
                self.view.addText(params.position, params.type);
            });
            this.on('fold', function (params) {
                self.view.resetHand(params.position);
                self.view.addText(params.position, 'fold');
            });
            this.on('game.start', function () {
                if (self.game.getPostionByPlayer(self.params.user.get('id'))) {
                    self.view.hideWelcomeAlert();
                    self.view.showButtons();
                }
                self.view.disableCheckboxes();
            });
            this.on('balance.change', function (params) {
                self.view.updatePlayerBalance(params.position, params.player.balance);
            });
            this.on('game.end', function (params) {
                self.view.showWin(params.wins, params.cards, self.game.users);
                self.view.hideButtons();
            });
            this.on('addCards', function (params) {
                self.view.addCards(params.cards, params.position);
            });
            this.on('timer.start', function (params) {
                var player = self.game.getTurnPlayer();
                var callValue = self.game.getCallValue(player.info['id'], self.game.step);
                var callSum = self.game.getCallSumByPlayer(player.info['id'], self.game.step);
                var isAllinEnd = self.game.isAllinEnd();
                var id = self.game.options.id;
                var requests = {
                    call: self.requests.call,
                    check: self.requests.check,
                    fold: self.requests.fold
                };

                self.view.timerSeat(params.time, params.position, params.timeDiff);
                self.view.showActiveButtons(player, callValue, callSum, isAllinEnd, requests, id);
            });
            this.on('timer.end', function (params) {
                self.view.timerRemove();
            });
            this.on('dealer.change', function (position) {
                self.view.changeDealerPosition(position);
            });
            this.on('bank.update', function (money) {
                self.view.updateBank(money);
            });
        },
        render: function () {
            var self = this;
            this.view.render();
            // layer event
            this.view.layer.on('click', function (e) {
                var target = e.target;
                if (target.hasName('empty')) {
                    var id = target.attrs.id;
                    var position = id.split('-')[1];
                    self.seatAction(position);
                }
            });
            // start
            this.start();
        },
        start: function () {
            for (var position in this.game.users) {
                var player = this.game.users[position];
                var me = ((player.info['id'] == this.game.my_id) ? true : false);
                this.view.beforeJoin(position, me);
                this.view.join(player, position);
                if (!this.game.isGameEnd && player.cards !== undefined) {
                    this.view.addCards(player.cards, position);
                }
                if (me) {
                    $('.btn-tolobby-' + this.game.my_id).addClass('disabled');
                    $('.btn-standup-' + this.game.my_id).fadeIn();
                    if (this.game.isGameEnd || !player.play || player.fold) {
                        this.view.showWelcomeAlert();
                    }
                    this.view.myPos = position;
                }
            }
            if (!this.game.isGameEnd) {
                var moneyInDesk = 0;
                if (this.game.bank.steps[this.game.step] !== undefined) {
                    for (var user_id in this.game.bank.steps[this.game.step]) {
                        var count = this.game.bank.steps[this.game.step][user_id];
                        if (count !== undefined) {
                            var position = this.game.getPostionByPlayer(user_id);
                            this.view.addBet(this.game.users[position], count, false, position);
                        }
                        moneyInDesk += count;
                    }
                }
                this.view.updateBank(this.game.bank.main.balance - moneyInDesk, null, false);
                this.view.addCards(this.game.publicCards);
                var position = this.game.getPostionByPlayer(this.params.user.get('id'));
                if (position) {
                    this.view.hideWelcomeAlert();
                    this.view.showActiveButtons();
                }
                this.game.createTimeoutTime();
            }
        },
        callAction: function () {
            var player = this.game.getTurnPlayer();
            if (player.info['id'] == this.game.my_id) {
                this.requests.call(this.game.options.id, {
                    success: function (data) {
                        //console.info('callAction', data);
                    },
                    error: function (data) {
                        console.error('callAction', data);
                    }
                });
            }
        },
        checkAction: function () {
            var player = this.game.getTurnPlayer();
            if (player.info['id'] == this.game.my_id) {
                this.requests.check(this.game.options.id, {
                    success: function (data) {
                        //console.info('checkAction', data);
                    },
                    error: function (data) {
                        console.error('checkAction', data);
                    }
                });
            }
        },
        foldAction: function () {
            this.requests.fold(this.game.options.id, {
                success: function (data) {
                    //console.info('foldAction', data);
                },
                error: function (data) {
                    console.error('foldAction', data);
                }
            });
        },
        allInAction: function () {
            this.requests.allin(this.game.options.id, {
                success: function (data) {
                    //console.info('allInAction', data);
                },
                error: function (data) {
                    console.error('allInAction', data);
                }
            });
        },
        raiseAction: function () {
//            this.view.showRaiseDialog(20, 40, 200, function (value, callbackClose) {
//                console.log(value);
//                callbackClose.close();
//            });
            var player = this.game.getTurnPlayer();
            if (player.info['id'] == this.game.my_id) {
                var callValue = this.game.getCallValue(player.info['id'], this.game.step);
                var self = this;
                var minValue = self.game.getMinRaise();
                var maxValue = self.game.getMaxRaise(player);
                this.view.showRaiseDialog(callValue, minValue, maxValue, function (value, callbackClose) {
                    if (player.balance == value - callValue) {
                        self.requests.allin(self.game.options.id, {
                            success: function (data) {
                                //console.info('allInAction', data);
                                callbackClose.close();
                            },
                            error: function (data) {
                                console.error('allInAction', data);
                            }
                        });
                    } else {
                        self.requests.raise(self.game.options.id, value, {
                            success: function (data) {
                                //console.info('raiseAction', data);
                                callbackClose.close();
                            },
                            error: function (data) {
                                console.error('raiseAction', data);
                            }
                        });
                    }
                });
            }
        },
        standUpAction: function () {
            if (this.game.getPostionByPlayer(this.params.user.get('id'))) {
                var self = this;
                this.requests.leave(this.game.options.id, {
                    success: function (data) {
                        //console.info('standUpAction', data);
                        self.view.hideWelcomeAlert();
                        $('#standUpBtn').fadeOut();
                        $('#toLobbyBtn').removeClass('disabled');
                        self.params.user.set('room_id', null);
                    },
                    error: function (data) {
                        console.error('standUpAction', data);
                    }
                });
            }
        },
        seatAction: function (position) {
            var userId = this.params.user.get('id');
            if (this.game.users[position] === undefined
                    && this.game.getPostionByPlayer(userId) === false
                    ) {
                var self = this;
                self.params.openWindow('situp', function (value) {
                    self.requests.join(self.game.options.id, position, value, {
                        success: function (data) {
                            //console.info('seatAction', data);
                            if (self.game.isGameEnd) {
                                self.view.showWelcomeAlert();
                            }
                            $('#toLobbyBtn').addClass('disabled');
                            $('#standUpBtn').fadeIn();
                            self.view.myPos = position;
                            self.params.user.set('room_id', self.game.options.id);
                        },
                        error: function (data) {
                            console.error('seatAction', data);
                        }
                    });
                });
            }
        },
        resize: function () {
            if (this.view !== undefined) {
                this.view.resize();
            }
        },
        animate: function () {
            if (this.view !== undefined) {
                this.view.animate();
            }
        }
    });
});
