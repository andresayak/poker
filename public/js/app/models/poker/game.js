define([
    'models/poker/player',
    'models/poker/card',
], function(Player, Card){

    function Game(room, getTimeNow){
        this.room = room;
        this.getTimeNow = getTimeNow;
        var blind = parseInt(room.get('blind'));
        var time = parseInt(room.get('data').time);
        this.options = {
            id: room.get('id'),
            blinds: [blind, blind*2],
            time: time,
            rounds: 3
        };
        this.max_players= parseInt(room.get('max_players'));
        this.turnPosition = parseInt(room.get('data').turnPosition);
        this.step = parseInt(room.get('data').step);
        this.isGameEnd = room.get('data').isGameEnd;
        this.isStepEnd = room.get('data').isStepEnd;
        this.dealerPosition = parseInt(room.get('data').dealerPosition);
        this.publicCards = [];
        this.time_update = parseInt(room.get('time_update'));
        this.users = {};
        for(var position in room.get('data').positions){
            this.users[position] = this.createPlayer(room.get('data').positions[position]);
        }
        this.bank = room.get('data').bank;
        for(var i in room.get('data').publicCards){
            var card = room.get('data').publicCards[i];
            this.publicCards[this.publicCards.length] = new Card(
                card.suit, card.value
            );
        }
    }

    Game.prototype.trigger = function(event, params){
        this.room.trigger(event, params);
    };

    Game.prototype.proxy = function(data){
        console.info('POKER data', data);
        this.time_update = parseInt(data.time_update);
        switch(data.name){
            case 'start':
                this.start(data);
                break;
            case 'turnNext':
                this.turnPosition = data.turnPosition;
                this.turnNext();
                break;
            case 'privateCards':
                this.setPrivateCards(data);
                break;
            case 'stepEnd':
                this.isStepEnd = true;
                if(this.step == this.options['rounds']){
                    this.isGameEnd = true;
                }else{
                    this.step++;
                    this.runstep();
                    this.trigger('bank.update', this.bank['main']['balance']);
                }
                break;
            case 'check':
                this.trigger('timer.end');
                var player = this.users[data.position];
                this.check(player);
                break;
            case 'allin':
                this.trigger('timer.end');
                var player = this.users[data.position];
                this.allin(player, data.value);
                break;
            case 'call':
                this.trigger('timer.end');
                var player = this.users[data.position];
                this.call(player, data.value);
                break;
            case 'raise':
                this.trigger('timer.end');
                var player = this.users[data.position];
                this.raise(player, data.value);
                break;
            case 'publicCards':
                this.setPublicCards(data);
                break;
            case 'timeout':
                this.timeout(data.position);
                break;
            case 'fold':
                var player = this.users[data.position];
                this.fold(player);
                break;
            case 'win':
                this.trigger('timer.end');
                this.endGame(data);
                break;
            case 'leave':
                this.trigger('timer.end');
                this.leave(data);
                break;
            case 'join':
                this.join(this.createPlayer(data.user), data.position);
                break;
        }
    };

    Game.prototype.isAllinEnd = function(){
        var count = 0;
        for (var position in this.users) {
            if(this.users[position].play
                && !this.users[position].allin
                && !this.users[position].fold
            ){
                count++;
            }
        }
        return (count == 1);
    };

    Game.prototype.getMaxRaise = function(player)
    {
        var value = 0;
        for (var position in this.users) {
            if(this.users[position].play
                //&& !this.users[position].allin
                && !this.users[position].fold
                && this.users[position].info['id']!=player.info['id']
            ){
                var valueSum = this.getCallSumByPlayer(this.users[position].info['id'], this.step);
                console.log('getMinRaise', position, 'valueSum', valueSum);
                value = Math.max(value, valueSum + this.users[position].balance);
            }
        }
        value = Math.min(value, valueSum + player.balance);
        return value;
    };

    Game.prototype.getMinRaise = function()
    {
        var value = 0;
        for (var position in this.users) {
            if(this.users[position].play
                //&& !this.users[position].allin
                && !this.users[position].fold
            ){
                var valueSum = this.getCallSumByPlayer(this.users[position].info['id'], this.step);
                console.log('getMinRaise', position, 'valueSum', valueSum);
                value = Math.max(value, valueSum);
            }
        }
        value = Math.max(value+1, this.options.blinds[1]);
        return value;
    };

    Game.prototype.createPlayer = function(user)
    {
        return new Player(user);
    };

    Game.prototype.setPublicCards = function(data)
    {
        this.publicCards = [];
        for (var i in data.cards) {
            this.publicCards[this.publicCards.length] = new Card(
                data.cards[i].suit, data.cards[i].value
            );
        }
        this.trigger('addCards', {
            cards: this.publicCards
        });
    };

    Game.prototype.start = function(data)
    {
        for (var position in data.users) {
            if (this.users[position] !== undefined) {
                this.users[position].play = data.users[position].play;
            }
        }
        this.startGame();
        this.dealerPosition = data.dealerPosition;
        this.trigger('dealer.change', data.dealerPosition);
        this.blindes(data.blindes);
        if(!this.isMyPlay()){
            this.setPrivateCards();
        }
        this.runstep();
    };

    Game.prototype.isMyPlay = function(){
        for(var position in this.users){
            if (this.my_id == this.users[position].info['id']) {
                return true;
            }
        }
        return false;
    };

    Game.prototype.setPrivateCards = function (data)
    {
        for (var position in this.users) {
            if (this.users[position].play) {
                var userId = this.users[position].info['id'];
                if (this.my_id == userId
                        && userId == data.player_id) {
                    var cards = [];
                    for (var i in data.cards) {
                        cards[cards.length] = new Card(
                            data.cards[i].suit, data.cards[i].value
                        );
                    }
                } else {
                    var cards = [
                        new Card(),
                        new Card()
                    ];
                }
                this.users[position].cards = cards;
                this.trigger('addCards', {
                    position: position,
                    cards: cards
                });
            }
        }
    };

    Game.prototype.getTurnPlayer = function()
    {
        return this.users[this.turnPosition];
    };

    Game.prototype.array_search = function( needle, haystack, strict ) {
        var strict = !!strict;
        for(var key in haystack){
            if( (strict && haystack[key] === needle) || (!strict && haystack[key] == needle) ){
                return key;
            }
        }
        return false;
    };

    Game.prototype.getPostionByPlayer = function(player_id){
        for(var position in this.users){
            if(this.users[position].info['id'] == player_id){
                return position;
            }
        }
        return false;
    };

    Game.prototype.getTurnPlayer = function()
    {
        return this.users[this.turnPosition];
    };

    Game.prototype.turnNextPlayer = function(){
        var keys = Object.keys(this.users);
        var index = this.array_search(this.turnPosition, keys);
        this.turnPosition = parseInt(keys[(index+1)%keys.length]);
    };

    Game.prototype.turnNext = function()
    {
        if(!this.isGameEnd){
            console.log('turnPosition', this.turnPosition);
            this.createTimeoutTime();
        }
    };

    Game.prototype.blindes = function(blindes)
    {
        for(var i in blindes){
            this.blinde(blindes[i]);
        }
    };

    Game.prototype.blinde = function(blinde)
    {
        var position = blinde.position;
        this.addToBank(position, this.users[position], blinde.value, 'blind');
        return position;
    };

    Game.prototype.startGame = function()
    {
        this.timeStart = this.getTimeNow();
        this.isGameEnd = false;
        for(var position in this.users){
            this.users[position].fold = false;
            this.users[position].allin = false;
        }
        this.step = 0;
        //this.nextDealer();
        this.trigger('game.start');
    };

    Game.prototype.runstep = function()
    {
        this.isStepEnd = false;
    };

    Game.prototype.createTimeoutTime = function()
    {
        var timeDiff = this.getTimeNow() - this.time_update*1000;
        var time = this.options['time'] * 1000;
        this.trigger('timer.start', {
            timeDiff: timeDiff, position: this.turnPosition, time: time
        });
    };

    Game.prototype.check = function(player)
    {
        if(this.isStepEnd){
            console.error('step end');
            return ;
        }
        var position = this.getPostionByPlayer(player.info['id']);
        if(position){
            if(this.turnPosition != position){
                console.error('Not your step', this.turnPosition, position);
                //return;
            }
            this.addToBank(position, player, 0, 'check');
        }else{
            console.error('position not found', player.info['id'], position);
        }
    };

    Game.prototype.call = function(player)
    {
        if(this.isStepEnd){
            console.error('step end');
            return ;
        }
        var position = this.getPostionByPlayer(player.info['id']);
        if(position){
            if(this.turnPosition != position){
                console.error('Not your step', this.turnPosition, position);
                //return;
            }
            var value = this.getCallValue(player.info['id'], this.step);
            this.addToBank(position, player, value, 'call');
        }else{
            console.error('position not found', player.info['id'], position);
        }
    };

    Game.prototype.allin = function(player, value)
    {
        if(this.isStepEnd){
            console.error('step end');
            return;
        }
        var position = this.getPostionByPlayer(player.info['id']);
        if(position){
            this.users[position].allin = true;
            this.addToBank(position, player, value, 'allin');
        }else{
            console.error('position not found', player.info['id'], position);
        }
    };

    Game.prototype.raise = function(player, value)
    {
        if(this.isStepEnd){
            console.error('step end');
            return ;
        }
        var position = this.getPostionByPlayer(player.info['id']);
        if(position){
            if(this.turnPosition != position){
                console.error('Not your step', this.turnPosition, position);
                //return;
            }
            this.addToBank(position, player, value, 'raise');
        }else{
            console.error('position not found', player.info['id'], position);
        }
    };

    Game.prototype.fold = function(player)
    {
        if(this.isStepEnd){
            return ;
        }
        var position = this.getPostionByPlayer(player.info['id']);
        if(position){
            player.fold = true;
            console.info('player = '+player.info['username']+' fold');
            if(this.turnPosition != position){
                this.trigger('timer.end');
            }
            this.trigger('fold', {
                position: position
            });
        }
    };

    Game.prototype.endGame = function(data)
    {
        this.trigger('timer.end');
        this.trigger('game.end', data);

        for(var position in data.balances){
            this.users[position].balance = data.balances[position];
        }
        this.resetBank();
        this.clearPlayers();
    };

    Game.prototype.join = function(user, position)
    {
        position = parseInt(position);
        console.info('join user', user.info['social_name'], position);
        this.users[position] = user;
        this.trigger('join', position);
    };

    Game.prototype.leave = function(data)
    {
        delete this.users[data.position];
        this.trigger('leave', data);
    };

    Game.prototype.timeout = function(position)
    {
        delete this.users[position];
        this.trigger('timeout', position);
    };

    Game.prototype.clearPlayers = function()
    {
        for(var position in this.users){
            if(this.users[position].balance === 0){
                delete this.users[position];
            }
        }
        return this;
    };

    Game.prototype.nextDealer = function()
    {
        var keys = Object.keys(this.users);
        if(this.dealerPosition === null){
            this.dealerPosition = keys[0];
        }else{
            var index = this.array_search(this.dealerPosition, keys);
            this.dealerPosition = keys[(index+1)%keys.length];
        }
        this.trigger('dealer.change', this.dealerPosition);
        this.resetBank();
        for(var position in this.users){
            this.users[position].play = true;
        }
    };

    Game.prototype.resetBank = function()
    {
        this.bank = {
            'main': {
                balance: 0
            },
            'steps': {}
        };
        this.trigger('bank.reset');
    };

    Game.prototype.getCallValue = function(player_id, step){
        var max = 0;
        if (this.bank['steps'][step] !== undefined){
            for(var i in this.bank['steps'][step]){
                max = Math.max(max, this.bank['steps'][step][i]);
            }
            if(this.bank['steps'][step][player_id] !==undefined){
                max-= this.bank['steps'][step][player_id];
            }
        }
        return max;
    };

    Game.prototype.getCallSumByPlayer = function(player_id, step){
        if (this.bank['steps'][step] !== undefined){
            if (this.bank['steps'][step][player_id] !== undefined){
                return this.bank['steps'][step][player_id];
            }
        }
        return 0;
    };

    Game.prototype.addToBank = function(position, player, value, type){
        console.log('addToBank', position, player, value, type);
        this.updateBank(player.info['id'], value);
        player.balance-=value;
        var allValue = this.getCallSumByPlayer(player.info['id'], this.step);
        console.log('player  = '+player.info['username']+' '+type+': '+value+'  all: '+allValue, this.bank['steps']);
        this.trigger('bet', {
            player: player,
            value: value,
            money: allValue,
            position: position,
            type: type,
        });
    };

    Game.prototype.updateBank = function(player_id, money){
        if(this.bank['steps'][this.step] === undefined){
            this.bank['steps'][this.step] = {};
        }
        if(this.bank['steps'][this.step][player_id] === undefined){
            this.bank['steps'][this.step][player_id] = 0;
        }
        this.bank['steps'][this.step][player_id]+=money;
        this.bank['main']['balance']+=money;
        console.log('updateBankGame ',money, this.bank['main']['balance']);
        return this.bank['steps'][this.step][player_id];
    };

    Game.prototype.isBankCompared = function(){
        for (var i in this.users) {
            var player = this.users[i];
            if (player.fold || !player.play) {
                continue;
            }
            if (this.bank['steps'][this.step] === undefined
                || this.bank['steps'][this.step][player.info['id']] === undefined
                || this.getCallValue(player.info['id'], this.step) !== 0
            ) {
                if (!player.allin)
                    return false;
            }
        }
        return true;
    };

    return Game;
});
