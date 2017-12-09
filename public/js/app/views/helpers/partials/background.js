define(['views/view', 'konva'], function (View, Konva) {
    return View.extend({
        events: {
        },
        initialize: function (params) {
            this.params = params;
        },
        render: function () {
            var w = $('.layout-home').width();
            var h = $('.layout-home').height();
            this.stage = new Konva.Stage({
                container: this.params.el,
                width: w,
                height: h
            });
            var layer = new Konva.Layer();
            layer.hitGraphEnabled(false);
            var bg = new Konva.Rect({
                id: 'bg',
                x: Math.round(w / 2),
                y: Math.round(h / 2),
                offset: {
                    x: Math.round(w / 2),
                    y: Math.round(h / 2)
                },
                width: w,
                height: h,
                fillPatternImage: this.getImage('bg-home'),
                fillPatternRotation: '-45',
                strokeEnabled: false,
                strokeHitEnabled: false,
                shadowForStrokeEnabled: false,
                perfectDrawEnabled: false,
                listening: false
            });
            bg.cache();
            layer.add(bg);
            this.stage.add(layer);
            return this;
        },
        resize: function(){
            var layout = $('body');
            var w = layout.width();
            var h = layout.height();

            // console.log(h);
            // console.log(document.querySelector('body').clientHeight);
            // console.log(document.querySelector('body').offsetHeight);

            this.stage.setWidth(w).setHeight(h);
            var bg = this.stage.findOne('#bg');
            bg.clearCache();
            bg.x(Math.round(w / 2))
                .y(Math.round(h / 2))
                .width(w)
                .height(h)
                .offset({x: Math.round(w / 2), y: Math.round(h / 2)});
            bg.cache();
            this.stage.draw();
        }
    });
});

