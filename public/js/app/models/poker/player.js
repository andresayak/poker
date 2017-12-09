define([
     'models/poker/card',
], function(Card){
     
    function Player(data){
        this.info = data;
        this.fold = data.fold;
        this.allin = data.allin;
        this.play = data.play;
        this.balance = data.money;
        if(data.cards !== undefined){
            this.cards = [];
            for (var i in data.cards) {
                this.cards[this.cards.length] = new Card(
                    data.cards[i].suit, data.cards[i].value
                );
            }
        }
    }
     
    Player.prototype = {
    };
    
    return Player;
});