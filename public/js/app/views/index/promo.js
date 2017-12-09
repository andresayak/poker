define([
    'views/view',
    'filters/TimeAgo',
    'prototypes/IntervalManager'
], function (View, filterTimeAgo, IntervalManager) {
    return View.extend({
        id: 'modal-promo',
        events: {
            'click .buy-now': 'buy'
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-promo');
            this.timeAgo = new filterTimeAgo({app: this.params.app});
            this.intervalManager = new IntervalManager('promo');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
                promo: this.params.promo,
                count: this.countFilter(parseInt(this.params.promo.getObjectCount('chip'))),
                userName: this.params.userName,
                tr: _.template
            }));
            var self = this;
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.loader.end();
                callback();
            });
            this.listenTo(this.params.app, 'resize', function () {
                self.resize();
            });
            this.$el.find('.modal').on('show.bs.modal', function (e) {
                self.setCountdown($(e.target));
            });
            this.$el.find('.modal').modal('show');
            this.$el.find('.modal').on('hidden.bs.modal', function () {
                self.intervalManager.remove(self.countdown);
                self.remove();
            });
            return this;
        },
        resize: function () {
        },
        submit: function (e) {
            e.preventDefault();
            return false;
        },
        buy: function (e) {
            var self = this;
            var id = $(e.currentTarget).data('id');
            this.params.buyBtn(id, 1, {
                success: function () {
                    self.$el.find('.modal').modal('hide');
                },
                error: function (data) {
                    console.error('error', data);
                }
            });
        },
        setCountdown: function (target) {
            var self = this;
            var fade = true;
            var el = target.find('.time .label');
            var d = new Date();
            d.setHours(24, 0, 0, 0);
            var time = d.getTime() / 1000;
            self.intervalManager.add('promo1TimeAgo', function (name) {
                self.countdown = name;
                var timeNow = self.params.app.getTimeNow() / 1000 + 2;
                if (timeNow > time) {
                    self.intervalManager.remove(name);
                    self.$el.find('.modal').modal('hide');
                }
                el.html(self.timeAgo.filter(time, true, 'min'));
                if (fade) {
                    el.addClass('in');
                }
            });
        }
    });
});