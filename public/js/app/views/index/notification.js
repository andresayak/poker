define(['views/view'], function (View) {
    return View.extend({
        id: 'modal-notification',
        events: {
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-notification');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
            }));
            var self = this;
            _.defer(function () {
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
    });
});