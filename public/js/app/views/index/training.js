define(['views/view', 'konva'], function (View, Konva) {
    return View.extend({
        id: 'modal-training',
        events: {
            'mouseenter #infoTraining': 'infoTrainingShow',
            'mouseleave #infoTraining': 'infoTrainingHide',
            'click #trainingButtonA': 'buttonA',
            'click #trainingButtonB': 'buttonB',
            'click #trainingButtonC': 'buttonC'
        },
        initialize: function (params) {
            this.params = params;
            this.countEl = -1;
            this.template = this.getTemplate('window-training');
        },
        render: function (callback) {
            this.$el.html(this.template({
                t: $.t
            }));
            var self = this;
            _.defer(function () {
                $(document).ready(function () {
                    setTimeout(function () {
                        $("#whichHand").show("normal");
                    }, 5000);
                });
                $('[data-toggle="tooltip"]').tooltip();
                self.resize();
                self.loader.end();
                callback();
            });
            this.listenTo(this.params.app, 'resize', function () {
                self.resize();
            });
            this.$el.find('.modal').modal('show');

            var loaderImg = self.getImage('loader');
            self.cardsBig = self.getImage('cards');
            var size = {w: 500, h: 547};
            var bgLayer = new Konva.Layer();
            self.layer0 = new Konva.Layer();
            self.layer1 = self.layer0.clone();
            self.layer2 = self.layer0.clone();
            var layer5 = self.layer0.clone();

            self.stage = new Konva.Stage({
                container: self.$el.find('#stageCanvas')[0],
                width: size.w,
                height: size.h
            });

            var rect2 = new Konva.Rect({
                x: -150,
                y: 280,
                width: 368,
                height: 522,
                fillPatternImage: self.cardsBig,
                fillPatternOffset: ({x: 2945, y: 2089})
            });

            var rect5 = rect2.clone({x: 0, y: 0});
            rect2.scale({x: 1 / 4, y: 1 / 4});
            var rect3 = rect2.clone();
            var rect4 = rect2.clone();

            var topCards = new Konva.Group({x: 45, y: 40});

            rect5.scale({x: 1 / 5.7, y: 1 / 5.7});
            var rect6 = rect5.clone({x: 75, y: 0});
            var rect7 = rect5.clone({x: 150, y: 0});
            var rect8 = rect5.clone({x: 260, y: 0});
            var rect9 = rect5.clone({x: 340, y: 0});

            topCards.add(rect5, rect6, rect7, rect8, rect9);
            layer5.add(topCards);

            var paramsX = [27, 180, 330];
            var gr0 = self.createGroupCards({x: 27, y: 280});
            var gr1 = self.createGroupCards({x: 180, y: 280});
            var gr2 = self.createGroupCards({x: 330, y: 280});

            self.layer0.add(rect2);
            self.layer1.add(rect3);
            self.layer2.add(rect4);
            var tw0 = new Konva.Tween({
                node: rect2,
                duration: 1.5,
                x: 352,
                easing: Konva.Easings.StrongEaseOut,
            });
            var tw1 = new Konva.Tween({
                node: rect3,
                duration: 1.5,
                x: 203,
                easing: Konva.Easings.StrongEaseOut,
            });
            var tw2 = new Konva.Tween({
                node: rect4,
                duration: 1.5,
                x: 54,
                easing: Konva.Easings.StrongEaseOut,
                onFinish: function () {
                    var tween = TweenMax.to(rect4, 0.3, {
                        scaleX: .1,
                        duration: .3,
                        onUpdate: function () {
                            if (tween.time() >= 0.29) {
                                rect4.remove();

                                self.layer2.add(gr0);
                                var tes = new Konva.Tween({
                                    node: self.layer2.findOne('#el1'),
                                    rotation: 10,
                                    x: 50,
                                    y: 0,
                                    duration: .07
                                });
                                var tes1 = new Konva.Tween({
                                    node: self.layer2.findOne('#el0'),
                                    x: 0,
                                    y: 10,
                                    rotation: -8,
                                    duration: .003
                                });
                                tes.play();
                                tes1.play();
                            }
                            self.layer2.batchDraw();
                        }
                    });
                }
            });
            setTimeout(function () {
                tw0.play();
            }, 1000);
            setTimeout(function () {
                tw1.play();
            }, 1500);
            setTimeout(function () {
                tw2.play();
            }, 2000);
            setTimeout(function () {
                var tween = TweenMax.to(rect3, 0.3, {
                    scaleX: .1,
                    duration: .3,
                    onUpdate: function () {
                        if (tween.time() >= 0.29) {
                            rect3.remove();

                            self.layer1.add(gr1);
                            var tes = new Konva.Tween({
                                node: self.layer1.findOne('#el3'),
                                rotation: 10,
                                x: 50,
                                y: 0,
                                duration: .05
                            });
                            var tes1 = new Konva.Tween({
                                node: self.layer1.findOne('#el2'),
                                x: 0,
                                y: 10,
                                rotation: -8,
                                duration: .003
                            });
                            tes.play();
                            tes1.play();
                        }
                        self.layer1.batchDraw();
                    }
                });
            }, 4000);
            setTimeout(function () {
                var tween = TweenMax.to(rect2, 0.3, {
                    scaleX: .1,
                    duration: .3,
                    onUpdate: function () {
                        if (tween.time() >= 0.29) {
                            rect2.remove();

                            self.layer0.add(gr2);
                            var tes = new Konva.Tween({
                                node: self.layer0.findOne('#el5'),
                                rotation: 10,
                                x: 50,
                                y: 0,
                                duration: .03
                            });
                            var tes1 = new Konva.Tween({
                                node: self.layer0.findOne('#el4'),
                                x: 0,
                                y: 10,
                                rotation: -8,
                                duration: .003
                            });
                            tes.play();
                            tes1.play();
                        }
                        self.layer0.batchDraw();
                    }
                });
            }, 4500);

            //  var simpleText = new Konva.Text({
            //     x: 0,
            //     y: 15,
            //     text: 'Simple Text',
            //     fontSize: 30,
            //     fontFamily: 'Calibri',
            //     fill: 'white',
            //     shadowColor: 'green',
            //     shadowBlur: 10,
            // });
            // bgLayer.add(simpleText);

            // console.log('loaderImg', loaderImg);
            self.stage.add(layer5, self.layer0, self.layer1, self.layer2);
            // var loader = new Konva.Image({
            //     id: 'loader',
            //     x: 0,
            //     y: 0,
            //     width: loaderImg.naturalWidth,
            //     height: loaderImg.naturalHeight,
            //     image: loaderImg,
            //     strokeEnabled: false,
            //     strokeHitEnabled: false,
            //     shadowForStrokeEnabled: false,
            //     perfectDrawEnabled: false,
            //     listening: false
            // });
            // bgLayer.add(loader);

            this.$el.find('.modal').on('hidden.bs.modal', function (e) {
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
        createGroupCards: function (obj) {
            var self = this;
            var group0 = new Konva.Group({
                x: (typeof obj === 'object') ? obj.x !== 'undefined' ? obj.x : 0 : 0,
                y: (typeof obj === 'object') ? obj.y !== 'undefined' ? obj.y : 0 : 0,
            });

            var x = -30;
            var y = 20;
            for (var i = 0; i < 2; i++) {
                ++self.countEl;
                var rect = new Konva.Rect({
                    id: 'el' + self.countEl,
                    x: 0,
                    y: 0,
                    width: 368,
                    height: 522,
                    fillPatternImage: self.cardsBig,
                    fillPatternOffset: ({x: 369, y: 1}),
                    // shadowColor: "red",
                    // shadowBlur: 10,
                    // shadowOffset: {x:10, y:10},
                });
                rect.scale({x: 1 / 3.9, y: 1 / 3.9});
                // if (i === 0) rect.rotate(-8);
                // if (i === 1) rect.rotate(10);

                group0.add(rect);
            }
            return group0;
        },
        appearanceCards: function (paramsX) {

        },
        infoTrainingShow: function () {
            $('#infoTrainingText').show();
        },
        infoTrainingHide: function () {
            $('#infoTrainingText').hide();
        },
        buttonA: function () {
            var self = this;
            self.replaceClassButton('#trainingButtonB', 3500);
            self.replaceClassButton('#trainingButtonC', 3500);
            self.replaceClassButton('#trainingButtonA', 3500, true);
            self.addFiltersToCard(['#el4', '#el5'], self.layer0);
            self.addFiltersToCard(['#el2', '#el3'], self.layer1);
            // var tmp = self.layer0.findOne('#el5'),
            // console.log(self.layer0.findOne('#el5'));
        },
        buttonB: function () {
            var self = this;
            self.replaceClassButton('#trainingButtonA', 3500);
            self.replaceClassButton('#trainingButtonC', 3500);
            self.replaceClassButton('#trainingButtonB', 3500, true);
            self.addFiltersToCard(['#el0', '#el1'], self.layer2);
            self.addFiltersToCard(['#el4', '#el5'], self.layer0);
        },
        buttonC: function () {
            var self = this;
            self.replaceClassButton('#trainingButtonA', 3500);
            self.replaceClassButton('#trainingButtonB', 3500);
            self.replaceClassButton('#trainingButtonC', 3500, true);
            self.addFiltersToCard(['#el0', '#el1'], self.layer2);
            self.addFiltersToCard(['#el2', '#el3'], self.layer1);
        },
        replaceClassButton: function (buttonId, time, st) {
            if (st == false) {
                $(buttonId).removeClass('btn-success');
                $(buttonId).addClass('btn-danger');
                $(buttonId).prop('disabled', true);
            }
            if (st == true)
                $(buttonId).prop('disabled', true);

            setTimeout(function () {
                if (st == false) {
                    $(buttonId).removeClass('btn-danger');
                    $(buttonId).addClass('btn-success');
                    $(buttonId).prop('disabled', false);
                }
                if (st == true)
                    $(buttonId).prop('disabled', false);
            }, time);
        },
        addFiltersToCard: function (idEl, layer) {
            for (var i = 0, l = idEl.length; i < l; i++) {
                var tmp = layer.findOne(idEl[i]);
                tmp.cache();
                tmp.filters([Konva.Filters.RGBA]).alpha(0.6);
            }
            layer.batchDraw();
            setTimeout(function () {
                for (var i = 0, l = idEl.length; i < l; i++) {
                    var tmp = layer.findOne(idEl[i]);
                    tmp.filters([Konva.Filters.RGBA]).alpha(0);
                    layer.batchDraw();
                }
            }, 3500);
        }
    });
});