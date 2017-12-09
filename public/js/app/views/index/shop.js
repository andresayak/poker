define(['views/view'], function (View) {
    return View.extend({
        id: 'modal-shop',
        events: {
            'click .shopList .shopItem': 'buy',
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-shop');
        },
        render: function () {
            var self = this;
            if (!this.params.shopList.initStatus) {
                this.params.shopRequest({
                    success: function () {
                        self.render();
                    }
                });
                return this;
            }
            this.$el.html(this.template({
                t: $.t,
                shopList: this.params.shopList
            }));

            var self = this;
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.loader.end();
            });
            this.listenTo(this.params.app, 'resize', function () {
                self.resize();
            });
            this.$el.find('.modal').modal('show');
            this.$el.find('.modal').on('hidden.bs.modal', function (e) {
                self.remove();
            });
            return this;
        },
        buy: function (e) {
            var id = $(e.currentTarget).data('id');
            var self = this;
            this.params.buyBtn(id, 1, {
                success: function () {
                    if (self.params.callbacks !== undefined) {
                        if (typeof (self.params.callbacks.buyEnd) == 'function') {
                            self.params.callbacks.buyEnd();
                        }
                    }
                    self.$el.find('.modal').modal('hide');
                },
                error: function (data) {
                    console.error('error', data);
                }
            });
        },
        resize: function () {
        }
    });
});