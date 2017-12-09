define([], function () {
    function Filter(options) {
        this.options = options;
    }
    Filter.prototype.filter = function (timeEnd, status, labels) {
        if (labels === undefined) {
            var labels = {Y: _.template.t('time.Y'),
                M: _.template.t('time.M'),
                d: _.template.t('time.d'),
                h: _.template.t('time.h'),
                min: _.template.t('time.m'),
                s: _.template.t('time.s'),
                sep: ' '
            };
        } else if (labels == 'min') {
            var labels = {Y: '',
                M: '',
                d: '',
                h: '',
                min: '',
                s: '',
                sep: ':'
            };
        }
        status = (typeof (status) == 'undefined') ? false : status;
        var timeNow = (status) ? this.options.app.getTimeNow() / 1000 : 0;
        var sec = parseInt(timeEnd) - timeNow;

        if (!sec || sec < 0)
            return '0' + labels.s;
        var arr = [{'secs': (12 * 30 * 24 * 60 * 60), 'label': labels.Y},
            {'secs': (30 * 24 * 60 * 60), 'label': labels.M},
            {'secs': (24 * 60 * 60), 'label': labels.d},
            {'secs': (60 * 60), 'label': labels.h},
            {'secs': 60, 'label': labels.min},
            {'secs': 1, 'label': labels.s}];

        var length = arr.length, element = null;
        var str = '';
        var w = 0, level = 0, n = 0;
        var max = 3;
        var out = [];
        for (var i = 0; i < length && n < max; i++) {
            element = arr[i];
            var d = (sec - w) / element.secs;
            var r = Math.floor(d);
            if (d >= 1 || n > 0) {
                level++;
                n++;
                out[out.length] = ((r <= 9 && n > 1) ? '0' : '') + r + '' + element.label;
                w += r * element.secs;
            }
        }
        return out.join(labels.sep);
    };
    return Filter;
});