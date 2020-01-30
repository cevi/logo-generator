/* global document, window */
import $ from 'jquery';

export default class Event {
    /**
     * @return {object}
     */
    static get EVENT() {
        return {
            RESIZE: 'cevi.resize.all',
            RESIZE_VERTICAL: 'cevi.resize.vertical',
            RESIZE_HORIZONTAL: 'cevi.resize.horizontal',
            SCROLL: 'cevi.scroll',
            WINDOW_LEAVE: 'cevi.window.leave',
            WINDOW_COME: 'cevi.window.come'
        };
    }

    /**
     * @param {string} eventName
     * @param {array|object} data
     */
    static trigger(eventName, data = null) {
        $(document).trigger(eventName, data);
    }

    /**
     * @param {string} eventName
     * @param {function} callback
     */
    static listen(eventName, callback) {
        $(document).on(eventName, callback);
    }

    /**
     * Register global resize events.
     */
    static registerGlobalWindowEvents() {
        let windowWidth = $(window).width();
        let windowHeight = $(window).height();

        $(window)
            .on('resize', () => {
                Event.trigger(Event.EVENT.RESIZE);

                // Is this a horizontal resize?
                if (windowWidth !== $(window).width()) {
                    Event.trigger(Event.EVENT.RESIZE_HORIZONTAL);
                    windowWidth = $(window).width();
                }

                // Is this a vertical resize?
                if (windowHeight !== $(window).height()) {
                    Event.trigger(Event.EVENT.RESIZE_VERTICAL);
                    windowHeight = $(window).height();
                }
            })
            .on('scroll', () => {
                Event.trigger(Event.EVENT.SCROLL);
            });
    }
}
