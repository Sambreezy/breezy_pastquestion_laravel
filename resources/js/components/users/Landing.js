import React, { Component } from "react";
import {
  getfirstpastQuestion,
  uploadpquestionValue,
  sendusMessage
} from "../../actions/UploadPquestionActions";
import { connect } from "react-redux";
import Spinner from "../reusables/Spinner";

class Landing extends Component {
  componentWillMount() {
    this.props.getfirstpastQuestion();
  }

  sendMessage = e => {
    const { all_name, all_email, all_message } = this.props;

    this.props.sendusMessage(all_name, all_email, all_message);

    e.preventDefault();
  };
  render() {
    const {
      firstquestions,
      all_name,
      all_email,
      all_message,
      contactusloading
    } = this.props;
    return (
      <div className="landing-page sidebar-collapse">
        <div
          className="page-header header-filter"
          data-parallax="true"
          style={{ backgroundImage: 'url("../images/city_light.jpg")' }}
        >
          <div className="container">
            <div className="row">
              <div className="col-md-6">
                <h1 className="title">Your Exam Success Starts With Us.</h1>
                <h4>
                  This platform connects students from all over the world and
                  enables them share documents and pictures which can be
                  beneficial to the exam success of their colleagues.
                </h4>
                <br />
                <a
                  href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                  target="blank"
                  className="btn btn-danger btn-raised btn-lg"
                >
                  <i className="fas fa-download" /> Get App Now
                </a>
              </div>
            </div>
          </div>
        </div>
        <div className="main main-raised">
          <div className="container">
            {firstquestions.length > 0 ? (
              <div className="section text-center">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="title" style={{ paddingBottom: 15 }}>
                      Recent Uploads{" "}
                    </h2>
                    <div className="description">
                      <div className="list-group">
                        {firstquestions.map(firstquestion => (
                          <div
                            className="list-group-item"
                            key={firstquestion.id}
                          >
                            <h4 className="list-group-item-heading">
                              {firstquestion.course_name}
                            </h4>
                            <p
                              className="list-group-item-text"
                              style={{ fontSize: 18 }}
                            >
                              {firstquestion.course_code}
                            </p>
                            <p
                              className="list-group-item-text"
                              style={{ fontSize: 18 }}
                            >
                              {firstquestion.semester} semester
                            </p>
                            <p
                              className="list-group-item-text"
                              style={{ fontSize: 18 }}
                            >
                              {firstquestion.year}
                            </p>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ) : (
              <div className="section">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="text-center title">No upload</h2>
                  </div>
                </div>
              </div>
            )}
            <div className="section section-about" id="about">
              <div className="row">
                <div className="col-md-8 ml-auto mr-auto">
                  <h2 className="text-center title">About Us</h2>
                  <h4 className="text-center description">
                    Divide details about your product or agency work into parts.
                    Write a few lines about each one and contact us about any
                    further collaboration. We will responde get back to you in a
                    couple of hours.
                  </h4>
                </div>
              </div>
            </div>
            <div className="section section-contacts" id="contact">
              <div className="row">
                <div className="col-md-8 ml-auto mr-auto">
                  <h2 className="text-center title">Contact Us</h2>
                  <h4
                    className="text-center description"
                    style={{ fontSize: 18, paddingBottom: 25 }}
                  >
                    We would love to hear from you. Divide details about your
                    product or agency work into parts. Write a few lines about
                    each one and contact us about any further collaboration. We
                    will responde get back to you in a couple of hours.
                  </h4>
                  <div className="row" style={{ paddingBottom: 18 }}>
                    <div className="col-md-6">
                      <div className="form-group">
                        <label
                          className="bmd-label-floating"
                          style={{ fontSize: 16 }}
                        >
                          <i className="fa fa-phone" /> Phone: 08032377771
                        </label>
                        <br />
                        <label
                          className="bmd-label-floating"
                          style={{ fontSize: 16 }}
                        >
                          <i className="fa fa-envelope" /> Email:
                          exampaperonline@gmail.com
                        </label>
                      </div>
                    </div>
                  </div>
                  <form className="contact-form">
                    <div className="row">
                      <div className="col-md-6">
                        <div className="form-group">
                          <label className="bmd-label-floating">
                            Your Name
                          </label>
                          <input
                            name="all_name"
                            type="text"
                            className="form-control"
                            value={all_name}
                            required
                            onChange={e =>
                              this.props.uploadpquestionValue({
                                props: e.target.name,
                                value: e.target.value
                              })
                            }
                          />
                        </div>
                      </div>
                      <div className="col-md-6">
                        <div className="form-group">
                          <label className="bmd-label-floating">
                            Your Email
                          </label>
                          <input
                            name="all_email"
                            type="email"
                            className="form-control"
                            value={all_email}
                            required
                            onChange={e =>
                              this.props.uploadpquestionValue({
                                props: e.target.name,
                                value: e.target.value
                              })
                            }
                          />
                        </div>
                      </div>
                    </div>
                    <div className="form-group">
                      <label
                        htmlFor="exampleMessage"
                        className="bmd-label-floating"
                      >
                        Your Message
                      </label>
                      <textarea
                        name="all_message"
                        type="text"
                        className="form-control"
                        rows={4}
                        value={all_message}
                        required
                        onChange={e =>
                          this.props.uploadpquestionValue({
                            props: e.target.name,
                            value: e.target.value
                          })
                        }
                      />
                    </div>
                    <div className="row">
                      <div className="col-md-4 ml-auto mr-auto text-center">
                        {contactusloading ? (
                          <Spinner />
                        ) : (
                          <button
                            onClick={this.sendMessage.bind(this)}
                            className="btn btn-primary btn-raised"
                          >
                            Send Message
                          </button>
                        )}
                      </div>
                    </div>
                  </form>
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
  firstquestions: state.uploadpquestion.firstquestions,
  contactusloading: state.uploadpquestion.contactusloading,
  all_name: state.uploadpquestion.all_name,
  all_email: state.uploadpquestion.all_email,
  all_message: state.uploadpquestion.all_message
});

export default connect(mapStateToProps, {
  getfirstpastQuestion,
  uploadpquestionValue,
  sendusMessage
})(Landing);
