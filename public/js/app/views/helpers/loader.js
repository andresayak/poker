define( function (View) {
    return Backbone.View.extend({
        className: 'backlayout',
        events: {
        },
        start: function(){
            $('#app').append(this.el);
        },
        end: function(){
            this.remove();
        }
    });
});

