/* global window */

// Import the components.
import Logo from './Components/Logo';

// Import the helpers.
import Event from './Helper/Event';

class App {
    static registerHelpers() {
        Event.registerGlobalWindowEvents();
    }

    static registerComponents() {
        // Self-initiating components:
        new Logo(); // eslint-disable-line no-new
    }

    constructor() {
        App.registerHelpers();
        App.registerComponents();

        console.log('construct');
    }
}

window.App = new App();
