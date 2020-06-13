import React, { Component } from 'react';
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';
import Header from './components/layout/Header';
import NotFound from './components/pages/NotFound';
import Landing from './components/users/Landing';
import Login from './components/users/Login';
import Signup from './components/users/Signup';
import User from './components/users/User';
import UploadUser from './components/users/UploadUser';
import UploadQuestion from './components/users/UploadQuestion';
import UserEdit from './components/users/UserEdit';
import Dashboard from './components/users/Dashboard';
import SingleItem from './components/users/SingleItem';
import ForgotPassword from './components/users/ForgotPassword';
import ChangePassword from './components/users/ChangePassword';
import { initializeUser } from './actions/loginActions';
import { connect } from 'react-redux';

class Router extends Component {
  componentWillMount() {
    this.props.initializeUser();
  }

  render() {
    const { loggedIn } = this.props;
    return (
      <BrowserRouter>
        <div className="App">
          <Header />
          <Switch>
            <Route
              exact
              path="/"
              render={() =>
                loggedIn === null ? <Landing /> : <Redirect to="/dashboard" />
              }
            />
            <Route
              exact
              path="/login"
              render={() =>
                loggedIn === null ? <Login /> : <Redirect to="/dashboard" />
              }
            />
            <Route
              exact
              path="/login/forgotpassword"
              render={() =>
                loggedIn === null ? (
                  <ForgotPassword />
                ) : (
                  <Redirect to="/dashboard" />
                )
              }
            />
            <Route
              exact
              path="/changepassword"
              render={() =>
                loggedIn ? <ChangePassword /> : <Redirect to="/" />
              }
            />
            <Route
              exact
              path="/signup"
              render={() =>
                loggedIn === null ? <Signup /> : <Redirect to="/dashboard" />
              }
            />
            <Route
              exact
              path="/user"
              render={() => (loggedIn ? <User /> : <Redirect to="/" />)}
            />
            <Route
              exact
              path="/uploadquestion"
              render={() =>
                loggedIn ? <UploadQuestion /> : <Redirect to="/" />
              }
            />
            <Route
              exact
              path="/uploaduser/:id"
              render={() => (loggedIn ? <UploadUser /> : <Redirect to="/" />)}
            />
            <Route
              exact
              path="/user/edit/:id"
              render={() => (loggedIn ? <UserEdit /> : <Redirect to="/" />)}
            />
            <Route
              exact
              path="/dashboard"
              render={() => (loggedIn ? <Dashboard /> : <Redirect to="/" />)}
            />
            <Route
              exact
              path="/dashboard/singleitem/:id"
              render={() => (loggedIn ? <SingleItem /> : <Redirect to="/" />)}
            />

            <Route component={NotFound} />
          </Switch>
        </div>
      </BrowserRouter>
    );
  }
}

const MapStateToProps = () => {
  let loggedIn = JSON.parse(localStorage.getItem('token'));

  return {
    loggedIn
  };
};

export default connect(
  MapStateToProps,
  { initializeUser }
)(Router);
