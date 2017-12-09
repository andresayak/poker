define(['views/view'], function (View) {
    return View.extend({
        className: 'inner',
        events: {
            'click #showStantionsBtn .dropdown-menu li': function(e){
                this.params.cityChangeBtn($(e.currentTarget).data('id'));
            }
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('helper-topInfo');
        },
        render: function () {
            this.$el.html(this.template({
                controllerName: this.params.controllerName,
                profile: this.params.profile,
                city: this.params.city,
                t: $.t
            }));
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
            return this;
        }
    });
});

