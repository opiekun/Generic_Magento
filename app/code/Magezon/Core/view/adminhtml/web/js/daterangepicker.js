define([
    'jquery',
    'Magezon_Core/js/moment',
    'uiComponent',
    'Magezon_Core/js/daterangepicker/daterangepicker.min'
], function ($, moment, Component) {
    'use strict';

    var startDate, endDate;
    if (window.mgzReportsConfig.from) {
        startDate = moment(window.mgzReportsConfig.from, 'YYYY-MM-DD');
    } else {
        startDate = moment().startOf('month').hours(0).minutes(0);        
    }

    if (window.mgzReportsConfig.to) {
        endDate = moment(window.mgzReportsConfig.to, 'YYYY-MM-DD');
    } else {
        endDate = moment().endOf('month').hours(23).minutes(59);        
    }

    return Component.extend({
        defaults: {
            template: 'Magezon_Core/daterangepicker',
            from: startDate.format('YYYY-MM-DD'),
            to: endDate.format('YYYY-MM-DD'),
            startDate: startDate,
            endDate: endDate,
            minDate: '',
            maxDate: '',
            exports: {
                from: '${ $.provider }:params.from',
                to: '${ $.provider }:params.to',
                startDate: '${ $.provider }:data.startDate',
                endDate: '${ $.provider }:data.endDate'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe([
                    'from',
                    'to',
                    'startDate',
                    'endDate',
                    'isSameYear',
                    'isSameMonth',
                    'isSameDay'
                ]);

            return this;
        },

        onElementRender: function (element) {
            this.initDateRangePicker(element);
        },

        initDateRangePicker: function (element) {
            var self = this;

            var start = this.startDate();
            var end   = this.endDate();

            function cb(start, end) {
                $(element).find('.date-range-field').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            }

            $(element).daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment().hours(0).minutes(0), moment().hours(23).minutes(59)],
                    'Yesterday': [moment().add(-1, 'days').hours(0).minutes(0), moment().add(-1, 'days').hours(23).minutes(59)],
                    'This Week': [moment().startOf('week').hours(0).minutes(0), moment().endOf('week').hours(23).minutes(59)],
                    'This Month': [moment().startOf('month').hours(0).minutes(0), moment().endOf('month').hours(23).minutes(59)],
                    'Last Week': [moment().subtract(1, 'weeks').startOf('week').hours(0).minutes(0), moment().subtract(1, 'weeks').endOf('week').hours(23).minutes(59)],
                    'Last Month': [moment().add(-1, 'month').startOf('month').hours(0).minutes(0), moment().add(-1, 'month').endOf('month').hours(23).minutes(59)]
                },
                showDropdowns: true,
                alwaysShowCalendars: true,
                minDate: self.minDate,
                maxDate: self.maxDate,
                template: '<div id="mgzreports-datepicker" class="daterangepicker ' + self.reportType + '">' +
                    '<div class="ranges"></div>' +
                    '<div class="drp-calendar left">' +
                        '<div class="calendar-table"></div>' +
                        '<div class="calendar-time"></div>' +
                    '</div>' +
                    '<div class="drp-calendar right">' +
                        '<div class="calendar-table"></div>' +
                        '<div class="calendar-time"></div>' +
                    '</div>' +
                    '<div class="drp-buttons">' +
                        '<span class="drp-selected"></span>' +
                        '<button class="cancelBtn" type="button"></button>' +
                        '<button class="applyBtn" disabled="disabled" type="button"></button> ' +
                    '</div>' +
                '</div>',
                applyButtonClasses: 'primary',
                cancelClass: 'action-secondary'
            }).on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate);
                var from = picker.startDate.second(0).format('YYYY-MM-DD');
                var to   = picker.endDate.second(0).format('YYYY-MM-DD');
                self.from(from);
                self.to(to);
                self.startDate(picker.startDate);
                self.endDate(picker.endDate);
            }).on('show.daterangepicker', function () {
                $(element).addClass('datepicker-active');
            }).on('hide.daterangepicker', function () {
                $(element).removeClass('datepicker-active');
            });
            cb(start, end);
        }
    })
});