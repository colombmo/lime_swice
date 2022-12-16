var pickers = {};

/**
 * returns a basic config object
 * @param locale
 * @param dateFormat
 * @returns {{localization: {locale}, display: {components: {clock: boolean}, icons: {date: string, next: string, previous: string, today: string, clear: string, time: string, up: string, down: string, close: string}}}}
 */
function getConfig(locale, dateFormat) {
    var clock = dateFormat.indexOf('HH:mm') !== -1;
    return {
        localization: {
            locale: locale
        },
        display: {
            icons: {
                time: 'ri-time-line text-success',
                date: 'ri-calendar-2-fill text-success',
                up: 'ri-arrow-up-s-fill',
                down: 'ri-arrow-down-s-fill',
                previous: 'ri-arrow-left-s-fill',
                next: 'ri-arrow-right-s-fill',
                today: 'ri-calendar-check-fill text-success',
                clear: 'ri-delete-bin-fill text-danger',
                close: 'ri-close-fill text-danger',
            },
            components: {
                useTwentyfourHour: true,
                clock: clock,
            },
        },
    };
}

/**
 * manipulates weak handling of tempus datetimepicker with dateformats
 * @param id
 * @param format
 * @param elemDate
 */
function setDatePickerFormat(id, format, elemDate) {
    // formatting when selected via datepicker
    id.dates.formatInput = function (date) {
        if (date !== null) {
            return moment(date).format(format);
        }
        return null;
    };

    // converting with moment.js
    id.dates.setFromInput = function (value, index) {
        let converted = moment(value, format);
        if (converted.isValid()) {
            let date = tempusDominus.DateTime.convert(converted.toDate(), this.optionsStore.options.localization.locale);
            this.setValue(date, index);
        }
    };
    //workaround: formatting when value is loaded on pageload
    if (elemDate) {
        id.dates.setFromInput(elemDate);
    }
}

/**
 * inits a basic version of tempus dominus datepicker.
 * For more functionality outside of pure js inits use the
 * ext.DateTimePickerWidget.DateTimePicker widget.
 * @param element
 * @param constName
 * @param locale
 * @param dateFormat
 */
function initDatePicker(element, constName, locale, dateFormat) {
    var config = getConfig(locale, dateFormat);
    constName = 'picker_' + constName;
    pickers[constName] = new tempusDominus.TempusDominus(element, config);
    setDatePickerFormat(pickers[constName], dateFormat, element.value);
    attachCalendarIconToDatepicker();
}


/**
 * Open datepicker via click on calendar icon.
 * This is needed for the datepicker fields in the tokenform.
 */
function attachCalendarIconToDatepicker() {
    $(document).off('click', '.datepicker-icon');
    $(document).on('click', '.datepicker-icon', function () {
        $(this).prevAll('input').click();
    });
}