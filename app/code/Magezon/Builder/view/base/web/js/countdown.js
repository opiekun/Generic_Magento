define([
    'jquery',
    'jquery-ui-modules/widget',
    'Magezon_Builder/js/waypoints/jquery.waypoints'
], function ($) {
    'use strict';

    $.widget('magezon.countdown', {
        options: {
            nodeClass: '',
            wrapperClass: '',
            countdown: '',
            dateWrapper: '',
            dateLabel: '',
            hoursWrapper: '',
            hoursLabel: '',
            minutesWrapper: '',
            minutesLabel: '',
            secondsWrapper: '',
            secondsLabel: '',
            time: '2019-02-28T01:00:00+00:00',
            _timeInterval: '',
        },

        _create: function () {
            this.dateWrapper    = this.element.find('.mgz-countdown-days');
            this.dateLabel      = this.dateWrapper.find('.mgz-countdown-unit-label').data('label');
            this.hoursWrapper   = this.element.find('.mgz-countdown-hours');
            this.hoursLabel     = this.hoursWrapper.find('.mgz-countdown-unit-label').data('label');
            this.minutesWrapper = this.element.find('.mgz-countdown-minutes');
            this.minutesLabel   = this.minutesWrapper.find('.mgz-countdown-unit-label').data('label');
            this.secondsWrapper = this.element.find('.mgz-countdown-seconds');
            this.secondsLabel   = this.secondsWrapper.find('.mgz-countdown-unit-label').data('label');

            this._initCountdown();
        },

        _getTimeRemaining: function(endtime) {
            var t       = Date.parse(endtime) - Date.parse(new Date());
            var seconds = Math.floor((t / 1000) % 60);
            var minutes = Math.floor((t / 1000 / 60) % 60);
            var hours   = Math.floor((t / (1000 * 60 * 60)) % 24);
            var days    = Math.floor(t / (1000 * 60 * 60 * 24));
            return {
                'total': t,
                'days': (days < 10) ? ('0' + days) : days,
                'hours': ('0' + hours).slice(-2),
                'minutes': ('0' + minutes).slice(-2),
                'seconds': ('0' + seconds).slice(-2)
            };
        },

        _setTimeRemaining: function() {
            var t = this._getTimeRemaining(this.options.time),
                wrappers = {
                    days: $(this.dateWrapper),
                    hours: $(this.hoursWrapper),
                    minutes: $(this.minutesWrapper),
                    seconds: $(this.secondsWrapper)
                },
                labels = {
                    days: this.dateLabel,
                    hours: this.hoursLabel,
                    minutes: this.minutesLabel,
                    seconds: this.secondsLabel
                };
            if (t.total <= 0) {
                clearInterval(this._timeInterval);
                $.each(wrappers, function(type, element) {
                    element.find('.mgz-countdown-unit-number').html('00');
                });
            } else {
                $.each(wrappers, function(type, element) {
                    element.find('.mgz-countdown-unit-number').html(t[type]);
                    var $el = element.find('.mgz-countdown-unit-label');
                    var label = parseInt(t[type]) != 1 ? labels[type].plural : labels[type].singular;
                    $el.html(label);
                });
            }
        },

        _setCircleCount: function() {
            var t = this._getTimeRemaining(this.options.time),
                max = {
                    days: 365,
                    hours: 24,
                    minutes: 60,
                    seconds: 60
                },
                circles = {
                    days: $(this.dateWrapper).find('svg'),
                    hours: $(this.hoursWrapper).find('svg'),
                    minutes: $(this.minutesWrapper).find('svg'),
                    seconds: $(this.secondsWrapper).find('svg'),
                }
            $.each(circles, function(type, element) {
                var $circle = element.find('.mgz-element-bar'),
                    r = $circle.attr('r'),
                    circle = Math.PI * (r * 2),
                    val = t[type],
                    total = max[type],
                    stroke = (1 - (val / total)) * circle;
                $circle.css({
                    strokeDashoffset: stroke
                });
            });
        },

        _initCountdown: function() {
            var self = this;
            this._setTimeRemaining();
            if (this.options.type == 'circle') {
                this._setCircleCount();
            }

            this.interval = setInterval(function() {
                self._setTimeRemaining();
                if (self.options.type == 'circle') {
                    self._setCircleCount();
                }
            }, 1000);
        },

        clearInterval: function() {
            clearInterval(this.interval);
        }
    });

    return $.magezon.countdown;
});