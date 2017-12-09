define(['views/view'], function (View) {
    return View.extend({
        id: 'modal-situp',
        old: '',
        events: {
            'mousedown .line-btn': function (e) {
                e.preventDefault();
                var self = this;
                var scrollElement = e.currentTarget;
                var scrollBlockElement = $(scrollElement).parent();
                var max = parseInt(scrollBlockElement.attr('data-max'));
                var code = scrollBlockElement.attr('data-code');
                $('#' + self.id).mousemove(function (e) {
                    var a = e.pageX - scrollBlockElement.offset().left;
                    var b = scrollBlockElement.width();
                    var c = Math.min(1, Math.max(0, a / b));
                    self.setCurrenty(code, Math.round(max * c), max, true);
                }).mouseup(function () {
                    $(this).unbind('mousemove');
                });
                $('#' + self.id + ' .expprogress').mouseleave(function () {
                    $('#' + self.id).unbind('mousemove');
                });
            },
            'click .line': function (e) {
                var self = this;
                var element = $(e.currentTarget);
                var max = parseInt(element.attr('data-max'));
                var code = element.attr('data-code');
                var a = e.pageX - element.offset().left;
                var b = element.width();
                var c = Math.min(1, Math.max(0, a / b));
                self.setCurrenty(code, Math.round(max * c), max, true);
            },
            'keydown #current': function (e) {
                var self = this;
                self.old = $(e.currentTarget).val();
            },
            'keyup #current': function (e) {
                var self = this;
                var element = $(e.currentTarget);
                var code = element.attr('data-code');
                var max = parseInt(element.attr('data-max'));
                var cur = 0;
                if (element.val() == '') {
                    cur = 0;
                } else {
                    var value = parseInt(element.val());
                    if (value < 0 || value > max) {
                        element.val(self.old);
                    } else {
                        cur = value;
                    }
                }
                self.setCurrenty(code, cur, max, true);
            },
            'blur #current': function (e) {
                var element = $(e.currentTarget);
                if (element.val() == '') {
                    element.val('0');
                }
            },
            'click #joinBtn': function () {
                var value = parseInt($('#' + this.id + ' #current').val());
                if (value > 0) {
                    this.params.joinBtn(value);
                    this.$el.find('.modal').modal('hide');
                }
            }
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-situp');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
                user: this.params.app.profile.user
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
            this.$el.find('.modal').modal('show');
            this.$el.find('.modal').on('hidden.bs.modal', function () {
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
        setCurrenty: function (code, current, max, change) {
            var element = '#' + this.id + ' .line-btn';
            var scrollElement = $(element);
            var scrollBlockElement = scrollElement.parent();
            var scrollBgElement = scrollBlockElement.find('.line-t');
            var c = (max) ? current / max : 0;
            var w = scrollElement.width();
            var b = scrollBlockElement.width();
            $(scrollElement).css({
                'left': ((b) * c) - w / 2 + 'px'
            });
            $(scrollBgElement).css({
                width: 100 * c + '%'
            });
            if (change) {
                $('.joinchips input[type="text"]').val(current);
            }
        }
    });
});