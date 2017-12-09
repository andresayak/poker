define([
    'backbone', 'services/Locator', 'views/helpers/loader'
], function (Backbone, ServiceLocator, loader) {
    return Backbone.View.extend({
        loader: new loader,
        pagination: function (element, request, render, Scroller, defer) {
            var el = this.$el.find(element);
            var p = el.data('page');
            var page_count = $(element).data('page_count');
            p = (p === undefined || !p) ? 1 : parseInt(p);
            page_count = (page_count === undefined || !page_count) ? 1 : parseInt(page_count);
            var self = this;
            var f = function (collection, page_count, p) {
                render.call(self, collection, p);
                _.defer(function () {
                    if (p == 1) {
                        if (typeof (defer) == 'function') {
                            defer(element);
                        }
                        el.data('page_count', page_count);
                        el.scroll(function (e) {
                            e.preventDefault();
                            var boxHeight = $(e.target).height();
                            var a = e.target.scrollTop;
                            var b = e.target.scrollHeight - 1 - boxHeight;
                            var c = a / b;
                            if (c >= 1 && p + 1 <= page_count) {
                                p++;
                                upload(p);
                            }
                        });
                    }
                });
            };
            var upload = function (p, data) {
                el.data('page', p);
                if (p == 1 && data !== undefined) {
                    f(data, p);
                } else {
                    request.call(self, {
                        success: function (data, page_count) {
                            f(data, page_count, p);
                        }
                    }, p);
                }
            };
            upload(p);
        },
        getImage: function(name){
            var images = ServiceLocator.get('images');
            return images[name];
        },
        getTemplate: function (name) {
            var el = ServiceLocator.get('templates').get(name);
            var temp = $('<script type="text/template">' + el + '</script>').html();
            return _.template(temp);
        },
        countFilter: function (count) {
            if (typeof (count) != 'number') {
                throw new Error('Invalid typeof=' + typeof (count) + ' of count = ' + count);
            }
            if (count >= 1000000000000) {
                return Math.floor(count / 1000000000000) / 10 + 'T';
            } else if (count >= 1000000000) {
                return Math.floor(count / 100000000) / 10 + 'G';
            } else if (count >= 1000000) {
                return Math.floor(count / 100000) / 10 + 'M';
            } else if (count >= 1000) {
                return Math.floor(count / 100) / 10 + 'K';
            }
            return count;
        },
        convertDate: function(unixtime, format) {
            var midnight = new Date(ServiceLocator.get('app').getTime() * 1000);
            midnight.setHours(0, 0, 0, 0);
            var date = new Date(unixtime * 1000 + ServiceLocator.get('app').timeDiff);
            var hour = date.getHours();
            hour = ((hour < 10) ? '0' : '') + hour;
            var min = date.getMinutes();
            min = ((min < 10) ? '0' : '') + min;
            var year = date.getUTCFullYear();
            var month = date.getUTCMonth() + 1;
            month = ((month < 10) ? '0' : '') + month;
            var day = date.getUTCDate();
            day = ((day < 10) ? '0' : '') + day;
            if(typeof(format) != 'function'){
                format = function (h, m, d, M, y){
                    if(date.getTime() >= midnight.getTime()){
                        return translate('time.today in') + ' '+ h + ':' + m;
                    }else if(date.getTime() >= midnight.getTime() - (3600 * 24 * 1000)){
                        return translate('time.yesterday in') + ' '+ h + ':' + m;
                    }else{
                        return h + ':' + m + ' ' + d + '-' + M + '-' + y;
                    }
                };
            }
            if(format === 'full'){
                format = function(h, m, d, M, y){
                    return h + ':' + m + ' ' + d + '-' + M + '-' + y;
                };
            }
            return format(hour, min, day, month, year);
        },
        getCards: function(){
            var cards = ServiceLocator.get('cards');
            return cards;
        }
    });
});