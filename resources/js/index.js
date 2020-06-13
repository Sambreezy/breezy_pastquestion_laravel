import React, { Component } from "react";
import ReactDOM from "react-dom";
import Router from "./Router";
import { Provider } from "react-redux";
import store from "./store";
import registerServiceWorker from "./registerServiceWorker";


export default class Root extends Component {
    render() {
      return (
        <Provider store={store}>
          <Router />
        </Provider>
      );
    }
};

if (document.getElementById('root')) {
    ReactDOM.render(<Root />, document.getElementById('root'));
    registerServiceWorker();
}
