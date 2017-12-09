define(['views/view'], function (View) {
    return View.extend({
        id: 'modal-error',
        events: {
            'click .btn-modal-resend': 'resent'
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('page-index-error');
        },
        render: function () {
            this.$el.html(this.template({
                error: this.params.response,
                t: $.t,
            }));
            var self = this;
            this.$el.find('.modal').modal('show');
            this.$el.find('.modal').on('hidden.bs.modal', function (e) {
                self.remove();
            });
            return this;
        },
        resent: function(){
            this.remove();
            this.params.resentBtn();
        }
    });
});

