import React, { Component } from 'react';
import { connect } from 'react-redux';
import { signupValue } from '../../actions/signupActions';
import { signupUser } from '../../actions/signupActions';
import Spinner from '../reusables/Spinner';

class Signup extends Component {
  componentWillReceiveProps(nextProps) {
    if (nextProps.reg) {
      this.props.history.push('/login');
    }
  }

  render() {
    const { name, email, phone, password, confpassword, loading } = this.props;

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
                      <h4 className="card-title">Sign Up</h4>
                    </div>
                    <div className="card-body" style={{ paddingBottom: 85 }}>
                      <div className="input-group">
                        <div className="input-group-prepend">
                          <span className="input-group-text">
                            <i className="material-icons">face</i>
                          </span>
                        </div>
                        <input
                          type="name"
                          name="name"
                          className="form-control"
                          value={name}
                          placeholder="Name..."
                          required
                          onChange={e =>
                            this.props.signupValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                        />
                      </div>
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
                            this.props.signupValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                        />
                      </div>
                      <div className="input-group">
                        <div className="input-group-prepend">
                          <span className="input-group-text">
                            <i className="material-icons">phone</i>
                          </span>
                        </div>
                        <input
                          type="phone"
                          name="phone"
                          value={phone}
                          className="form-control"
                          placeholder="Phone..."
                          required
                          onChange={e =>
                            this.props.signupValue({
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
                            this.props.signupValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                          autoComplete="on"
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
                          name="confpassword"
                          value={confpassword}
                          className="form-control"
                          placeholder="Confirm Password..."
                          required
                          onChange={e =>
                            this.props.signupValue({
                              props: e.target.name,
                              value: e.target.value
                            })
                          }
                          autoComplete="on"
                        />
                      </div>
                    </div>
                    <div
                      className="footer text-center"
                      style={{ paddingBottom: 10 }}
                    >
                      {loading ? (
                        <Spinner />
                      ) : (
                        <div
                          onClick={e =>
                            this.props.signupUser(
                              name,
                              email,
                              phone,
                              password,
                              confpassword
                            )
                          }
                          className="btn btn-primary btn-link btn-wd btn-lg"
                          style={{ backgroundColor: '#9c27b0', color: '#fff' }}
                        >
                          Sign Up
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
  name: state.signup.name,
  email: state.signup.email,
  phone: state.signup.phone,
  password: state.signup.password,
  confpassword: state.signup.confpassword,
  loading: state.signup.loading,
  reg: state.login.reg
});

export default connect(
  mapStateToProps,
  { signupValue, signupUser }
)(Signup);
