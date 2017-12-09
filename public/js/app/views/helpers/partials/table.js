define([
    'views/view',
    'konva',
    'gsap.KonvaPlugin',
    'TweenMax'
], function (View, Konva, KonvaPlugin, TweenMax) {
    return View.extend({
        animation: {},
        myId: 0,
        myPos: false,
        alert: 0,
        startBetTimer: 0,
        // konva global settings
        perfectDraw: true,
        // width settings
        origWidth: 0,
        origHeight: 0,
        chairBgWidth: 0,
        chairBgHeight: 0,
        chairTimer: null,
        chairPos: {
            1: {x: 1325, y: 200},
            2: {x: 1780, y: 400},
            3: {x: 1825, y: 810},
            4: {x: 1760, y: 1199},
            5: {x: 1240, y: 1255},
            6: {x: 635, y: 1255},
            7: {x: 80, y: 1199},
            8: {x: 0, y: 810},
            9: {x: 50, y: 400},
            10: {x: 525, y: 200}
        },
        cardOrginWidth: 366,
        cardOrginHeight: 520,
        cardWidth: 146,
        cardHeight: 208,
        cardUserWidth: 102,
        cardUserHeight: 136,
        cards: {},
        cardsPos: {
            1: {x: 730, y: 783}
        },
        chipWidth: 70,
        chipHeight: 70,
        chips: {
            'chip-1': {x: 5, y: 5},
            'chip-10': {x: 85, y: 5},
            'chip-100': {x: 5, y: 85},
            'chip-2': {x: 85, y: 85},
            'chip-200': {x: 165, y: 5},
            'chip-5': {x: 165, y: 85},
            'chip-50': {x: 5, y: 165},
            'chip-500': {x: 85, y: 165}
        },
        chipsPos: {
            1: {x: 1440, y: 614},
            2: {x: 1610, y: 712},
            3: {x: 1670, y: 885},
            4: {x: 1653, y: 1090},
            5: {x: 1354, y: 1150},
            6: {x: 768, y: 1150},
            7: {x: 469, y: 1090},
            8: {x: 368, y: 914},
            9: {x: 465, y: 712},
            10: {x: 663, y: 614},
            allbank: {x: 1045, y: 658}
        },
        dealerPos: {
            1: {x: 1377, y: 691},
            2: {x: 1552, y: 741},
            3: {x: 1632, y: 898},
            4: {x: 1610, y: 1033},
            5: {x: 1304, y: 1136},
            6: {x: 869, y: 1136},
            7: {x: 589, y: 1033},
            8: {x: 505, y: 898},
            9: {x: 565, y: 741},
            10: {x: 744, y: 691}
        },
        events: {},
        initialize: function (params) {
            this.params = params;
        },
        render: function () {
            this.cards = this.getCards();
            this.my_id = this.params.user.get('id');

            var tableImg = this.getImage('table');
            var chairImg = this.getImage('chair');
            var dealerImg = this.getImage('dealer');

            var w = $(this.params.el).width();
            var h = $(this.params.el).height();

            this.origWidth = tableImg.naturalWidth + 235 * 2;
            this.origHeight = tableImg.naturalHeight + 261;
            this.chairBgWidth = Math.round(chairImg.naturalWidth * 2.5);
            this.chairBgHeight = Math.round(chairImg.naturalHeight * 3);

            var ratio = this.origWidth / this.origHeight;
            var height = Math.round(Math.min(h, w / ratio));
            var width = Math.round(height * ratio);
            ratio = width / this.origWidth;

            this.stage = new Konva.Stage({
                container: this.params.el,
                width: this.origWidth,
                height: this.origHeight,
                draggable: false
            });

            this.layerTimer = new Konva.FastLayer();
            this.layer = new Konva.Layer();

            var table = new Konva.Image({
                id: 'table',
                width: this.origWidth,
                height: this.origHeight,
                image: tableImg,
                transformsEnabled: 'position',
                fillEnabled: false,
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            table.cache();
            this.layerTimer.add(table);

            for (var i in this.chairPos) {
                i = +i;
                var item = this.chairPos[i];
                var chairBgGroup = new Konva.Group({
                    id: 'chairBgGroup-' + i,
                    name: 'chairBgGroup',
                    x: item.x,
                    y: item.y,
                    width: this.chairBgWidth,
                    height: this.chairBgHeight
                });
                var chairBg = new Konva.Rect({
                    id: 'chairBg-' + i,
                    name: 'chairBg empty',
                    width: this.chairBgWidth,
                    height: this.chairBgHeight,
                    fill: 'rgba(0, 0, 0, 0.4)',
                    cornerRadius: 10,
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false
                });
                var chair = new Konva.Image({
                    id: 'chair-' + i,
                    name: 'chair',
                    x: Math.round((this.chairBgWidth - chairImg.naturalWidth) / 2),
                    y: Math.round((this.chairBgHeight - chairImg.naturalHeight) / 2),
                    width: chairImg.naturalWidth,
                    height: chairImg.naturalHeight,
                    image: chairImg,
                    transformsEnabled: 'position',
                    fillEnabled: false,
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                });
                var chairBgText = new Konva.Text({
                    id: 'chairBgText-' + i,
                    name: 'chairBgText',
                    text: _.template.t('poker.sit'),
                    fontFamily: 'Arial',
                    fontSize: 40,
                    fill: '#9E9E9E',
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                });
                chairBgText.x(chair.x() + Math.round((chairImg.naturalWidth / 2 - chairBgText.getWidth() / 2)));
                chairBgText.y(chair.y() + Math.round((chairImg.naturalHeight / 2 - chairBgText.getHeight() / 2)) - 22);
                chairBg.cache();
                chair.cache();
                chairBgText.cache();
                chairBgGroup.add(chairBg).add(chair).add(chairBgText);
                this.layer.add(chairBgGroup);
            }

            // add dealer chip
            var dealer = new Konva.Image({
                name: 'dealer',
                width: this.chipWidth,
                height: this.chipHeight,
                image: dealerImg,
                visible: false,
                transformsEnabled: 'position',
                fillEnabled: false,
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            }).cache();
            this.layer.add(dealer);

            this.addGlobalShapes();
            this.addCardShapes();
            this.addLayerEvents();

            this.stage.add(this.layerTimer).add(this.layer);

            var steps = Math.ceil(Math.log(this.origWidth / width) / Math.log(2));
            var roundRatio = ratio * steps;
            for (var i = 1; i <= steps; i++) {
                this.stage.scale({x: roundRatio / i, y: roundRatio / i});
                this.stage.draw();
            }
            this.stage.x(Math.round((w - this.origWidth * ratio) / 2));
            this.stage.y(Math.round((h - this.origHeight * ratio) / 2));
            this.stage.draw();
            return this;
        },
        addLayerEvents: function () {
            var self = this;
            this.layer.on('mouseover', function (e) {
                self.params.el.style.cursor = 'pointer';
                var target = e.target;
                if (target.hasName('chairBg')) {
                    var id = target.attrs.id;
                    var num = id.split('-')[1];
                    var text = self.layer.findOne('#chairBgText-' + num);
                    target.clearCache();
                    target.fill('rgba(0, 0, 0, 0.6)');
                    text.clearCache();
                    text.fill('#fff');
                    text.cache();
                    target.cache();
                    self.stage.batchDraw();
                }
            });
            this.layer.on('mouseout', function (e) {
                self.params.el.style.cursor = 'default';
                var target = e.target;
                if (target.hasName('chairBg')) {
                    var id = target.attrs.id;
                    var num = id.split('-')[1];
                    var text = self.layer.findOne('#chairBgText-' + num);
                    target.clearCache();
                    target.fill('rgba(0, 0, 0, 0.4)');
                    text.clearCache();
                    text.fill('#9E9E9E');
                    text.cache();
                    target.cache();
                    self.stage.batchDraw();
                }
            });
        },
        beforeJoin: function (position, id, balance) {
            var self = this;
            var chairBg = this.stage.findOne('#chairBg-' + position);
            var chair = this.stage.findOne('#chair-' + position);
            var chairBgText = this.stage.findOne('#chairBgText-' + position);
            if (chairBg) {
                chairBg.clearCache();
                chairBg.fill('rgba(0, 0, 0, 1)');
                chairBg.removeName('empty').listening(false);
                chairBg.cache();

                if (parseInt(id) === parseInt(this.my_id)) {
                    var money = this.params.user.getObjectCount('chip') - balance;
                    this.params.user.objectList.set([
                        {code: 'chip', count: money}
                    ]);

                    var items = this.stage.find('.empty');
                    items.each(function (item) {
                        item.listening(false);
                        var id = item.id();
                        var pos = id.split('-')[1];
                        var text = self.stage.findOne('#chairBgText-' + pos);
                        text.hide();
                    });
                }

            }
            if (chair) {
                chair.hide();
            }
            if (chairBgText) {
                chairBgText.hide();
            }
        },
        join: function (player, position) {
            var self = this;
            var photo = new Konva.Shape({
                id: 'photo-' + position,
                width: 331,
                height: 285,
                x: 1,
                y: 53,
                sceneFunc: function (context) {
                    var x = 0, y = 0, width = 331, height = 285;
                    var radius = {tl: 0, tr: 0, br: 10, bl: 10};
                    context.beginPath();
                    context.moveTo(x + radius.tl, y);
                    context.lineTo(x + width - radius.tr, y);
                    context.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
                    context.lineTo(x + width, y + height - radius.br);
                    context.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
                    context.lineTo(x + radius.bl, y + height);
                    context.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
                    context.lineTo(x, y + radius.tl);
                    context.quadraticCurveTo(x, y, x + radius.tl, y);
                    context.closePath();
                    context.fillStrokeShape(this);
                },
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: self.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            var name = new Konva.Text({
                id: 'name-' + position,
                x: 12,
                y: 4,
                width: 309,
                height: 45,
                text: _.template.escapeHtml(player.info['social_name']),
                fontSize: 42,
                fontFamily: 'Arial',
                fill: '#fff',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: self.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false
            });
            name.on('click', function () {
                window.open(player.info['social_link'], '_blank');
            });
            name.on('mouseover', function () {
                document.getElementById('tablepoker').style.cursor = 'pointer';
                this.fill('#0BB9CE');
                self.layer.batchDraw();
            });
            name.on('mouseout', function () {
                document.getElementById('tablepoker').style.cursor = 'default';
                this.fill('#fff');
                self.layer.batchDraw();
            });
            var chip = new Konva.Image({
                id: 'chip_s-' + position,
                x: 5,
                y: 300,
                width: 24,
                height: 24,
                image: self.getImage('chip-s'),
                transformsEnabled: 'position',
                fillEnabled: false,
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: self.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            var text = new Konva.Text({
                id: 'count_chip-' + position,
                x: 37,
                y: 292,
                text: self.countFilter(player.balance),
                fontSize: 40,
                fontFamily: 'Arial',
                fill: '#fff',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: self.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            var bgWidth = 5 + 32 + 5 + text.getWidth() + 7;
            var bg = new Konva.Shape({
                id: 'bg_count_chip-' + position,
                width: bgWidth,
                height: 53,
                x: 1,
                y: 285,
                sceneFunc: function (context) {
                    var x = 0, y = 0, width = bgWidth, height = 53;
                    var radius = {tl: 0, tr: 10, br: 0, bl: 10};
                    context.beginPath();
                    context.moveTo(x + radius.tl, y);
                    context.lineTo(x + width - radius.tr, y);
                    context.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
                    context.lineTo(x + width, y + height - radius.br);
                    context.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
                    context.lineTo(x + radius.bl, y + height);
                    context.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
                    context.lineTo(x, y + radius.tl);
                    context.quadraticCurveTo(x, y, x + radius.tl, y);
                    context.closePath();
                    context.fillStrokeShape(this);
                },
                fill: 'rgba(0, 0, 0, 0.7)',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: self.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            bg.cache();
            chip.cache();

            var textLabel = new Konva.Label({
                id: 'textLabel-' + position,
                visible: false,
                listening: false
            });
            textLabel.add(new Konva.Tag({
                fill: 'rgba(0, 0, 0, 0.4)',
                listening: false
            }));
            var textShape = new Konva.Text({
                id: 'textShape-' + position,
                width: this.chairBgWidth,
                padding: 5,
                text: 'Fold',
                fontFamily: 'Arial',
                fontSize: 40,
                align: 'center',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            textLabel.add(textShape);
            var winSum = new Konva.Text({
                id: 'winSum-' + position,
                x: 10,
                text: '',
                fontFamily: 'Arial',
                fontSize: 36,
                fill: '#FFEB3B',
                visible: false,
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });

            var group = self.layer.findOne('#chairBgGroup-' + position);
            group.add(name).add(photo).add(bg).add(chip).add(text);

            var c = 1;
            while (c < 3) {
                var card = new Konva.Rect({
                    id: 'hand-card-' + c,
                    name: 'hand-cards grayscale hand-card-' + c,
                    x: (c > 1 ? 236 : 166) + self.cardUserWidth / 2,
                    y: 210 + self.cardUserHeight / 2,
                    width: self.cardUserWidth,
                    height: self.cardUserHeight,
                    offset: {
                        x: self.cardUserWidth / 2,
                        y: self.cardUserHeight / 2
                    },
                    fillPatternScale: {
                        x: self.cardUserWidth / self.cardOrginWidth,
                        y: self.cardUserHeight / self.cardOrginHeight
                    },
                    scaleX: -1,
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: self.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                }).cache();
                group.add(card);
                c++;
            }

            group.add(textLabel).add(winSum);
            self.layer.batchDraw();

            var img = new Image();
            img.onload = function () {
                photo.fillPatternImage(img);
                photo.cache();
                self.layer.batchDraw();
            };
            img.crossOrigin = 'Anonymous';
            img.src = 'https://graph.facebook.com/' + player.info['social_id'] + '/picture?width=' + 300 + '&height=' + 300;
        },
        leave: function (position, leavetype, money, userPos, seatCallback) {
            var self = this;

            var chairBg = this.stage.findOne('#chairBg-' + position);
            var chair = this.stage.findOne('#chair-' + position);
            var chairBgText = this.stage.findOne('#chairBgText-' + position);
            var chairBgGroup = chairBg.getParent();
            var photo = chairBgGroup.findOne('#photo-' + position);
            var name = chairBgGroup.findOne('#name-' + position);
            var chip_s = chairBgGroup.findOne('#chip_s-' + position);
            var count_chip = chairBgGroup.findOne('#count_chip-' + position);
            var bg_count_chip = chairBgGroup.findOne('#bg_count_chip-' + position);
            var cards = chairBgGroup.find('.hand-cards');
            var textLabel = chairBgGroup.find('#textLabel-' + position);
            var winSum = chairBgGroup.find('#winSum-' + position);

            var f = function () {
                if (photo) {
                    photo.clearCache().destroy();
                }
                if (name) {
                    name.destroy();
                }
                if (bg_count_chip) {
                    bg_count_chip.clearCache().destroy();
                }
                if (chip_s) {
                    chip_s.clearCache().destroy();
                }
                if (count_chip) {
                    count_chip.destroy();
                }
                cards.each(function (card) {
                    card.destroy();
                });
                if (textLabel) {
                    textLabel.destroy();
                }
                if (winSum) {
                    winSum.destroy();
                }
                if (chair) {
                    chair.show();
                }
                if (chairBg) {
                    chairBg.clearCache();
                    chairBg.fill('rgba(0, 0, 0, 0.4)');
                }

                if (self.myPos !== false) {
                    if (chairBg) {
                        chairBg.listening(false);
                        chairBg.cache();
                    }
                } else {
                    if (chairBg) {
                        chairBg.listening(true);
                        chairBg.addName('empty');
                        chairBg.cache();
                    }
                    if (chairBgText) {
                        chairBgText.show();
                    }
                }

                if (self.myPos !== false && userPos === false) {
                    self.myPos = false;
                    if (chairBg) {
                        chairBg.addName('empty');
                        chairBg.cache();
                        var items = self.stage.find('.chairBg');
                        items.each(function (item) {
                            if (item.hasName('empty')) {
                                item.listening(true);
                                var id = item.id();
                                var pos = id.split('-')[1];
                                var text = self.stage.findOne('#chairBgText-' + pos);
                                text.show();
                            }
                        });
                        self.params.user.objectList.set([
                            {
                                code: 'chip',
                                count: parseInt(self.params.user.getObjectCount('chip')) + parseInt(money)
                            }
                        ]);
                    }
                }

                self.layer.batchDraw();
            };
            if (leavetype == 'no_money' || leavetype == 'timeout') {
                setTimeout(function () {
                    var myPos = self.myPos;

                    f();

                    if (myPos !== false && userPos === false) {
                        var userId = self.params.user.get('id');
                        self.params.user.set('room_id', null);
                        $('.btn-standup-' + userId).fadeOut();
                        $('.btn-tolobby-' + userId).removeClass('disabled');
                        if (leavetype == 'no_money') {
                            var chips = self.params.user.getObjectCount('chip');
                            if (parseInt(chips)) {
                                setTimeout(function () {
                                    seatCallback(position);
                                }, 500);
                            } else {
                                setTimeout(function () {
                                    self.params.openWindow('shop', {
                                        buyEnd: seatCallback.bind(null, position)
                                    });
                                }, 500);
                            }
                        }
                    }
                }, 4000);
            } else {
                f();
            }
        },
        changeDealerPosition: function (newposition) {
            var dealer = this.layer.findOne('.dealer');
            var dealerPos = this.dealerPos[newposition];
            if (!dealer.isVisible()) {
                dealer.clearCache().x(dealerPos.x).y(dealerPos.y).visible(true).cache();
                this.layer.batchDraw();
            } else {
                dealer.to({
                    x: dealerPos.x,
                    y: dealerPos.y,
                    duration: 0.1
                });
            }
        },
        hideDealer: function () {
            var dealer = this.layer.findOne('.dealer');
            dealer.to({
                opacity: 0,
                duration: 0.4
            });
        },
        addText: function (position, textType) {
            var self = this;
            var color = '#fff';
            if (textType == 'win') {
                color = '#FFEB3B';
            } else if (textType == 'fold') {
                color = '#FF5722';
            } else if (textType == 'timeout') {
                color = '#C12D0D';
            } else if (textType == 'call' || textType == 'check') {
                color = '#2196F3';
            } else if (textType == 'raise' || textType == 'allin') {
                color = '#CDDC39';
            }
            var chairBgGroup = this.layer.findOne('#chairBgGroup-' + position);
            var textLabel = chairBgGroup.findOne('#textLabel-' + position);
            var textShape = chairBgGroup.findOne('#textShape-' + position);
            textShape.setText(textType);
            textShape.fill(color);
            textLabel.y(Math.round((chairBgGroup.height() / 2 - textShape.getHeight() / 2)));
            textLabel.show();
            this.layer.batchDraw();
            setTimeout(function () {
                textLabel.hide();
                self.layer.batchDraw();
            }, 2000);
        },
        addTextWinMoney: function (position, money) {
            var self = this;
            var chairBgGroup = this.layer.findOne('#chairBgGroup-' + position);
            var winSum = chairBgGroup.findOne('#winSum-' + position);
            winSum.setText('+' + money);
            winSum.y(this.chairBgHeight + winSum.getHeight() / 2);
            winSum.show();
            this.layer.batchDraw();

            setTimeout(function () {
                winSum.hide();
                self.layer.batchDraw();
            }, 1500);
        },
        showWelcomeAlert: function (text) {
            if (typeof (text) == 'undefined') {
                text = _.template.t('poker.pls_wait_nxt_hand');
            }
            if (!this.alert) {
                this.alert = new Konva.Label({
                    y: 1020,
                    listening: false
                });
                this.alert.add(new Konva.Tag({
                    fill: 'rgba(0, 0, 0, 0.8)',
                    listening: false
                }));
                this.alertText = new Konva.Text({
                    name: 'alertText',
                    padding: 20,
                    fill: '#fff',
                    text: text,
                    fontFamily: 'Arial',
                    fontSize: 45,
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                });
                this.alert.x(this.origWidth / 2 - (this.alertText.width() - 10) / 2);
                this.alert.add(this.alertText);
                this.layer.add(this.alert);
            }
        },
        updateAlertText: function (text) {
            if (!this.alert) {
                this.showWelcomeAlert(text);
            } else {
                this.alert.hide();
                this.alertText.setText(text);
                this.alert.x(this.origWidth / 2 - (this.alertText.width() - 10) / 2);
                this.alert.show();
                this.layer.batchDraw();
            }
        },
        hideWelcomeAlert: function () {
            if (this.alert) {
                this.alert.hide();
                this.layer.batchDraw();
            }
        },
        updatePlayerBalance: function (position, newValue) {
            var chip = this.layer.findOne('#count_chip-' + position);
            if (chip) {
                chip.setText(this.countFilter(newValue));
                this.layer.batchDraw();
            }
        },
        resetHand: function (position) {
            var group = this.layer.findOne('#chairBgGroup-' + position);
            var cards = group.find('.hand-cards');
            cards.each(function (card) {
                card.scaleX(-1);
                card.fillPatternImage(null);
                card.fillPatternOffset({
                    x: null,
                    y: null
                });
            });
            this.layer.batchDraw();
        },
        addCards: function (cards, position, callback) {
            var self = this;
            var shapes = [];

            if (position !== undefined && position !== null) {
                var group = this.layer.findOne('#chairBgGroup-' + position);
                shapes = group.find('.hand-cards');
            } else {
                shapes = this.layer.find('.cards');
            }
            shapes.each(function (shape) {
                if (!shape.fillPatternOffsetX()) {
                    shape.clearCache();
                    shape.fillPatternImage(self.cardsImg);
                    shape.fillPatternOffset({x: 2945, y: 2089});
                    shape.cache();
                }
            });

            if (shapes.length) {
                this.layer.batchDraw();

                var i = 0;
                var f = function (i) {
                    if (cards[i] !== undefined) {
                        if (cards[i].suit !== undefined && cards[i].value !== undefined) {
                            setTimeout(t.bind(null, i), 150 + i * 50);
                        }
                        i++;
                        if (i == cards.length) {
                            if (typeof (callback) == 'function') {
                                callback();
                            }
                        } else {
                            f(i);
                        }
                    }
                };
                var t = function (i) {
                    self.showCard(shapes[i], cards[i]);
                };
                f(i);
            }
        },
        showCard: function (shape, card, index, callback) {
            var name = 'card-' + card['value'] + '-' + card['suit'];
            var cards = this.cards[name];
            var patternOffset = shape.fillPatternOffset();

            if (patternOffset.x != cards.x || patternOffset.y != cards.y) {
                var self = this;

                var tween = TweenMax.to(shape, 0.8, {
                    scaleX: 1,
                    ease: 'Linear.easeInOut',
                    onUpdate: function () {
                        if (tween && tween.time() > 0.39) {
                            shape.clearCache();
                            shape.fillPatternOffset({x: cards.x, y: cards.y});
                            shape.cache();
                        }
                        self.layer.batchDraw();
                    },
                    onComplete: function () {
                        tween.kill();
                    }
                });
            }
        },
        timerSeat: function (time, position, timeDiff, callback) {
            timeDiff = time + timeDiff;
            if (timeDiff > time) {
                return false;
            }

            var self = this;

            var chairBgGroup = this.layer.findOne('#chairBgGroup-' + position);
            if (typeof (chairBgGroup) == 'undefined') {
                return false;
            }
            this.chairTimer.position({
                x: chairBgGroup.x() - 15,
                y: chairBgGroup.y() - 15
            });
            this.layerTimer.add(this.chairTimer);

            var params = {};
            params.context = this.canvasTimer.getContext('2d');
            params.context.lineWidth = 170;
            params.w = this.canvasTimer.width;
            params.h = this.canvasTimer.height;
            params.x = params.w / 2;
            params.y = params.h / 2;
            params.radius = 170;
            params.curPerc = 0;
            params.circ = Math.PI * 2;
            params.quart = Math.PI / 2;
            params.colors = ['#8BC34A', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722', '#F44336'];

            timeDiff = Math.max(0, timeDiff);
            params.timeStart = this.params.app.getTimeNow() - timeDiff;
            params.timeEnd = params.timeStart + time;
            params.chipsTime = 100;

            var animate = function (params) {
                var timeNow = self.params.app.getTimeNow();

                params.context.clearRect(0, 0, params.w, params.h);
                params.context.beginPath();
                params.curPerc = Math.min(100, Math.max(0, (timeNow - params.timeStart) / (params.timeEnd - params.timeStart) * 100));
                var current = params.curPerc / 100;
                params.context.arc(params.x, params.y, params.radius, -(params.quart), ((params.circ) * current) - params.quart, false);
                var colorIndex = Math.floor(params.curPerc / (100 / params.colors.length));
                if (params.colors[colorIndex] !== undefined) {
                    params.context.strokeStyle = params.colors[colorIndex];
                    params.context.stroke();
                    self.layerTimer.batchDraw();
                }
                if (params.curPerc >= params.chipsTime) {
                    delete self.animation['timer'];
                    self.chairTimer.remove();
                    self.layerTimer.batchDraw();
                }
            };

            this.layerTimer.draw();

            self.animation['timer'] = {
                'params': params,
                'callback': animate
            };
        },
        timerRemove: function () {
            var parent = this.chairTimer.getParent();
            if (parent) {
                this.chairTimer.remove();
            }
        },
        getChipTypes: function (value) {
            var nominals = [1, 2, 5, 10, 50, 100, 200, 500];
            nominals.sort(function (a, b) {
                return b - 1;
            });
            var types = [];
            for (var i in nominals) {
                var nominal = nominals[i];
                if (nominal < value) {
                    value -= nominal;
                    types[types.length] = {value: nominal, pos: types.length % 3 + 1};
                } else if (nominal == value) {
                    types[types.length] = {value: nominal, pos: types.length % 3 + 1};
                    break;
                }
                if (value <= 0 || types.length == 10) {
                    break;
                }
            }
            return types;
        },
        getChipsStr: function (value, group) {
            var chipsImg = this.getImage('chips');

            var positions = {
                1: {
                    1: [0, 0],
                    2: [10, -5],
                    3: [-10, 10]
                }
            };
            var positionCounts = {};
            var stepY = 3;

            var type = this.getChipTypes(value);
            for (var i in type) {
                if (positionCounts[type[i].pos] === undefined) {
                    positionCounts[type[i].pos] = 0;
                }
                positionCounts[type[i].pos]++;
                var left = positions['1'][type[i].pos][0];
                var top = positions['1'][type[i].pos][1] + positionCounts[type[i].pos] * stepY;
                var chip = new Konva.Rect({
                    name: 'chip_type_' + type[i].value,
                    x: left,
                    y: top,
                    width: this.chipWidth,
                    height: this.chipHeight,
                    fillPatternImage: chipsImg,
                    fillPatternOffset: {
                        x: this.chips['chip-' + type[i].value].x,
                        y: this.chips['chip-' + type[i].value].y
                    },
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                });
                chip.cache();
                group.add(chip);
            }

            return group;
        },
        addChips: function (position, value) {
            var chip_sImg = this.getImage('chip-s');

            var item = this.chipsPos[position];

            var group = new Konva.Group({
                id: 'chipGroup-' + position,
                name: ((position != 'allbank' && position != 'bank') ? 'hands' : 'bank'),
                x: item.x,
                y: item.y,
                width: this.chipWidth,
                height: this.chipHeight,
                transformsEnabled: 'position',
                listening: false
            });

            group = this.getChipsStr(value, group);

            var group_wrap = new Konva.Group({
                transformsEnabled: 'position',
                listening: false
            });
            var chip_money_wrap = new Konva.Rect({
                name: 'chip-money-wrap',
                x: 17,
                width: 50,
                height: 43,
                cornerRadius: 5,
                fill: 'rgba(205, 220, 57, 0.9)',
                shadowColor: 'black',
                shadowBlur: 3,
                shadowOffset: {x: 0, y: 0},
                shadowOpacity: 0.85,
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                dashEnabled: false,
                listening: false
            });
            var chip_s = new Konva.Image({
                x: 20,
                y: 10,
                width: 24,
                height: 24,
                image: chip_sImg,
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            var chip_money = new Konva.Text({
                name: 'chip-money',
                x: 50,
                y: 4,
                text: this.countFilter(value),
                fontFamily: 'Arial',
                fontSize: 36,
                fontStyle: 'bold',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
            chip_money_wrap.width(42 + chip_money.width());
            chip_s.cache();
            group_wrap.add(chip_money_wrap).add(chip_s).add(chip_money);
            group.add(group_wrap);
            this.layer.add(group);
            chip_money_wrap.cache().draw();
            return group;
        },
        addFlyChips: function (uniq, x, y, value) {
            var group = new Konva.Group({
                id: 'chipGroup-' + uniq,
                x: x,
                y: y,
                width: this.chipWidth,
                height: this.chipHeight,
                transformsEnabled: 'position',
                listening: false
            });
            group = this.getChipsStr(value, group);
            this.layer.add(group);
            return group;
        },
        showWin: function (banks, hands, users) {
            var self = this;
            var card;

            var showWinCards = function (wins, callback) {
                setTimeout(function () {
                    for (var position in wins['positions']) {
                        var win = wins['positions'][position];
                        for (var i in win['public']) {
                            card = self.layer.findOne('#card-' + win['public'][i]);
                            card.removeName('grayscale');
                            card.to({
                                y: card.y() - 50,
                                duration: 0.4
                            });
                        }
                        var group = self.layer.findOne('#chairBgGroup-' + position);
                        for (var i in win['private']) {
                            card = group.findOne('.hand-card-' + win['private'][i]);
                            card.removeName('grayscale');
                            card.to({
                                y: card.y() - 50,
                                duration: 0.4
                            });
                        }

                        var cards = self.layer.find('.grayscale');
                        cards.each(function (card) {
                            card.filters([Konva.Filters.Grayscale]);
                            self.layer.batchDraw();
                        });

                        var winShape = self.chairTimer.clone({
                            fillPatternImage: null,
                            fill: '#F0D200'
                        });
                        winShape.position({
                            x: group.x() - 15,
                            y: group.y() - 15
                        });
                        self.layerTimer.add(winShape);
                        var tween = TweenMax.to(winShape, 1, {
                            konva: {opacity: 0.5},
                            repeat: -1,
                            yoyo: true,
                            ease: 'Linear.easeInOut',
                            onUpdate: function () {
                                self.layer.batchDraw();
                            }
                        });
                        setTimeout(function () {
                            tween.kill();
                            winShape.destroy();
                            self.layerTimer.batchDraw();
                        }, 3000);

                        self.addText(position, 'win');
                        self.updateAlertText(_.template.t(win['ranking']));
                    }
                    self.layer.batchDraw();
                    callback();
                }, 400);
            };

            var hideWinCards = function (wins, callback) {
                setTimeout(function () {
                    for (var position in wins['positions']) {
                        var cards = self.layer.find('.grayscale');
                        cards.each(function (card) {
                            card.filters(null);
                            self.layer.batchDraw();
                        });

                        var win = wins['positions'][position];
                        for (var i in win['public']) {
                            card = self.layer.findOne('#card-' + win['public'][i]);
                            card.addName('grayscale');
                            card.to({
                                y: card.y() + 50,
                                duration: 0.4
                            });
                        }
                        var group = self.layer.findOne('#chairBgGroup-' + position);
                        for (var i in win['private']) {
                            card = group.findOne('.hand-card-' + win['private'][i]);
                            card.addName('grayscale');
                            card.to({
                                y: card.y() + 50,
                                duration: 0.4
                            });
                        }
                        var textLabel = self.layer.findOne('#textLabel-' + position);
                        textLabel.hide();
                        self.hideWelcomeAlert();
                    }
                    self.layer.batchDraw();
                    callback();
                }, 400);
            };

            var size = Object.keys(banks).length;
            var sumbank = 0;
            for (var i in banks) {
                sumbank += banks[i]['bank'];
            }

            var f = function (i) {
                if (banks[i] !== undefined) {
                    showWinCards(banks[i], function () {
                        self.getBankToPlayer(sumbank, banks[i], users, function () {
                            sumbank -= banks[i]['bank'];
                            hideWinCards(banks[i], function () {
                                if (++i == size) {
                                    self.winTimeout = setTimeout(function () {
                                        self.hideCards();
                                        self.hideDealer();
                                    }, 1000);
                                } else {
                                    f(i);
                                }
                            });
                        }, (i + 1 == size));
                    });
                }
            };

            self.showCards(hands, users, function () {
                f(0);
            });
        },
        getBankToPlayer: function (sumbank, bank, users, callback, isLast) {
            var self = this;
            var bankNew = sumbank - bank['bank'];
            if (isLast) {
                bankNew = bank['bank'];
            }
            self.updateBank(bankNew, function () {
                var i = 0;
                var size = Object.keys(bank['positions']).length;
                for (var position in bank['positions']) {
                    i++;
                    var money = bank['positions'][position]['money'];
                    sumbank -= money;
                    self.win(sumbank, money, position, users, (isLast && i == size) ? true : false);
                }
                setTimeout(function () {
                    callback();
                }, 2000);
            });
        },
        updateBank: function (sum, callback, removeHands) {
            removeHands = (removeHands == undefined) ? true : removeHands;
            var self = this;
            if (sum == 0) {
                return false;
            }
            var f = function () {
                var bankGroup = self.layer.findOne('#chipGroup-allbank');
                if (!bankGroup) {
                    self.addChips('allbank', sum);
                    //Sound.play('poker', 'createBank');
                } else {
                    var chip_money_wrap = bankGroup.findOne('.chip-money-wrap');
                    var chipMoney = bankGroup.findOne('.chip-money');
                    chipMoney.setText(sum);
                    chip_money_wrap.clearCache()
                            .width(42 + chipMoney.width())
                            .cache();
                    self.layer.batchDraw();
                    //Sound.play('poker', 'updateBank');
                }
                if (removeHands) {
                    var hands = self.layer.find('.hands');
                    hands.each(function (hand) {
                        hand.destroy();
                    });
                }
                if (typeof (callback) == 'function') {
                    callback();
                }
            };
            if (removeHands) {
                var h = function () {
                    var bankPos = self.chipsPos['allbank'];
                    var hands = self.layer.find('.hands');
                    hands.each(function (hand) {
                        var chip_money_wrap = hand.findOne('.chip-money-wrap');
                        if (chip_money_wrap) {
                            chip_money_wrap.getParent().destroy();
                        }
                        hand.to({
                            x: bankPos.x,
                            y: bankPos.y,
                            duration: 0.4
                        });
                    });
                };
                if (self.startBetTimer) {
                    var time = 450 - ((new Date()).getTime() - self.startBetTimer);
                    setTimeout(function () {
                        h();
                        self.startBetTimer = 0;
                        setTimeout(function () {
                            f();
                        }, 400);
                    }, time);
                } else {
                    h();
                    setTimeout(function () {
                        f();
                    }, 400);
                }
            } else {
                f();
            }
            return this;
        },
        showCards: function (hands, users, callback) {
            var self = this;
            var group, shape;
            var t = function (shape, cards) {
                var tween = TweenMax.to(shape, 0.8, {
                    scaleX: 1,
                    ease: 'Linear.easeInOut',
                    onUpdate: function () {
                        if (tween.time() > 0.39) {
                            shape.clearCache();
                            shape.fillPatternOffset({x: cards.x, y: cards.y});
                            shape.cache();
                        }
                        self.layer.batchDraw();
                    },
                    onComplete: function () {
                        tween.kill();
                    }
                });
            };
            setTimeout(function () {
                for (var position in hands) {
                    if (users[position] !== undefined
                            && users[position].info['id'] != self.my_id
                            ) {
                        group = self.layer.findOne('#chairBgGroup-' + position);
                        for (var i in hands[position]) {
                            var card = hands[position][i];
                            var name = 'card-' + card['value'] + '-' + card['suit'];
                            var cards = self.cards[name];
                            var index = parseInt(i) + 1;
                            shape = group.findOne('.hand-card-' + index);
                            setTimeout(t.bind(null, shape, cards), 150 + i * 50);
                        }
                    }
                }

                setTimeout(function () {
                    callback();
                }, 400);
            }, 400);
        },
        hideCards: function () {
            var shapes;
            shapes = this.layer.find('.hand-cards');
            shapes.each(function (shape) {
                shape.clearCache();
                shape.fillPatternImage(null);
                shape.fillPatternOffset({x: 0, y: 0});
                shape.scaleX(-1);
                shape.cache();
            });
            shapes = this.layer.find('.cards');
            shapes.each(function (shape) {
                shape.clearCache();
                shape.fillPatternImage(null);
                shape.fillPatternOffset({x: 0, y: 0});
                shape.scaleX(-1);
                shape.cache();
            });

            this.layer.batchDraw();
        },
        win: function (bank, money, position, users, ifLast) {
            var self = this;
            var player = users[position];
            var chair = this.layer.findOne('#chair-' + position);
            var chairBgGroup = chair.getParent();
            var pos = {
                x: chairBgGroup.x() + chair.x() + chair.width() / 2,
                y: chairBgGroup.y() + chair.y() + chair.height() / 2
            };
            var time = 1000;

            if (!ifLast) {
                var uniq = new Date().getTime() + '_' + Math.ceil(100 * Math.random());
                var bankPos = self.chipsPos['allbank'];
                var flyChips = self.addFlyChips(uniq, bankPos.x, bankPos.y, money);
                flyChips.to({
                    x: pos.x,
                    y: pos.y,
                    duration: 1,
                    onFinish: function () {
                        flyChips.destroy();
                        if (player) {
                            self.addTextWinMoney(position, money);
                        }
                    }
                });
            } else {
                var chipGroup = self.layer.findOne('#chipGroup-' + 'allbank');
                var chips = chipGroup.find('Rect');
                chips.each(function (chip) {
                    if (!chip.hasName('chip-money-wrap')) {
                        chip.destroy();
                    }
                });
                self.layer.batchDraw();
                chipGroup = self.getChipsStr(money, chipGroup);
                var chip_money_wrap = chipGroup.findOne('.chip-money-wrap');
                chip_money_wrap.getParent().destroy();
                self.layer.batchDraw();
                chipGroup.to({
                    x: pos.x,
                    y: pos.y,
                    duration: 1,
                    onFinish: function () {
                        if (player) {
                            self.addTextWinMoney(position, money);
                        }
                        chipGroup.destroy();
                    }
                });
            }
            setTimeout(function () {
                if (player) {
                    var count_chip = self.layer.findOne('#count_chip-' + position);
                    if (count_chip) {
                        count_chip.setText(self.countFilter(player.balance));
                        var bg_width = 5 + 32 + 5 + count_chip.getWidth() + 7;
                        var bg_count_chip = self.layer.findOne('#bg_count_chip-' + position);
                        bg_count_chip.clearCache().width(bg_width).sceneFunc(function (context) {
                            var x = 0, y = 0, width = bg_width, height = 53;
                            var radius = {tl: 0, tr: 10, br: 0, bl: 10};
                            context.beginPath();
                            context.moveTo(x + radius.tl, y);
                            context.lineTo(x + width - radius.tr, y);
                            context.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
                            context.lineTo(x + width, y + height - radius.br);
                            context.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
                            context.lineTo(x + radius.bl, y + height);
                            context.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
                            context.lineTo(x, y + radius.tl);
                            context.quadraticCurveTo(x, y, x + radius.tl, y);
                            context.closePath();
                            context.fillStrokeShape(this);
                        }).cache();
                    }
                }
            }, time);
        },
        addBet: function (player, money, value, position, type) {
            var self = this;
            if (!money) {
                return;
            }
            
            var balance = player.balance;
            var chipsPos = this.chipsPos[position];
            var chipLeft = chipsPos['x'];
            var chipTop = chipsPos['y'];

            if (!value) {
                var chipGroup = this.layer.findOne('#chipGroup-' + position);
                if (!chipGroup) {
                    this.addChips(position, money);
                } else {
                    var chip_money_wrap = chipGroup.findOne('.chip-money-wrap');
                    var chip_money = chipGroup.findOne('.chip-money');
                    chip_money.setText(money);
                    chip_money_wrap.clearCache()
                            .width(42 + chip_money.width())
                            .cache();
                    this.layer.batchDraw();
                }
                return;
            }

            var chair = this.layer.findOne('#chair-' + position);
            var chairBgGroup = chair.getParent();
            var pos = {
                x: chairBgGroup.x() + chair.x() + chair.width() / 2,
                y: chairBgGroup.y() + chair.y() + chair.height() / 2
            };

            var uniq = new Date().getTime() + '_' + Math.ceil(100 * Math.random());
            var flyChips = this.addFlyChips(uniq, pos.x, pos.y, value);
            flyChips.to({
                x: chipLeft,
                y: chipTop,
                duration: 0.4
            });

            this.startBetTimer = (new Date()).getTime();
            setTimeout(function () {
                var chip_money_wrap, chip_money;
                var chipGroup = self.layer.findOne('#chipGroup-' + position);

                if (!chipGroup) {
                    chipGroup = self.addChips(position, money);
                    chip_money_wrap = chipGroup.findOne('.chip-money-wrap');
                    chip_money_wrap.clearCache().opacity(0).cache();
                    //Sound.play('poker', 'callfirst');
                } else {
                    chip_money_wrap = chipGroup.findOne('.chip-money-wrap');
                    chip_money = chipGroup.findOne('.chip-money');
                    chip_money_wrap.clearCache().opacity(0).cache();
                    self.layer.batchDraw();
                    chip_money.setText(money);
                    chip_money_wrap.clearCache()
                            .width(42 + chip_money.width())
                            .cache();
                    self.layer.batchDraw();
                    //Sound.play('poker', 'call' + Math.ceil(Math.random() * 3));
                }
                var count_chip = self.layer.findOne('#count_chip-' + position);
                if (count_chip) {
                    count_chip.setText(self.countFilter(balance));
                    var bg_width = 5 + 32 + 5 + count_chip.getWidth() + 7;
                    var bg_count_chip = self.layer.findOne('#bg_count_chip-' + position);
                    bg_count_chip.clearCache().width(bg_width).sceneFunc(function (context) {
                        var x = 0, y = 0, width = bg_width, height = 53;
                        var radius = {tl: 0, tr: 10, br: 0, bl: 10};
                        context.beginPath();
                        context.moveTo(x + radius.tl, y);
                        context.lineTo(x + width - radius.tr, y);
                        context.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
                        context.lineTo(x + width, y + height - radius.br);
                        context.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
                        context.lineTo(x + radius.bl, y + height);
                        context.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
                        context.lineTo(x, y + radius.tl);
                        context.quadraticCurveTo(x, y, x + radius.tl, y);
                        context.closePath();
                        context.fillStrokeShape(this);
                    }).cache();
                }
                var chips = chipGroup.find('Rect');
                chips.each(function (chip) {
                    if (!chip.hasName('chip-money-wrap')) {
                        chip.destroy();
                    }
                });
                self.layer.batchDraw();
                chipGroup = self.getChipsStr(money, chipGroup);
                chip_money_wrap = chipGroup.findOne('.chip-money-wrap');
                chip_money_wrap.getParent().moveToTop();
                chip_money_wrap.to({
                    opacity: 1,
                    duration: 0.2
                });
                flyChips.destroy();
            }, 400);
        },
        /*
         * buttons
         */
        showActiveButtons: function (player, callValue, callSum, isAllinEnd, requests, id) {
            var self = this;
            $('.playBtnsContainer').fadeTo(400, 1);
            if (player && player.info['id'] == self.my_id) {
                callValue = Math.min(callValue, player.balance);
                if (callValue == 0) {
                    $('#btn-call').hide();
                    $('#btn-check').show();
                } else {
                    $('#btn-check').hide();
                    $('#btn-call').show().find('span').text('Call to ' + (callSum + callValue));
                }
                if (isAllinEnd && callValue == player.balance) {
                    $('#btn-call, #btn-fold').removeClass('disabled');
                    $('#btn-all-in, #btn-check').hide();
                } else {
                    $('#btn-call, #btn-check, #btn-raise, #btn-fold').removeClass('disabled');

                    $('#btn-raise').show();
                    $('#btn-all-in').show();

                    if ($('#btn-callAll').parent().hasClass('checked')) {
                        requests.call(id);
                    }
                    if ($('#btn-checkOrFold').parent().hasClass('checked')) {
                        if (callValue == 0) {
                            requests.check(id);
                        } else {
                            requests.fold(id);
                        }
                    }
                }
            } else {
                $('#btn-call, #btn-raise, #btn-all-in, #btn-check').hide();
            }
        },
        hideButtons: function () {
            $('.playBtnsContainer').fadeTo(400, 0);
        },
        showButtons: function () {
            $('.playBtnsContainer').fadeTo(400, 1);
        },
        disableCheckboxes: function () {
            $('#btn-callAll').parent().removeClass('checked');
            $('#btn-checkOrFold').parent().removeClass('checked');
        },
        /*
         * add shapes
         */
        addGlobalShapes: function () {
            this.canvasTimer = document.createElement('canvas');
            var max = Math.max(this.chairBgWidth, this.chairBgHeight);
            this.canvasTimer.width = max + 30;
            this.canvasTimer.height = max + 30;

            this.chairTimer = new Konva.Rect({
                name: 'chairTimer',
                x: 0,
                y: 0,
                width: this.chairBgWidth + 30,
                height: this.chairBgHeight + 30,
                cornerRadius: 20,
                fillPatternImage: this.canvasTimer,
                fillPatternX: -((this.chairBgHeight - this.chairBgWidth) / 2),
                fillPatternRepeat: 'no-repeat',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                perfectDrawEnabled: this.perfectDraw,
                shadowForStrokeEnabled: false,
                strokeScaleEnabled: false,
                shadowEnabled: false,
                dashEnabled: false,
                listening: false
            });
        },
        addCardShapes: function () {
            this.cardsImg = this.getImage('cards');

            var i = 0;
            while (i < 5) {
                var item = this.cardsPos['1'];
                var card = new Konva.Rect({
                    id: 'card-' + ((+i) + 1),
                    name: 'cards grayscale',
                    x: item.x + i * (this.cardWidth + 10) + this.cardWidth / 2,
                    y: item.y + this.cardHeight / 2,
                    width: this.cardWidth,
                    height: this.cardHeight,
                    offset: {
                        x: this.cardWidth / 2,
                        y: this.cardHeight / 2
                    },
                    fillPatternScale: {
                        x: this.cardWidth / this.cardOrginWidth,
                        y: this.cardHeight / this.cardOrginHeight
                    },
                    scaleX: -1,
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    perfectDrawEnabled: this.perfectDraw,
                    shadowForStrokeEnabled: false,
                    strokeScaleEnabled: false,
                    shadowEnabled: false,
                    dashEnabled: false,
                    listening: false
                }).cache();
                this.layer.add(card);
                i++;
            }
        },
        showRaiseDialog: function (callValue, min, max, callback) {
            if (!$('.raise_popover').is(':visible')) {
                var content = '<div class="joinchips text-center">'
                            + '<input id="raise_current" type="text" name="current" class="m_input form-control text-right" value="' + min + '">'
                            + '<span class="max">/ ' + max + '</span>'
                            + '<div class="expprogress center-block">'
                                + '<div class="line">'
                                    + '<div class="line-t" style="width:50%;"></div>'
                                    + '<div class="line-btn" style="left:160px;"></div>'
                                + '</div>'
                            + '</div>'
                            + '<br>'
                            + '<a id="raiseBtn" class="btn m_btn btn-warning btn-s center-block"><b>Raise</b></a>'
                        + '</div>';

                $('#btn-raise').popover({
                    container: 'body',
                    content: content,
                    html: true,
                    placement: 'top',
                    template: '<div class="popover raise_popover" role="tooltip"><div class="popover-content"></div></div>'
                });

                $('#btn-raise').popover('show');

                var callbackClose = {
                    close: function () {
                        $('#btn-raise').popover('destroy');
                    }
                };

                $('#btn-raise').on('shown.bs.popover', function () {
                    var old = '';
                    var element = '.raise_popover .line-btn';
                    var current = min;
                    var setCurrenty = function (cur, change) {
                        var c = (max) ? cur / max : 0;
                        var scrollElement = $(element);
                        var scrollBlockElement = scrollElement.parent();
                        var scrollBgElement = scrollBlockElement.find('.line-t');
                        var w = scrollElement.width();
                        var b = scrollBlockElement.width();
                        $(scrollElement).css({
                            left: ((b) * c) - w / 2 + 'px'
                        });
                        $(scrollBgElement).css({
                            width: 100 * c + '%'
                        });
                        if (change) {
                            $('#raise_current').val(cur);
                        }
                        current = cur;
                        if (current >= min && current <= max) {
                            if ($('#raiseBtn').hasClass('disabled'))
                                $('#raiseBtn').removeClass('disabled');
                        } else {
                            if (!$('#raiseBtn').hasClass('disabled'))
                                $('#raiseBtn').addClass('disabled');
                        }
                    };

                    setCurrenty(current, true);

                    $('.raise_popover .max').text(max);

                    $(element).mousedown(function (e) {
                        e.preventDefault();
                        $('.raise_popover').mousemove(function (e) {
                            var scrollBlockElement = $(element).parent();
                            var a = e.pageX - $(scrollBlockElement).offset().left;
                            var b = $(scrollBlockElement).width();
                            var c = Math.min(1, Math.max(0, a / b));
                            setCurrenty(Math.max(min, Math.round(max * c)), true);
                        }).mouseup(function () {
                            $(this).unbind('mousemove');
                        });
                        $('#.raise_popover .expprogress').mouseleave(function () {
                            $('.raise_popover').unbind('mousemove');
                        });
                    });

                    $('.raise_popover #raise_current').keydown(function () {
                        old = $(this).val();
                    }).keyup(function () {
                        if ($(this).val() == '') {
                            current = 0;
                        } else {
                            var value = parseInt($(this).val());
                            if (value < 0 || value > max) {
                                $(this).val(old);
                            } else {
                                current = value;
                            }
                        }
                        setCurrenty(current, false);
                    }).blur(function () {
                        if ($(this).val() == '') {
                            $(this).val('0');
                        }
                    });

                    $('.raise_popover .line').click(function (e) {
                        var element = this;
                        var a = e.pageX - $(element).offset().left;
                        var b = $(element).width();
                        var c = Math.min(1, Math.max(0, a / b));
                        setCurrenty(Math.round(max * c), true);
                    });

                    $('.raise_popover input#raise_current').keydown(function (e) {
                        if (e.keyCode == 13) {
                            callback($(this).val(), callbackClose);
                        }
                    });
                    $('#raiseBtn').click(function () {
                        if (!$(this).hasClass('disabled')) {
                            callback($('#raise_current').val(), callbackClose);
                        }
                    });

                    var enter = true;
                    $('.raise_popover').mouseenter(function () {
                        enter = true;
                    });
                    $('.raise_popover').mouseleave(function () {
                        enter = false;
                        setTimeout(function () {
                            if (!enter) {
                                $('#btn-raise').popover('hide');
                            }
                        }, 500);
                    });
                });
            }
        },
        resize: function () {
            var tableImg = this.getImage('table');

            var w = $(this.params.el).width();
            var h = $(this.params.el).height();

            this.origWidth = tableImg.naturalWidth + 235 * 2;
            this.origHeight = tableImg.naturalHeight + 261;

            var ratio = this.origWidth / this.origHeight;
            var height = Math.round(Math.min(h, w / ratio));
            var width = Math.round(height * ratio);
            ratio = width / this.origWidth;

            var x = Math.round((w - this.origWidth * ratio) / 2);
            var y = Math.round((h - this.origHeight * ratio) / 2);
            this.stage.x(x).y(y).scale({x: ratio, y: ratio});
            this.stage.draw();
        },
        animate: function () {
            for (var i in this.animation) {
                var item = this.animation[i];
                if (i == 'timer') {
                    item.callback(item.params);
                }
            }
        }
    });
});
