define(['views/view', 'html2canvas'], function (View, hc) {
    return View.extend({
        id: 'modal-help',
        events: {
        },
        initialize: function (params) {
            this.params = params;
            this.template = this.getTemplate('window-help');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t,
            }));
            var self = this;
            this.setUserInfo();
            console.log('profile',this.params.app.profile);
            _.defer(function () {
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.screenshotCanvas;
                html2canvas($("#app .content-inner")[0], {
                    onrendered: function(c){
                        self.screenshotCanvas = c;
                    },
                }).then(function(canvas) {
                    callback();
                    self.loader.end();
                    self.$el.find('.modal').modal('show');
                });
            });
            this.listenTo(this.params.app, 'resize', function() {
                self.resize();
            });
            
            this.$el.find('.modal').on('hidden.bs.modal', function (e) {
                self.remove();
            });
            this.$el.find('.modal').on('shown.bs.modal', function() {
                self.createScreenshot();
            });
            return this;
        },
        resize: function(){
        },
        createScreenshot: function(){
            var self = this;
            var canvas = self.$el.find('#screenshotCanvas');
            var ratio = self.screenshotCanvas.width/self.screenshotCanvas.height;
            var boxWidth = $(canvas).parent().width();
            canvas.attr({
                'width': self.screenshotCanvas.width,
                'height': self.screenshotCanvas.height
            }).css({
                'width':boxWidth,
                'height': boxWidth/ratio
            });
            var ctx = canvas[0].getContext("2d");
            ctx.drawImage(self.screenshotCanvas, 0, 0);
        },
        submit: function(e){
            e.preventDefault();
            return false;
        },
        setUserInfo: function(){
            var user = this.params.app.profile.user;
            this.$el.find('.fromelement .avatar')
                .css('background-image', 'url(\''+user.get('url')+'\')');
                
            this.$el.find('.fromelement .username a')
                .text(user.get('name'))
                .attr('href', user.get('url'));
            
        }
    });
});