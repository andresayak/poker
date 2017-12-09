define(['views/view'], function (View) {
    return View.extend({
        className: 'inner',
        events: {
            'click #settingsBtn': function(){
                this.params.openWindow('settings');
            },
            'click #sagaBtn': function(){
                this.params.app.dispatch('saga', 'index');
            },
            'click #tournamentsBtn': function(){
                this.params.openWindow('tournaments');
            },
            'click #checkedBtn': function(){
                this.params.openWindow('checked');
            },
            'click #friendsBtn': function(){
                this.params.openWindow('friends');
            },
            'click #shopBtn': function(){
                this.params.openWindow('shop');
            },
            'click #chartBtn': function(){
                this.params.openWindow('chart');
            },
            'click #newsBtn': function(){
                this.params.openWindow('news');
            },
            'click #guildBtn': function(){
                this.params.openWindow('guild');
            },
            'click #inventoryBtn': function(){
                this.params.openWindow('inventory');
            },
            'click #giftsBtn': function(){
                this.params.openWindow('gifts');
            },
            'click #questsBtn': function(){
                this.params.openWindow('quests');
            },
            'click #notificationsBtn': function(){
                this.params.openWindow('notifications');
            },
            'click #mailBtn': function(){
                this.params.openWindow('mail');
            },
            'click #profileBtn': function(){
                this.params.openWindow('profile');
            },
            'click #editMapBtn': function(){
            },
            'click #changeServerBtn': function(){
            },
            'click #raidBtn': function(){
                this.params.app.dispatch('raid', 'index');
            },
            'click #shipsBtn': function(){
                this.params.openWindow('ships');
            },
            'click #galaxyMapBtn': function(){
                this.params.app.dispatch('galaxy', 'index');
            },
            'click #systemMapBtn': function(){
                this.params.app.dispatch('system', 'index');
            },
            'click #planetMapBtn': function(){
                this.params.app.dispatch('planet', 'index');
            },
            'click #commandCenterBtn': function(){
                this.params.openWindow('commandCenter');
            },
            'click #backBtn': function(){
                this.params.app.dispatch('city', 'index');
            },
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('helper-menu');
        },
        render: function () {
            this.$el.html(this.template({
                t: $.t,
                controllerName: this.params.controllerName,
                chatsManager: this.params.app.profile.chatsManager
            }));
            var self = this;
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
            });
            this.listenTo(this.params.app, 'resize', function() {
                self.resize();
            });
            return this;
        },
        resize: function(){
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

