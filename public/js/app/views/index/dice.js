define([
        'views/view', 'konva'
    ],
    function (View, Konva) {
        return View.extend({
            elementCanvas: 'diceCanvas',
            elementCanvas1: 'container',
            events: {
                'click #buttonStart': 'start',
            },

            initialize: function (params) {
                this.buttonStatus = 0;
                this.params = params;
                this.startFrame = 0;
                this.template = this.getTemplate('window-dice');
                this.user = this.params.app.profile.user;
            },
            render: function (callback) {

                this.$el.html(this.template({
                    t: $.t,
                }));

                var self = this;
                _.defer(function () {
                    var height = Math.round($(document).height() / 1.35);
                    var width = height * 0.688;
                    var widthContainer = width * 0.8037634;
                    var heightContainer = height * 0.261631;
                    var widthElement = (width * 0.37837837838) / 1.5;
                    var heightElement = (height * 0.37768817204) / 1.5;
                    self.beginCoordinates = [];
                    self.endCoordinates = [];
                    self.nameFigury = ['bar','seven','diamant','horseshoe','heart','clubs','diamonds','spades','lemon','watermelon','cherry','bell'];
                    self.nameFigury = self.nameFigury.reverse();
                    var images = [];

                    $("#sizeDice").css( {
                        width: width,
                        height: height,
                        marginLeft: -(width / 2),
                    });
                    
                    var iKoef = heightElement * 0.802681862;

                    $("#resultDice").css( {
                        width: width * 0.67207758798,
                        height: height * 0.08185840707
                    });

                    $(".attempts").css( {
                        width: width * 0.13755933282,
                        height: height * 0.09464082098
                    });

                    var attempts = self.user.get('slot_attempt');
                    if (attempts > 0) {
                        while (attempts) {
                            $(".attempt_" + attempts).addClass('full');
                            attempts--;
                        }
                    }

                    var originWidth = $("#buttonStart").width();
                    var koefX = 0.38;
                    var koefY = 0.06870229008;
                    var scaleX = (width * koefX) / originWidth;
                    var scaleY = (height * koefY) / $("#buttonStart").height();
                    $("#buttonStart").css({
                      '-webkit-transform' : 'scaleX(' + scaleX + ') scaleY(' + scaleY + ')',
                      '-moz-transform'    : 'scaleX(' + scaleX + ') scaleY(' + scaleY + ')',
                      '-ms-transform'     : 'scaleX(' + scaleX + ') scaleY(' + scaleY + ')',
                      '-o-transform'      : 'scaleX(' + scaleX + ') scaleY(' + scaleY + ')',
                      'transform'         : 'scaleX(' + scaleX + ') scaleY(' + scaleY + ')',
                      'margin-left'       : (-originWidth / 2)
                    });

                    var slotMachine = self.getImage('sl-machine');

                    // var beginY = -4140;
                    var beginY = -(height * 7.275922671);
                    for (var i = 0; i < 12; i++) {
                        beginY += iKoef;
                        self.beginCoordinates[i] = beginY;
                    }
                    var endY = iKoef;
                    for (var i = 0; i < 12; i++) {
                        endY -= iKoef;
                        self.endCoordinates[i] = endY;
                    }

                    for (var i = 0; i < 12; i++) images[i] = self.getImage('el' + i);
                    self.loader.end();

                    self.stage = new Konva.Stage({
                        container: self.elementCanvas,
                        width: width,
                        height: height,
                    });
                    self.container = new Konva.Stage({
                        x: 0,
                        y: 0,
                        container: self.elementCanvas1,
                        width: widthContainer,
                        height: heightContainer,
                    });

                    var bgLayer = new Konva.Layer();

                    self.layerRow0 = new Konva.Layer({
                        id: 'layerRow0',
                        x: widthContainer * 0.01,
                        y: self.beginCoordinates[self.getRandomInt(0, 4)],
                    });

                    self.layerRow1 = new Konva.Layer({
                        id: 'layerRow1',
                        x: widthContainer * 0.34,
                        y: self.beginCoordinates[self.getRandomInt(4, 8)],
                    });

                    self.layerRow2 = new Konva.Layer({
                        id: 'layerRow2',
                        x: widthContainer * 0.68,
                        y: self.beginCoordinates[self.getRandomInt(8, 12)],
                    });

                    self.group0 = new Konva.Group({
                        id: 'group0',
                        x: 0,
                        y: 0,
                        width: 90,
                        // height: 4140,
                        height: height * 7.275922671,
                    });

                    var slotM = new Konva.Image({
                        id: 'diceCan',
                        x: 0,
                        y: 0,
                        width: width,
                        height: height,
                        image: slotMachine,
                    });

                    var positionY = -iKoef;
                    for (var j = 0; j < 3; j++) {
                        for (var i = 0; i < 12; i++) {
                            var element = new Konva.Image({
                                x: 0,
                                y: positionY += iKoef,
                                width: widthElement,
                                height: heightElement,
                                image: images[i],
                            });
                            self.group0.add(element);
                        };
                    };

                    self.group1 = self.group0.clone({
                        id: 'group1',
                    });

                    self.group2 = self.group0.clone({
                        id: 'group2',
                    });

                    bgLayer.add(slotM);
                    self.layerRow0.add(self.group0);
                    self.layerRow1.add(self.group1);
                    self.layerRow2.add(self.group2);

                    self.stage.add(bgLayer);
                    self.container.add(self.layerRow0).add(self.layerRow1).add(self.layerRow2);

                    $('[data-toggle="tooltip"]').tooltip();
                    self.resize();
                    callback();
                });

                this.listenTo(this.params.app, 'frame', function () {
                    if (self.startFrame >= 1) {
                        if (self.layerRow0.y() >= self.paramsSlMachine.yTween0 + 14) {
                            self.layerRow0.to({
                                y: self.paramsSlMachine.yTween0,
                                duration: 0.5,
                            });
                        };
                        if (self.layerRow1.y() >= self.paramsSlMachine.yTween1 + 14) {
                            self.layerRow1.to({
                                y: self.paramsSlMachine.yTween1,
                                duration: 0.5,
                            });
                        };
                        if (self.layerRow2.y() >= self.paramsSlMachine.yTween2 + 12) {
                            ++this.buttonStatus;
                            self.layerRow2.to({
                                y: self.paramsSlMachine.yTween2,
                                duration: 0.5,
                            });
                        };
                    }

                    return this;
                });

                this.listenTo(this.params.app, 'resize', function () {
                });


                this.$el.find('.modal').modal('show');
                this.$el.find('.modal').on('hidden.bs.modal', function (e) {
                    self.remove();
                });

                return this;
            },

            start: function () {
                var self = this;
                self.params.getResultSlMachine(function(data){
                    if (data.error) {
                        $("#resultDice td").text(data.error);
                    } else {
                        self.layerRow0.y(self.beginCoordinates[self.getRandomInt(0, 4)]);
                        self.layerRow2.y(self.beginCoordinates[self.getRandomInt(4, 8)]);
                        self.layerRow1.y(self.beginCoordinates[self.getRandomInt(8, 12)]);

                        var slMachine = self.user.get("slotMachine");

                        if (typeof slMachine !== "undefined") {
                            var attemptEl = $('#diceButton b');
                            var prev = attemptEl.text();
                            attemptEl.text(prev - 1);
                            $(".attempt_" + prev).removeClass('full');
                            self.user.set('slot_attempt', prev - 1);

                            ++self.startFrame;
                            self.paramsSlMachine = {
                                yTween0: self.endCoordinates[self.nameFigury.indexOf(slMachine["win"]["0"])],
                                yTween1: self.endCoordinates[self.nameFigury.indexOf(slMachine["win"]["1"])],
                                yTween2: self.endCoordinates[self.nameFigury.indexOf(slMachine["win"]["2"])],
                            };
                            setTimeout(function(){
                                if(slMachine["prize"] !== 0) {
                                    $("#resultDice td").addClass("brilliant-text blinking-text");
                                    var prize = slMachine["prize"] + '';
                                    var summ = prize.replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ');
                                    $("#resultDice td").text("You win " + summ + "$");

                                    var prevMoney = self.user.getObjectCount('chip');
                                    var money = self.countFilter(parseInt(prevMoney) + parseInt(prize));
                                    $('#chipsCount').text(money);
                                }
                                if(slMachine["prize"] === 0) $("#resultDice td").text("You lost!");

                                setTimeout(function(){
                                    $("#resultDice td").removeClass("brilliant-text blinking-text");
                                    $("#resultDice td").text("");
                                }, 2500);

                            }, 2500);

                            self.playSlotMachine(self.paramsSlMachine);
                        }
                    }
                });
            },

            buy: function (e) {
                var id = $(e.currentTarget).data('id');
                var self = this;
                this.params.buyBtn(id, 1, {
                    success: function () {
                        self.remove();
                    },
                    error: function (data) {
                        console.log('error', data);
                    }
                });
            },

            resize: function () {

            },
            getSize: function () {
                return {
                    w: $('#' + this.elementCanvas).width(),
                    h: $('#' + this.elementCanvas).height()
                };
            },
            isEmptyObject: function (obj) {
                for (var i in obj) {
                    return false;
                }
                return true;
            },
            getRandomInt: function (min, max) {
                return Math.floor(Math.random() * (max - min)) + min;
            },
            playSlotMachine: function (obj) {
                var self = this;
                var tween0 = new Konva.Tween({
                    node: self.layerRow0,
                    duration: 1.5,
                    y: self.paramsSlMachine.yTween0 + 15,
                    easing: Konva.Easings.StrongEaseOut,
                });

                var tween1 = new Konva.Tween({
                    node: self.layerRow1,
                    duration: 2,
                    y: self.paramsSlMachine.yTween1 + 15,
                    easing: Konva.Easings.StrongEaseOut,
                });

                var tween2 = new Konva.Tween({
                    node: self.layerRow2,
                    duration: 2.5,
                    y: self.paramsSlMachine.yTween2 + 13,
                    easing: Konva.Easings.StrongEaseOut,
                });

                tween0.play();
                tween1.play();
                tween2.play();

                $( "#buttonStart" ).addClass("disabled");
                setTimeout(function(){
                    $( "#buttonStart" ).removeClass("disabled");
                }, 5000);
            },

        });
    });