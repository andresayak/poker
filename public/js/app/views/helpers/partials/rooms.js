define(['views/view'], function (View) {
    return View.extend({
        className: 'inner',
        events: {
            'click tr': 'selectRoom',
            'click #enterRoomBtn': 'enter',
            'click #refreshRoomsBtn': 'refreshList',
            'dblclick tr': function (e) {
                this.selectRoom(e);
                this.enter();
            }
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('part-rooms');
        },
        selectRoom: function (e) {
            this.$el.find('tr').removeClass('selected');
            $(e.currentTarget).addClass('selected');
            $('#enterRoomBtn').removeClass('disabled');
        },
        render: function () {
            this.$el.html(this.template({
                t: $.t,
                list: this.params.list
            }));
            return this;
        },
        enter: function () {
            this.loader.start();
            var id = this.$el.find('tr.selected').data('id');
            if (id) {
                this.params.enterBtn(id);
            } else {
                this.loader.end();
            }
        },
        refreshList: function (page) {
            this.loader.start();
            var self = this;
            this.pagination('#game-poker-public-list', function (callback, p) {
                self.params.getRoomsRequest(p, callback);
            }, self.renderRefreshList);
        },
        renderRefreshList: function (data, p, force) {
            var self = this;
            if (Object.keys(data.list).length) {
                if (p === 1 && !force) {
                    self.params.list = data.list;
                    self.render();
                }
            }
            this.loader.end();
        }
    });
});

