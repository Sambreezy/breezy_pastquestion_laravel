import React, { Component } from 'react';
import { loginValue, forgotloginUser } from '../../actions/loginActions';
import { connect } from 'react-redux';
import Spinner from '../reusables/Spinner';

class ForgotPassword extends Component {
  render() {
    const { forgotemail, loading } = this.props;
    return (
      <div class="login-page sidebar-collapse">
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
                  <form className="form" method action>
                    <div
                      className="card-header card-header-primary text-center"
                      style={{ paddingBottom: 28 }}
                    >
                      <h4 className="card-title">Forgot Password</h4>
                    </div>

                    <div className="card-body">
                      <div className="input-group" style={{ paddingTop: 40 }}>
                        <div className="input-group-prepend">
                          <span className="input-group-text">
                            <i className="material-icons">mail</i>
                          </span>
                        </div>
                        <input
                          type="email"
                          name="forgotemail"
                          className="form-control"
                          value={forgotemail}
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
                    </div>
                    <div
                      className="footer text-center"
                      style={{ marginBottom: 20 }}
                    >
                      {loading ? (
                        <Spinner />
                      ) : (
                        <div
                          onClick={e => this.props.forgotloginUser(forgotemail)}
                          className="btn btn-primary btn-link btn-wd btn-lg"
                          style={{ backgroundColor: '#9c27b0', color: '#fff' }}
                        >
                          Send
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
  forgotemail: state.login.forgotemail,
  loading: state.login.loading
});

export default connect(
  mapStateToProps,
  { loginValue, forgotloginUser }
)(ForgotPassword);
