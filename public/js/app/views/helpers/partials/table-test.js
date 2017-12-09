define(['views/view', 'konva',
    'gsap.KonvaPlugin',
    'TweenMax'], function (View, Konva, KonvaPlugin, TweenMax) {
    return View.extend({
        // user values
        id: 0,
        sum: 0,
        // konva global settings
        perfectDraw: true,
        // width settings
        origWidth: 0,
        origHeight: 0,
        chairBgWidth: 0,
        chairBgHeight: 0,
        chairTimer: null,
        chairPos: {
            1: {x: 1155, y: 298},
            2: {x: 1419, y: 554},
            3: {x: 1419, y: 820},
            4: {x: 1263, y: 1100},
            5: {x: 853, y: 1113},
            6: {x: 516, y: 1113},
            7: {x: 150, y: 1100},
            8: {x: 2, y: 820},
            9: {x: 2, y: 554},
            10: {x: 255, y: 298}
        },
        cardOrginWidth: 366,
        cardOrginHeight: 520,
        cardWidth: 122,
        cardHeight: 174,
        cards: {
            'card-10-clubs': {x: 1, y: 1},
            'card-10-diamonds': {x: 369, y: 1},
            'card-10-hearts': {x: 737, y: 1},
            'card-10-spades': {x: 1105, y: 1},
            'card-2-clubs': {x: 1473, y: 1},
            'card-2-diamonds': {x: 1841, y: 1},
            'card-2-hearts': {x: 2209, y: 1},
            'card-2-spades': {x: 2577, y: 1},
            'card-3-clubs': {x: 1, y: 523},
            'card-3-diamonds': {x: 369, y: 523},
            'card-3-hearts': {x: 737, y: 523},
            'card-3-spades': {x: 1105, y: 523},
            'card-4-clubs': {x: 1473, y: 523},
            'card-4-diamonds': {x: 1841, y: 523},
            'card-4-hearts': {x: 2209, y: 523},
            'card-4-spades': {x: 2577, y: 523},
            'card-5-clubs': {x: 1, y: 1045},
            'card-5-diamonds': {x: 369, y: 1045},
            'card-5-hearts': {x: 737, y: 1045},
            'card-5-spades': {x: 1105, y: 1045},
            'card-6-clubs': {x: 1473, y: 1045},
            'card-6-diamonds': {x: 1841, y: 1045},
            'card-6-hearts': {x: 2209, y: 1045},
            'card-6-spades': {x: 2577, y: 1045},
            'card-7-clubs': {x: 1, y: 1567},
            'card-7-diamonds': {x: 369, y: 1567},
            'card-7-hearts': {x: 737, y: 1567},
            'card-7-spades': {x: 1105, y: 1567},
            'card-8-clubs': {x: 1473, y: 1567},
            'card-8-diamonds': {x: 1841, y: 1567},
            'card-8-hearts': {x: 2209, y: 1567},
            'card-8-spades': {x: 2577, y: 1567},
            'card-9-clubs': {x: 1, y: 2089},
            'card-9-diamonds': {x: 369, y: 2089},
            'card-9-hearts': {x: 737, y: 2089},
            'card-9-spades': {x: 1105, y: 2089},
            'card-ace-clubs': {x: 1473, y: 2089},
            'card-ace-diamonds': {x: 1841, y: 2089},
            'card-ace-hearts': {x: 2209, y: 2089},
            'card-ace-spades': {x: 2577, y: 2089},
            'card-jack-clubs': {x: 1, y: 2611},
            'card-jack-diamonds': {x: 369, y: 2611},
            'card-jack-hearts': {x: 737, y: 2611},
            'card-jack-spades': {x: 1105, y: 2611},
            'card-king-clubs': {x: 1473, y: 2611},
            'card-king-diamonds': {x: 1841, y: 2611},
            'card-king-hearts': {x: 2209, y: 2611},
            'card-king-spades': {x: 2577, y: 2611},
            'card-queen-clubs': {x: 2945, y: 1},
            'card-queen-diamonds': {x: 2945, y: 523},
            'card-queen-hearts': {x: 2945, y: 1045},
            'card-queen-spades': {x: 2945, y: 1567}
        },
        cardsPos: {
            1: {x: 456, y: 630}
        },
        chipWidth: 35,
        chipHeight: 35,
        chips: {
            'chip-1': {x: 1, y: 1},
            'chip-10': {x: 38, y: 1},
            'chip-100': {x: 1, y: 38},
            'chip-2': {x: 38, y: 38},
            'chip-200': {x: 75, y: 1},
            'chip-5': {x: 75, y: 38},
            'chip-50': {x: 1, y: 75},
            'chip-500': {x: 38, y: 75}
        },
        chipsPos: {
            1: {x: 1110, y: 528},
            2: {x: 1260, y: 617},
            3: {x: 1298, y: 826},
            4: {x: 1198, y: 964},
            5: {x: 902, y: 1000},
            6: {x: 565, y: 1000},
            7: {x: 323, y: 964},
            8: {x: 208, y: 826},
            9: {x: 250, y: 617},
            10: {x: 375, y: 528},
            bank: {x: 760, y: 830},
            allbank: {x: 750, y: 540}
        },
        dealerPos: {
            1: {x: 1075, y: 573},
            2: {x: 1225, y: 662},
            3: {x: 1253, y: 791},
            4: {x: 1153, y: 929},
            5: {x: 867, y: 966},
            6: {x: 610, y: 966},
            7: {x: 368, y: 929},
            8: {x: 253, y: 791},
            9: {x: 295, y: 662},
            10: {x: 410, y: 573}
        },
        events: {},
        initialize: function (params) {
            this.params = params;
        },
        render: function () {
            var self = this;

            var tableImg = this.getImage('table');
            var chairImg = this.getImage('chair');

            var w = $(this.params.el).width();
            var h = $(this.params.el).height();

            this.origWidth = tableImg.naturalWidth;
            this.origHeight = tableImg.naturalHeight;
            this.chairBgWidth = Math.round(chairImg.naturalWidth * 1.5);
            this.chairBgHeight = Math.round(chairImg.naturalHeight * 2);

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

            this.layer = new Konva.Layer();

            var bg = new Konva.Image({
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
            bg.cache();
            this.layer.add(bg);

            for (var i in this.chairPos) {
                var num = +i;
                var item = this.chairPos[num];
                var chairBgGroup = new Konva.Group({
                    id: 'chairBgGroup-' + num,
                    name: 'chairBgGroup',
                    x: item.x - Math.round((this.chairBgWidth / 2 - chairImg.naturalWidth / 2)),
                    y: item.y - Math.round((this.chairBgHeight / 2 - chairImg.naturalHeight / 2)),
                    width: this.chairBgWidth,
                    height: this.chairBgHeight
                });
                var chairBg = new Konva.Rect({
                    id: 'chairBg-' + num,
                    name: 'chairBg',
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
                    id: 'chair-' + num,
                    name: 'chair',
                    x: item.x,
                    y: item.y,
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
                    id: 'chairBgText-' + num,
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
                chairBgText.x(item.x + Math.round((chairImg.naturalWidth / 2 - chairBgText.getWidth() / 2)));
                chairBgText.y(item.y + Math.round((chairImg.naturalHeight / 2 - chairBgText.getHeight() / 2)) - 22);
                chairBg.cache();
                chair.cache();
                chairBgText.cache();
                chairBgGroup.add(chairBg);
                this.layer.add(chairBgGroup).add(chair).add(chairBgText);
            }

            this.addLayerEvents();
            //this.addAlertShape();
            //this.addTimerShape(chairBgWidth, chairBgHeight);
            //this.addWinShapes(chairBgWidth, chairBgHeight);

            //self.timerSeat(10000, 10, 1000, function () {
            //self.win(2000, 200, 10, false);
            //});

            // add cards
            //this.showCards();
            // add chips
            //this.showChips();

            this.stage.add(this.layer);

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
                    text.clearCache();
                    target.fill('rgba(0, 0, 0, 0.6)');
                    text.fill('#fff');
                    target.cache();
                    text.cache();
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
                    text.clearCache();
                    target.fill('rgba(0, 0, 0, 0.4)');
                    text.fill('#9E9E9E');
                    target.cache();
                    text.cache();
                    self.stage.batchDraw();
                }
            });
            this.layer.on('click', function (e) {
                var target = e.target;
                if (target.hasName('chairBg')) {
                    var id = target.attrs.id;
                    var num = id.split('-')[1];
                    self.sitUpAction(num);
                    self.stage.batchDraw();
                }
            });
        },
        addAlertShape: function () {
            this.alert = new Konva.Label({
                y: 820,
                listening: false,
                draggable: false
            });
            this.alert.add(new Konva.Tag({
                fill: 'rgba(0, 0, 0, 0.8)',
                listening: false
            }));
            this.alertText = new Konva.Text({
                name: 'alertText',
                padding: 20,
                fill: '#fff',
                text: _.template.t('poker.pls_wait_nxt_hand'),
                fontFamily: 'Arial',
                fontSize: 40,
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                shadowForStrokeEnabled: false,
                perfectDrawEnabled: false,
                listening: false
            });
            this.alert.x(this.origWidth / 2 - (this.alertText.width() - 10) / 2);
            this.alert.add(this.alertText);
            this.layer.add(this.alert);
        },
        addTimerShape: function (chairBgWidth, chairBgHeight) {
            this.canvasTimer = document.createElement('canvas');
            var max = Math.max(this.chairBgWidth, this.chairBgHeight);
            this.canvasTimer.width = max + 30;
            this.canvasTimer.height = max + 30;

            this.chairTimer = new Konva.Rect({
                name: 'chairTimer',
                x: -15,
                y: -15,
                width: this.chairBgWidth + 30,
                height: this.chairBgHeight + 30,
                cornerRadius: 20,
                fillPatternImage: this.canvasTimer,
                fillPatternX: -((this.chairBgHeight - this.chairBgWidth) / 2),
                fillPatternRepeat: 'no-repeat',
                transformsEnabled: 'position',
                shadowEnabled: false,
                strokeEnabled: false,
                strokeHitEnabled: false,
                shadowForStrokeEnabled: false,
                perfectDrawEnabled: false,
                listening: false
            });
        },
        addWinShapes: function (chairBgWidth, chairBgHeight) {
            this.winLabel = new Konva.Label({
                visible: false,
                listening: false
            });
            this.winLabel.add(new Konva.Tag({
                fill: 'rgba(0, 0, 0, 0.4)',
                listening: false
            }));
            this.winText = new Konva.Text({
                name: 'winText',
                width: this.chairBgWidth,
                padding: 5,
                text: '',
                fontFamily: 'Arial',
                fontSize: 40,
                align: 'center',
                transformsEnabled: 'position',
                strokeEnabled: false,
                strokeHitEnabled: false,
                shadowForStrokeEnabled: false,
                perfectDrawEnabled: false,
                listening: false
            });
            this.winLabel.add(this.winText);
            this.layer.add(this.winLabel);

            this.winPlus = new Konva.Text({
                name: 'winPlus',
                x: 10,
                text: '',
                fontFamily: 'Arial',
                fontSize: 22,
                fill: '#FFEB3B',
                transformsEnabled: 'position',
                visible: false,
                strokeEnabled: false,
                strokeHitEnabled: false,
                shadowForStrokeEnabled: false,
                perfectDrawEnabled: false,
                listening: false
            });
            this.winPlus.y(this.chairBgHeight + this.winPlus.getHeight() / 2 + 10);
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
        showCards: function () {
            var cardsImg = this.getImage('cards');
            var cardBackImg = this.getImage('red-back');

            var lear = ['diamonds', 'hearts', 'spades', 'clubs'];
            var rndLear = lear[Math.floor(Math.random() * lear.length)];

            var i = 0;
            while (i < 5) {
                var item = this.cardsPos['1'];

                var back = Math.round(Math.random());
                var type = ['2', '3', '4', '5', '6', '7', '8', '8', '9', '10', 'jack', 'queen', 'king', 'ace'];
                var name = 'card-' + type[Math.floor(Math.random() * type.length)] + '-' + rndLear;

                var card = new Konva.Rect({
                    x: item.x + i * (this.cardWidth + 10),
                    y: item.y,
                    width: this.cardWidth,
                    height: this.cardHeight,
                    fillPatternImage: ((back) ? cardsImg : cardBackImg),
                    fillPatternOffset: {
                        x: ((back) ? this.cards[name].x : 0),
                        y: ((back) ? this.cards[name].y : 0)
                    },
                    fillPatternScale: {
                        x: this.cardWidth / this.cardOrginWidth,
                        y: this.cardHeight / this.cardOrginHeight
                    },
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                });
                card.cache();
                this.layer.add(card);
                i++;
            }
        },
        showChips: function () {
            var chipsImg = this.getImage('chips');
            var dealerImg = this.getImage('dealer');

            for (var j in this.chipsPos) {
                var item = this.chipsPos[j];

                var group = new Konva.Group({
                    x: item.x,
                    y: item.y,
                    width: this.chipWidth,
                    height: this.chipHeight,
                    transformsEnabled: 'position',
                    listening: false
//                    listening: true,
//                    draggable: true
                });
//                var self = this;
//                group.on('dragend', function () {
//                    var pos = this.getAbsolutePosition();
//                    var x = pos.x - self.stage.x();
//                    var y = pos.y - self.stage.y();
//                    console.info({x: x, y: y});
//                });
                this.layer.add(group);

                var positions = {
                    1: {
                        1: [0, 0],
                        2: [10, -5],
                        3: [-10, 10]
                    }
                };
                var positionCounts = {};
                var stepY = 3;
                var sum = Math.floor(Math.random() * (2999 - 2001 + 1)) + 2001;
                var type = this.getChipTypes(sum);
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
                        shadowForStrokeEnabled: false,
                        perfectDrawEnabled: false,
                        listening: false
//                        listening: true,
//                        draggable: true
                    });
                    chip.cache();
//                    var self = this;
//                    chip.on('dragend', function () {
//                        var pos = this.getAbsolutePosition();
//                        var x = pos.x - self.stage.x();
//                        var y = pos.y - self.stage.y();
//                        console.info({x: x, y: y});
//                    });
                    group.add(chip);
                }

                var label = new Konva.Label({
                    x: 17,
                    y: 0,
                    listening: false
                });
                label.add(new Konva.Tag({
                    cornerRadius: 5,
                    fill: 'rgba(205, 220, 57, 0.9)',
                    //stroke: '#7B8617',
                    //strokeWidth: 1,
                    shadowColor: 'black',
                    shadowBlur: 3,
                    shadowOffset: {x: 0, y: 0},
                    shadowOpacity: 0.85,
                    listening: false
                }));
                var chip_sImg = this.getImage('chip-s');
                label.add(new Konva.Image({
                    x: 3,
                    y: 4,
                    width: 24,
                    height: 24,
                    image: chip_sImg,
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                }));
                var text = new Konva.Text({
                    padding: 5,
                    text: '2' + ' ',
                    fontFamily: 'Arial',
                    fontSize: 22,
                    fontStyle: 'bold',
                    align: 'right',
                    transformsEnabled: 'position',
                    strokeEnabled: false,
                    strokeHitEnabled: false,
                    shadowForStrokeEnabled: false,
                    perfectDrawEnabled: false,
                    listening: false
                });
                text.width(text.getWidth() + 27);
                label.add(text);
                group.add(label);

                if (this.dealerPos[j]) {
                    var item = this.dealerPos[j];
                    var dealer = new Konva.Image({
                        x: item.x,
                        y: item.y,
                        width: this.chipWidth + 2,
                        height: this.chipHeight + 2,
                        image: dealerImg,
                        transformsEnabled: 'position',
                        strokeEnabled: false,
                        strokeHitEnabled: false,
                        shadowForStrokeEnabled: false,
                        perfectDrawEnabled: false,
                        listening: false
                    });
                    dealer.cache();
                    this.layer.add(dealer);
                }
            }
        },
        addUser: function () {
            var self = this;

            var chipImg = this.getImage('chip-s');

            var user = this.params.app.profile.user;
            var link = user.get('url');

            var img = new Image();
            img.onload = function () {
                var photo = new Konva.Shape({
                    id: 'photo-' + self.id,
                    width: 198,
                    height: 188,
                    x: 1,
                    y: 37,
                    sceneFunc: function (context) {
                        var x = 0, y = 0, width = 198, height = 188;
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
                    fillPatternImage: img,
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
                    id: 'name-' + self.id,
                    x: 7,
                    y: 5,
                    width: 186,
                    text: user.get('name'),
                    fontSize: 25,
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
                    window.open(link, '_blank');
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
                    id: 'chip_s-' + self.id,
                    x: 5,
                    y: 189,
                    width: 32,
                    height: 32,
                    image: chipImg,
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
                    id: 'count_chip-' + self.id,
                    x: 43,
                    y: 195,
                    text: '200K',
                    fontSize: 22,
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
                    id: 'bg_count_chip-' + self.chairId,
                    width: bgWidth,
                    height: 43,
                    x: 0,
                    y: 183,
                    sceneFunc: function (context) {
                        var x = 0, y = 0, width = bgWidth, height = 43;
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
                    fill: 'rgba(0,0,0,0.7)',
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
                photo.cache();
                //name.cache();
                bg.cache();
                chip.cache();

                var chairBg = self.layer.findOne('#chairBg-' + self.id);
                var group = chairBg.getParent();
                //group.add(photo).add(name).add(bg).add(chip).add(text);
                group.add(photo).add(name);

                self.layer.draw();
            };
            img.crossOrigin = 'Anonymous';
            img.src = user.get('url');
        },
        timerSeat: function (time, position, timeDiff, callback) {
            if (timeDiff > time) {
                return;
            }

            var self = this;
            var requestAnimationFrame = window.requestAnimationFrame
                    || window.mozRequestAnimationFrame
                    || window.webkitRequestAnimationFrame
                    || window.msRequestAnimationFrame;
            window.requestAnimationFrame = requestAnimationFrame;

            var chairBgGroup = this.layer.findOne('#chairBgGroup-' + position);
            if (typeof (chairBgGroup) == 'undefined') {
                return;
            }
            chairBgGroup.add(this.chairTimer);
            this.chairTimer.moveToBottom();
            var context = this.canvasTimer.getContext('2d');
            var x = this.canvasTimer.width / 2;
            var y = this.canvasTimer.height / 2;
            var radius = 120;
            var curPerc = 0;
            var circ = Math.PI * 2;
            var quart = Math.PI / 2;
            context.lineWidth = 120;
            var colors = ['#8BC34A', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722', '#F44336'];
            timeDiff = Math.max(0, timeDiff);

            var t = this.params.app.getTimeNow();
            //var timeStart = self.game.getTimeNow() - timeDiff;
            var timeStart = t - timeDiff;

            var timeEnd = timeStart + time;
            var chipsTime = 100;
            function animate() {

                var t = self.params.app.getTimeNow();
                //var timeNow = self.game.getTimeNow();
                var timeNow = t;

                context.clearRect(0, 0, self.canvasTimer.width, self.canvasTimer.height);
                context.beginPath();
                curPerc = Math.min(100, Math.max(0, (timeNow - timeStart) / (timeEnd - timeStart) * 100));
                var current = curPerc / 100;
                context.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
                var colorIndex = Math.floor(curPerc / (100 / colors.length));
                if (colors[colorIndex] !== undefined) {
                    context.strokeStyle = colors[colorIndex];
                    context.stroke();
                    self.layer.batchDraw();
                }
                if (curPerc < chipsTime) {
                    self.requestId = requestAnimationFrame(function () {
                        animate();
                    });
                } else {
                    window.cancelAnimationFrame(self.requestId);
                    self.requestId = undefined;
                    self.chairTimer.remove();
                    if (typeof (callback) == 'function')
                        callback();
                    self.layer.batchDraw();
                }
            }
            this.layer.draw();
            animate();
        },
        win: function (bank, money, position, ifLast) {
            var self = this;

            var chairBgGroup = this.layer.findOne('#chairBgGroup-' + position);

            this.chairTimer.fillPatternImage(null);
            this.chairTimer.fill('#F0D200');
            chairBgGroup.add(this.chairTimer);
            this.chairTimer.moveToBottom();

            this.winText.setText('Win');
            this.winText.fill('#FFEB3B');
            this.winLabel.x(chairBgGroup.x());
            this.winLabel.y(chairBgGroup.y() + Math.round((chairBgGroup.height() / 2 - this.winText.getHeight() / 2)));
            this.winLabel.visible(true);

            this.winPlus.setText('+3000');
            chairBgGroup.add(this.winPlus);
            this.winPlus.visible(true);

            this.alertText.setText('Two pairs');
            this.alert.x(this.origWidth / 2 - (this.alertText.width() - 10) / 2);
            this.alert.show();

            self.layer.batchDraw();

            var tween = TweenMax.to(this.chairTimer, 1, {
                konva: {opacity: 0.5},
                repeat: -1,
                yoyo: true,
                ease: Linear.easeInOut,
                onUpdate: function () {
                    self.layer.batchDraw();
                }
            });

            setTimeout(function () {
                tween.kill();
                self.chairTimer.fill(null);
                self.chairTimer.fillPatternImage(self.canvasTimer);
                self.chairTimer.opacity(1);
                self.winLabel.visible(false);
                self.winPlus.visible(false).remove();
                self.alert.hide();
                self.layer.batchDraw();
            }, 3000);

//            var player = this.game.users[position];
//            var pos = this.getSeatPosition(position);
//            var time = 1000;
//            console.log('win', bank, money);
//            if (!ifLast) {
//                var uniq = new Date().getTime() + '_' + Math.ceil(100 * Math.random());
//                var bankPos = $('#window-' + self.name + ' .panel-content .bank').position();
//
//                $('#window-' + self.name + ' .panel-content').append(
//                        '<div id="chips-' + uniq + '" class="hand" style="z-index:101;position:absolute;left:' + bankPos.left + 'px;top:' + bankPos.top + 'px">'
//                        + '<div class="chips">'
//                        + self.getChipsStr(self.getChipTypes(money))
//                        + '</div>'
//                        + '<div class="value"><span class="gem-icon">' + money + '</span></div>'
//                        + '</div>');
//
//                $('#chips-' + uniq).animate({
//                    left: pos.left + 'px',
//                    top: pos.top + 'px'
//                }, time, function () {
//                    $('#chips-' + uniq).remove();
//                    if (player) {
//                        self.addTextWinMoney(position, money);
//                    }
//                });
//            } else {
//                $('#window-' + self.name + ' .panel-content .bank .chips').html(self.getChipsStr(self.getChipTypes(money)));
//                $('#window-' + self.name + ' .panel-content .bank .value .gem-icon span').html(money);
//                $('#window-' + self.name + ' .panel-content .bank').animate({
//                    left: pos.left,
//                    top: pos.top
//                }, time, function () {
//                    if (player) {
//                        self.addTextWinMoney(position, money);
//                    }
//                    $('#window-' + self.name + ' .panel-content .bank').css({
//                        left: '',
//                        top: ''
//                    }).empty();
//                });
//            }
//            setTimeout(function () {
//                $('#chips-' + uniq).remove();
//                if (player) {
//                    $('#window-' + self.name + ' .seat.seat-' + position + ' .gems-count .gem-icon').html(self.countFilter(player.balance));
//                }
//            }, time);
        },
        sitUpAction: function (num) {
            var self = this;
            this.params.openWindow('situp', function (sum) {
                self.sitAction(num, sum);
            });
        },
        sitAction: function (num, sum) {
            this.id = num;
            this.sum = sum;
            var chairBg = this.stage.findOne('#chairBg-' + this.id);
            var chair = this.stage.findOne('#chair-' + this.id);
            var chairBgText = this.stage.findOne('#chairBgText-' + this.id);
            if (chairBg) {
                chairBg.clearCache();
                chairBg.fill('rgba(0, 0, 0, 0.8)');
                chairBg.cache();
                var items = this.stage.find('.chairBg');
                items.each(function (item) {
                    item.listening(false);
                });
            }
            if (chair) {
                chair.hide();
            }
            if (chairBgText) {
                chairBgText.hide();
            }

            this.addUser();

            $('#standUpBtn').fadeIn();
        },
        standUpAction: function () {
            var chairBg = this.stage.findOne('#chairBg-' + this.id);
            var chair = this.stage.findOne('#chair-' + this.id);
            var chairBgText = this.stage.findOne('#chairBgText-' + this.id);
            var chairBgGroup = chairBg.getParent();
            var photo = chairBgGroup.findOne('#photo-' + this.id);
            var name = chairBgGroup.findOne('#name-' + this.id);
            var chip_s = chairBgGroup.findOne('#chip_s-' + this.id);
            var count_chip = chairBgGroup.findOne('#count_chip-' + this.id);
            var bg_count_chip = chairBgGroup.findOne('#bg_count_chip-' + this.id);
            if (photo) {
                photo.clearCache();
                photo.destroy();
            }
            if (name) {
                name.destroy();
            }
            if (bg_count_chip) {
                bg_count_chip.clearCache();
                bg_count_chip.destroy();
            }
            if (chip_s) {
                chip_s.clearCache();
                chip_s.destroy();
            }
            if (count_chip) {
                count_chip.destroy();
            }
            if (chairBg) {
                chairBg.clearCache();
                chairBg.fill('rgba(0, 0, 0, 0.4)');
                chairBg.cache();
                var items = this.stage.find('.chairBg');
                items.each(function (item) {
                    item.listening(true);
                });
            }
            if (chair) {
                chair.show();
            }
            if (chairBgText) {
                chairBgText.show();
            }
            this.layer.draw();

            $('#standUpBtn').fadeOut();
        },
        resize: function () {
            var tableImg = this.getImage('table');

            var w = $(this.params.el).width();
            var h = $(this.params.el).height();

            this.origWidth = tableImg.naturalWidth;
            this.origHeight = tableImg.naturalHeight;

            var ratio = this.origWidth / this.origHeight;
            var height = Math.round(Math.min(h, w / ratio));
            var width = Math.round(height * ratio);
            ratio = width / this.origWidth;

            var x = Math.round((w - this.origWidth * ratio) / 2);
            var y = Math.round((h - this.origHeight * ratio) / 2);
            this.stage.x(x).y(y).scale({x: ratio, y: ratio});
            this.stage.draw();
        }
    });
});

