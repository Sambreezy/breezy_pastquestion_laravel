import React, { Component } from "react";
import { connect } from "react-redux";
import { loginValue, updatePassword } from "../../actions/loginActions";
import {
  getuserInfo,
  usereditValue,
  updateUser
} from "../../actions/UserActions";
import { withRouter } from "react-router-dom";
import Spinner from "../reusables/Spinner";

class UserEdit extends Component {
  componentWillMount() {
    const { id } = this.props.match.params;
    this.props.getuserInfo(id);
  }

  update = e => {
    const {
      singleusername,
      singleuserphone,
      singleuserdesc,
      singleuserid
    } = this.props;
    this.props.updateUser(
      singleusername,
      singleuserphone,
      singleuserdesc,
      singleuserid
    );

    e.preventDefault();
  };

  save = e => {
    const {
      current_password,
      new_password,
      confirm_password,
      singleuserid
    } = this.props;
    this.props.updatePassword(
      current_password,
      new_password,
      confirm_password,
      singleuserid
    );

    e.preventDefault();
  };
  render() {
    const {
      singleusername,
      singleuserphone,
      singleuserdesc,
      current_password,
      new_password,
      confirm_password,
      updateuserloading,
      updatepasswordloading
    } = this.props;

    return (
      <div className="profile-page sidebar-collapse">
        <div
          className="page-header header-filter"
          data-parallax="true"
          style={{
            backgroundImage: 'url("../images/city_dark.jpg")',
            height: 200
          }}
        />
        <div className="main main-raised">
          <div className="profile-content">
            <div className="container">
              <div className="row">
                <div className="col-lg-4 col-md-6 ml-auto mr-auto">
                  <div className="card card-login">
                    <form className="form">
                      <div
                        className="card-header card-header-primary text-center"
                        style={{ paddingBottom: 28 }}
                      >
                        <h4 className="card-title">Update Profile</h4>
                      </div>
                      <div className="card-body" style={{ paddingBottom: 40 }}>
                        <div className="input-group">
                          <div className="input-group-prepend">
                            <span className="input-group-text">
                              <i className="material-icons">face</i>
                            </span>
                          </div>
                          <input
                            name="singleusername"
                            type="name"
                            className="form-control"
                            placeholder="Name..."
                            value={singleusername}
                            onChange={e =>
                              this.props.usereditValue({
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
                            name="singleuserphone"
                            type="phone"
                            className="form-control"
                            placeholder="Phone..."
                            value={singleuserphone}
                            onChange={e =>
                              this.props.usereditValue({
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
                          <textarea
                            name="singleuserdesc"
                            type="text"
                            className="form-control"
                            rows={4}
                            placeholder="Description..."
                            value={singleuserdesc}
                            onChange={e =>
                              this.props.usereditValue({
                                props: e.target.name,
                                value: e.target.value
                              })
                            }
                          />
                        </div>
                      </div>
                      <div className="footer text-center">
                        {updateuserloading ? (
                          <Spinner />
                        ) : (
                          <div
                            onClick={this.update.bind(this)}
                            className="btn btn-primary btn-link btn-wd btn-lg"
                            style={{
                              backgroundColor: "#9c27b0",
                              color: "#fff"
                            }}
                          >
                            Update
                          </div>
                        )}
                      </div>
                    </form>
                  </div>
                </div>
                <div className="col-lg-4 col-md-6 ml-auto mr-auto">
                  <div className="card card-login">
                    <form className="form">
                      <div
                        className="card-header card-header-primary text-center"
                        style={{ paddingBottom: 28 }}
                      >
                        <h4 className="card-title">Change Password</h4>
                      </div>
                      <div className="card-body" style={{ paddingBottom: 40 }}>
                        <div className="input-group">
                          <div className="input-group-prepend">
                            <span className="input-group-text">
                              <i className="material-icons">lock_outline</i>
                            </span>
                          </div>
                          <input
                            name="current_password"
                            type="password"
                            className="form-control"
                            placeholder="Current Password..."
                            value={current_password}
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
                            name="new_password"
                            type="password"
                            className="form-control"
                            placeholder="New Password..."
                            value={new_password}
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
                            name="confirm_password"
                            type="password"
                            className="form-control"
                            placeholder="Confirm Password..."
                            value={confirm_password}
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
                      <div className="footer text-center">
                        {updatepasswordloading ? (
                          <Spinner />
                        ) : (
                          <div
                            onClick={this.save.bind(this)}
                            className="btn btn-primary btn-link btn-wd btn-lg"
                            style={{
                              backgroundColor: "#9c27b0",
                              color: "#fff"
                            }}
                          >
                            Save
                          </div>
                        )}
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer className="footer footer-default">
          <div className="container">
            <nav className="float-left"></nav>
            <div className="copyright float-right">ExamPaperOnline © 2019</div>
          </div>
        </footer>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  singleusername: state.user.singleusername,
  updateuserloading: state.user.updateuserloading,
  singleuserphone: state.user.singleuserphone,
  singleuserdesc: state.user.singleuserdesc,
  singleuserid: state.user.singleuserid,
  current_password: state.login.current_password,
  new_password: state.login.new_password,
  confirm_password: state.login.confirm_password,
  updatepasswordloading: state.login.updatepasswordloading
});

export default withRouter(
  connect(mapStateToProps, {
    getuserInfo,
    usereditValue,
    updateUser,
    loginValue,
    updatePassword
  })(UserEdit)
);
