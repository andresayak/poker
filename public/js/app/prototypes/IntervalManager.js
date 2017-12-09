define(function () {
    function IntervalManager(name) {
        this.name = name
        this.callbacks = {};
    }

    IntervalManager.prototype.create = function (interval) {
        if (typeof (interval) == 'undefined') {
            interval = 1000;
        }
        var self = this;
        this.t = setInterval(function () {
            for (var name in self.callbacks) {
                self.callbacks[name](name, self);
            }
        }, interval);
    };

    IntervalManager.prototype.add = function (name, callback, interval) {
        if (typeof (this.t) == 'undefined') {
            this.create(interval);
        }
        this.callbacks[name] = callback;
    };

    IntervalManager.prototype.remove = function (name) {
        delete this.callbacks[name];
        if (Object.keys(this.callbacks).length == 0) {
            this.clear();
        }
    };

    IntervalManager.prototype.end = function () {
        if (typeof (this.callbackEnd) == 'function')
            this.callbackEnd(this);
    };

    IntervalManager.prototype.clear = function () {
        this.callbacks = {};
        if (typeof (this.t) != 'undefined')
            clearInterval(this.t);
        delete this.t;
    };

    return IntervalManager;
});