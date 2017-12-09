define([
    'views/view', 'views/helpers/controll', 'konva'
], function (View, ControllHelper, Konva) {
    var View = View.extend({
        className: 'content-inner',
        elementCanvas: 'index-canvas',
        events: {
            'click #btnPlay': 'playAction'
        },
        initialize: function (params) {
            this.params = params;
            this.params.controllerName = 'index';
            this.template = this.getTemplate('page-index');
        },
        playAction: function () {
            var self = this;
            this.params.playBtn(function () {
                self.remove();
            });
        },
        render: function (callback) {
            var self = this;
            this.$el.html(this.template({
                t: $.t
            }));
            _.defer(function () {
                var w, h, ratio;

                var cursorPointer = function () {
                    document.getElementById(self.elementCanvas).style.cursor = 'pointer';
                };
                var cursorDefault = function () {
                    document.getElementById(self.elementCanvas).style.cursor = 'default';
                };

                var bgImg = self.getImage('bg');
                var loaderImg = self.getImage('loader');
                var titleImg = self.getImage('index-title');
                var startbtnImg = self.getImage('startbtn');

                var size = self.getSize();
                ratio = bgImg.naturalWidth / bgImg.naturalHeight;
                var width = size.w;
                var height = Math.round(Math.min(size.h, width / ratio));
                width = Math.round(height * ratio);
                ratio = width / bgImg.naturalWidth;

                self.stage = new Konva.Stage({
                    container: self.elementCanvas,
                    width: width,
                    height: height
                });

                var bgLayer = new Konva.Layer();

                var bg = new Konva.Image({
                    id: 'bg',
                    x: 0,
                    y: 0,
                    width: width,
                    height: height,
                    image: bgImg,
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                });
                //bg.cache();

                w = loaderImg.naturalWidth * ratio;
                h = loaderImg.naturalHeight * ratio;
                var loader = new Konva.Image({
                    id: 'loader',
                    x: Math.round(width / 2 - w / 2) + Math.round(w / 2),
                    y: Math.round(height - h * 1.15) + Math.round(h / 2),
                    offset: {
                        x: Math.round(w / 2),
                        y: Math.round(h / 2)
                    },
                    width: w,
                    height: h,
                    image: loaderImg,
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                });
                //loader.cache();

                w = titleImg.naturalWidth * ratio;
                h = titleImg.naturalHeight * ratio;
                var title = new Konva.Image({
                    id: 'title',
                    x: Math.round(width / 2 - w / 2),
                    y: Math.round(height / 2 - h / 2),
                    width: w,
                    height: h,
                    image: titleImg,
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                });
                //title.cache();

//                w = startbtnImg.naturalWidth * ratio;
//                h = startbtnImg.naturalHeight * ratio;
//                var startbtn = new Konva.Image({
//                    id: 'startbtn',
//                    x: Math.round(width / 2 - w / 2),
//                    y: Math.round(height - h * 1.15),
//                    width: w,
//                    height: h,
//                    image: startbtnImg,
//                    transformsEnabled: 'position',
//                    strokeEnabled: false,
//                    strokeHitEnabled: false,
//                    shadowForStrokeEnabled: false,
//                    perfectDrawEnabled: false,
//                    listening: true
//                });
//                startbtn.on('mouseover', cursorPointer);
//                startbtn.on('mouseout', cursorDefault);
//                startbtn.on('click', function () {
//                    startbtn.listening(false);
//                    self.playAction();
//                });

                var group = new Konva.Group({
                    transformsEnabled: 'position'
                });
                group.opacity(0);

                bgLayer.add(bg);
                bgLayer.add(loader);
                group.add(title);
//                group.add(startbtn);
                bgLayer.add(group);

                var period = 2000;
                var anim = new Konva.Animation(function (frame) {
                    var scale = Math.sin(frame.time * 2 * Math.PI / period) + 0.001;
                    loader.scaleX(scale);
                }, bgLayer);
                anim.start();

                self.stage.add(bgLayer);

                var tween = new Konva.Tween({
                    node: group,
                    duration: 1,
                    opacity: 1,
//                    onFinish: function () {
//                        startbtn.cache();
//                        startbtn.drawHitFromCache();
//                    }
                });
                callback();
                setTimeout(function () {
//                    if (anim.isRunning()) {
//                        anim.stop();
//                        loader.hide();
//                    }
                    tween.play();
                    setTimeout(function () {
                        self.playAction();
                    }, 350);
                }, 1000);
            });
            this.listenTo(this.params.app, 'frame', function () {
            });
            this.listenTo(this.params.app, 'resize', function () {
                var w, h, ratio;

                var bgImg = self.getImage('bg');
                var loaderImg = self.getImage('loader');
                var titleImg = self.getImage('index-title');
                var startbtnImg = self.getImage('startbtn');

                var size = self.getSize();
                ratio = bgImg.naturalWidth / bgImg.naturalHeight;
                var width = size.w;
                var height = Math.round(Math.min(size.h, width / ratio));
                width = Math.round(height * ratio);

                ratio = width / bgImg.naturalWidth;

                this.stage.setWidth(width).setHeight(height);

                var bg = this.stage.findOne('#bg');
                if (bg) {
                    //bg.clearCache();
                    bg.width(width).height(height);
                    //bg.cache();
                }

                var loader = this.stage.findOne('#loader');
                if (loader) {
                    w = loaderImg.naturalWidth * ratio;
                    h = loaderImg.naturalHeight * ratio;
                    //loader.clearCache();
                    loader.x(Math.round(width / 2 - w / 2) + Math.round(w / 2))
                            .y(Math.round(height - h * 1.15) + Math.round(h / 2))
                            .offset({x: Math.round(w / 2), y: Math.round(h / 2)})
                            .width(w).height(h);
                    //loader.cache();
                }

                var title = this.stage.findOne('#title');
                if (title) {
                    w = titleImg.naturalWidth * ratio;
                    h = titleImg.naturalHeight * ratio;
                    //title.clearCache();
                    title.x(Math.round(width / 2 - w / 2))
                            .y(Math.round(height / 2 - h / 2))
                            .width(w).height(h);
                    //title.cache();
                }

                var startbtn = this.stage.findOne('#startbtn');
                if (startbtn) {
                    w = startbtnImg.naturalWidth * ratio;
                    h = startbtnImg.naturalHeight * ratio;
                    startbtn.clearCache();
                    startbtn.x(Math.round(width / 2 - w / 2))
                            .y(Math.round(height - h * 1.15))
                            .width(w).height(h);
                    startbtn.cache();
                }

                this.stage.draw();
            });

            return this;
        },
        getSize: function () {
            return  {
                w: $('#' + this.elementCanvas).width(),
                h: $('#' + this.elementCanvas).height()
            };
        }
    });
    return View;

});