define([
], function(){
     
    function Card(suit, value){
        this.use_in_ranking = false;
        this.suit = suit;
        this.value = value;
    };
    
    Card.prototype.toString = function(){
        if(this.suit === undefined || this.value === undefined)
            return 'back';
        else
            return this.value+'-'+this.suit;
    };
    
    return Card;
});