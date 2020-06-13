import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { loginValue } from '../../actions/loginActions';
import { loginUser } from '../../actions/loginActions';
import Spinner from '../reusables/Spinner';

class Login extends Component {
  componentWillReceiveProps(nextProps) {
    if (nextProps.auth) {
      this.props.history.push('/dashboard');
    }
  }

  render() {
    const { email, password, loading } = this.props;
    return (
      <div className="login-page sidebar-collapse">
        <div
          className="page-header header-filter"
          style={{
            backgroundImage: 'url("../images/bg7.jpg")',
            backgroundSize: 'cover',
            backgroundPosition: 'top center'
          }}
        >
          <div className="container">
            <div className="row">
              <div className="col-lg-4 col-md-6 ml-auto mr-auto">
                <div className="card card-login">
                  <form className="form">
                    <div
                      className="card-header card-header-primary text-center"
                      style={{ paddingBottom: 28 }}
                    >
                      <h4 className="card-title">Log In</h4>
                    </div>
                    <Link
                      to="/login/forgotpassword"
                      style={{ textDecoration: 'none' }}
                    >
                      <p className="description text-center">
                        Forgot Password?
                      </p>
                    </Link>
                    <div className="card-body">
                      <div className="input-group">
                        <div className="input-group-prepend">
                          <span className="input-group-text">
                            <i className="material-icons">mail</i>
                          </span>
                        </div>
                        <input
                          type="email"
                          name="email"
                          className="form-control"
                          value={email}
                          placeholder="Email..."
                          required
                          onChange={e =>
                            this.props.loginValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                        />
                      </div>
                      <div className="input-group">
                        <div className="input-group-prepend">
                          <span className="input-group-text">
                            <i className="material-icons">lock_outline</i>
                          </span>
                        </div>
                        <input
                          type="password"
                          name="password"
                          value={password}
                          className="form-control"
                          placeholder="Password..."
                          required
                          onChange={e =>
                            this.props.loginValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                        />
                      </div>
                    </div>
                    <div
                      className="footer text-center"
                      style={{ marginBottom: 50 }}
                    >
                      {loading ? (
                        <Spinner />
                      ) : (
                        <div
                          onClick={e => this.props.loginUser(email, password)}
                          className="btn btn-primary btn-link btn-wd btn-lg"
                          style={{ backgroundColor: '#9c27b0', color: '#fff' }}
                        >
                          Log In
                        </div>
                      )}
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <footer className="footer footer-default">
            <div className="container">
              <nav className="float-left"></nav>
              <div className="copyright float-right">
                ExamPaperOnline © 2019
              </div>
            </div>
          </footer>
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  email: state.login.email,
  password: state.login.password,
  auth: state.login.auth,
  loading: state.login.loading
});

export default connect(
  mapStateToProps,
  { loginValue, loginUser }
)(Login);
