define([
    'views/view', 'views/helpers/chats', 'views/helpers/menu',
    'views/helpers/controll', 'views/helpers/topInfo',  'views/helpers/systemmap'
], function (View, ChatsHelper, MenuHelper, ControllHelper, TopInfoHelper, SystemMap) {
    return View.extend({
        className: 'content-inner',
        events: {
            'click #mapSystemPlaneControll .btn': function(e){
                this.$el.find('#mapSystemPlaneControll .btn').removeClass('active');
                $(e.currentTarget).addClass('active');
                this.systemMap.changPlane($(e.currentTarget).data('value'));
            },
            'click #mapSystemZoomControll .btn': function(e){
                this.$el.find('#mapSystemZoomControll .btn').removeClass('active');
                $(e.currentTarget).addClass('active');
                this.systemMap.changZoom($(e.currentTarget).data('value'));
            },
            'click #creditsBtn': function(){
                this.params.openWindow('credits');
            },
        },
        initialize: function (params) {
            this.params = params;
            this.params.controllerName = 'system';
            this.template = this.getTemplate('page-system-index');
        },
        render: function () {
            this.$el.html(this.template({
                t: $.t
            }));
            var controllHelper = new ControllHelper(this.params);
            this.$el.find('#bottom').html(controllHelper.render().$el);
            
            var chatsHelper = new ChatsHelper(this.params);
            this.$el.find('#chats').html(chatsHelper.render().$el);
            
            var menuHelper = new MenuHelper(this.params);
            this.$el.find('#menu').html(menuHelper.render().$el);
            
            var topInfoHelper = new TopInfoHelper(this.params);
            this.$el.find('#topInfo').html(topInfoHelper.render().$el);
            
            var self = this;
            this.systemMap = new SystemMap({
                container: self.$el.find('#systemMap')[0],
                infoBtn: function(){
                }
            });
            _.defer(function() {
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.systemMap.init();
            });
            this.listenTo(this.params.app, 'frame', function(frame) {
                self.systemMap.render(frame);
            });
            this.listenTo(this.params.app, 'resize', function() {
                self.resize();
                self.systemMap.resize();
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