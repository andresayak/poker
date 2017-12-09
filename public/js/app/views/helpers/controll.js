define(['views/view'], function (View) {
    return View.extend({
        id: 'systemBtns',
        className: 'system-buttons',
        events: {
            'click #logoutBtn': function(){
                this.params.app.profile.logout();
                this.params.app.dispatch('index', 'index');
            },
            'click #fullscreenBtn': 'fullscreenToggle',
            'click #audioBtn': 'audioToggle',
            'click #helpBtn': function(){
                this.params.openWindow('help');
            },
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('helper-controll');
        },
        render: function () {
            this.$el.html(this.template({
                controllerName: this.params.controllerName,
                t: $.t,
            }));
            var self = this;
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
            return this;
        },
        fullscreenToggle: function(){
            if (!document.fullscreenElement &&
                !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        },
        audioToggle: function(){
            
        }
    });
});

