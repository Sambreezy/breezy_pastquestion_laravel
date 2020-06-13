import React, { Component } from "react";
import {
  getsingleItem,
  getpastQuestion,
  votelikeQuestion,
  votedislikeQuestion,
  uploadparamValue,
  commentQuestion,
  uploadpquestionValue,
  getjsvoteup,
  getjsvotedown
} from "../../actions/UploadPquestionActions";
import { getuserInfo } from "../../actions/UserActions";
import { Link } from "react-router-dom";
import { connect } from "react-redux";
import Spinner from "../reusables/Spinner";

import { withRouter } from "react-router-dom";

class SingleItem extends Component {
  componentWillMount() {
    const { id } = this.props.match.params;
    this.props.getsingleItem(id);
    this.props.uploadparamValue(id);
    this.props.getpastQuestion();
  }

  voteLike = e => {
    const { id } = this.props.match.params;
    this.props.votelikeQuestion(id);
    const { singleitem } = this.props;
    let vote_up = (singleitem.vote_up += 1);
    this.props.getjsvoteup(vote_up);
    e.preventDefault();
  };

  voteDislike = e => {
    const { id } = this.props.match.params;
    this.props.votedislikeQuestion(id);
    const { singleitem } = this.props;
    let vote_down = (singleitem.vote_down += 1);
    this.props.getjsvotedown(vote_down);
    e.preventDefault();
  };

  commentCall = e => {
    const { singleitemid } = this.props;
    const { comment } = this.props;
    this.props.commentQuestion(comment, singleitemid);

    e.preventDefault();
  };

  render() {
    const {
      singleuser,
      singleitem,
      singledocs,
      singleimages,
      singlecomments,
      commentquestionloading,
      comment,
      jsvoteupshow,
      jsvotedownshow
    } = this.props;

    let usercommentpix = [];
    for (let i = 0; i < singlecomments.length; i++) {
      usercommentpix.push(
        singlecomments[i].user_picture == null
          ? ""
          : singlecomments[i].user_picture.replace(
              "public/profile/",
              "https://pastquestions.xyz/storage/profile/"
            )
      );
    }
    console.log(usercommentpix, "i am usercommentpix");
    return (
      <div className="landing-page sidebar-collapse">
        <div
          className="page-header header-filter"
          data-parallax="true"
          style={{
            backgroundImage: 'url("../../images/city_light.jpg")',
            height: 450
          }}
        >
          <div className="container">
            <div className="row">
              <div className="col-md-6 ml-auto mr-auto">
                <div className="text-center">
                  <h2>{singleitem.course_name}</h2>
                  <h5>{singleitem.course_code}</h5>
                  <h5>{singleitem.semester} semester</h5>
                  <h5>{singleitem.year}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="main main-raised">
          <div className="container">
            {singleimages.length > 0 ? (
              <div className="section text-center">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="title" style={{ paddingBottom: 15 }}>
                      Images{" "}
                    </h2>
                    <div className="description">
                      <div className="row">
                        {singleimages.map(singleimage => (
                          <div className="col-md-4 mb-3">
                            <a
                              href={`https://pastquestions.xyz/storage/images/${singleimage.image_name}`}
                              target="blank"
                            >
                              <img
                                key={singleimage.id}
                                alt="display"
                                src={`https://pastquestions.xyz/storage/images/${singleimage.image_name}`}
                                height="120"
                                width="120"
                              />
                            </a>
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
                    <h2 className="text-center title">No Image available</h2>
                  </div>
                </div>
              </div>
            )}

            {singledocs.length > 0 ? (
              <div className="section">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="text-center title">Documents</h2>

                    {singledocs.map(singledoc => (
                      <div className="row">
                        <div className="col-md-2.5 ml-auto mr-auto">
                          <a
                            key={singledoc.id}
                            href={`https://pastquestions.xyz/storage/documents/${singledoc.doc_name}`}
                            style={{ width: 330 }}
                            className="btn btn-success"
                            target="blank"
                            download
                          >
                            <span className="fa fa-download" /> Download{" "}
                            {singledoc.doc_name}
                          </a>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            ) : (
              <div className="section">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="text-center title">No Document available</h2>
                  </div>
                </div>
              </div>
            )}

            <div className="section">
              <div className="row">
                <div className="col-md-2.5 ml-auto mr-auto">
                  <div className="profile-tabs">
                    <h6 style={{ paddingTop: -6 }}>
                      {" "}
                      <button
                        className="btn btn-primary btn-raised"
                        onClick={this.voteLike.bind(this)}
                      >
                        Like <i className="fas fa-thumbs-up" />
                      </button>{" "}
                      <span style={{ fontSize: 20, paddingLeft: 7 }}>
                        {jsvoteupshow === "" ? (
                          <b>{singleitem.vote_up}</b>
                        ) : (
                          <b>{jsvoteupshow}</b>
                        )}
                      </span>{" "}
                      <button
                        className="btn btn-primary btn-raised"
                        onClick={this.voteDislike.bind(this)}
                        style={{
                          marginLeft: 30,
                          backgroundColor: "red",
                          border: "1px solid red"
                        }}
                      >
                        Dislike <i className="fas fa-thumbs-down" />
                      </button>{" "}
                      <span style={{ fontSize: 20, paddingLeft: 7 }}>
                        {jsvotedownshow === "" ? (
                          <b>{singleitem.vote_down}</b>
                        ) : (
                          <b>{jsvotedownshow}</b>
                        )}
                      </span>
                    </h6>
                  </div>
                </div>
              </div>
            </div>

            <div className="section">
              <div className="row">
                <div className="col-md-2.5 ml-auto mr-auto">
                  <h6>
                    <span>
                      <b>Uploaded By:</b>
                    </span>{" "}
                    <Link
                      to={`/uploaduser/${singleuser.id}`}
                      style={{ textDecoration: "none" }}
                    >
                      <b>{singleuser.name}</b>
                    </Link>
                  </h6>
                </div>
              </div>
            </div>

            {singlecomments.length > 0 ? (
              <div className="section-contacts">
                <div id="nav-tabs">
                  <div className="row">
                    <div className="col-md-6 ml-auto mr-auto">
                      <h5 className="text-center title">
                        <i>Users' review</i>
                      </h5>
                      {singlecomments.map((singlecomment, index) => (
                        <div style={{ paddingBottom: 1 }}>
                          <div className="card card-nav-tabs">
                            <div className="card-header card-header-primary">
                              <div className="nav-tabs-navigation">
                                <div className="nav-tabs-wrapper">
                                  <div className="row">
                                    <div className="col-md-2">
                                      <img
                                        src={
                                          usercommentpix[index] == ""
                                            ? "/images/noimage.jpg"
                                            : usercommentpix[index]
                                        }
                                        alt="Circle"
                                        className="rounded-circle"
                                        style={{ height: 40, width: 40 }}
                                      />
                                    </div>
                                    <div className="col-md-10">
                                      <h5> {singlecomment.user_name} </h5>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div className="card-body ">
                              <div className="tab-content text-center">
                                <div>
                                  <p> {singlecomment.comment}</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </div>
            ) : (
              <div className="section">
                <div className="row">
                  <div className="col-md-8 ml-auto mr-auto">
                    <h2 className="text-center title">No Comment available</h2>
                  </div>
                </div>
              </div>
            )}

            <div className="section section-contacts">
              <div className="row">
                <div className="col-md-6 ml-auto mr-auto">
                  <h4 className="title">Leave your comment</h4>
                  <form className="contact-form">
                    <textarea
                      type="text"
                      name="comment"
                      className="form-control"
                      rows={5}
                      value={comment}
                      required
                      onChange={e =>
                        this.props.uploadpquestionValue({
                          props: e.target.name,
                          value: e.target.value
                        })
                      }
                    />
                    <div className="row" style={{ marginTop: 20 }}>
                      <div className="col-md-4 ml-auto mr-auto text-center">
                        {commentquestionloading ? (
                          <Spinner />
                        ) : (
                          <button
                            onClick={this.commentCall.bind(this)}
                            className="btn btn-primary btn-raised"
                          >
                            Comment
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
  singleitem: state.uploadpquestion.singleitem,
  voteup: state.uploadpquestion.voteup,
  singledocs: state.uploadpquestion.singledocs,
  singleimages: state.uploadpquestion.singleimages,
  singlecomments: state.uploadpquestion.singlecomments,
  singlecommentuser: state.uploadpquestion.singlecommentuser,
  commentquestionloading: state.uploadpquestion.commentquestionloading,
  jsvoteupshow: state.uploadpquestion.jsvoteupshow,
  jsvotedownshow: state.uploadpquestion.jsvotedownshow,
  prev: state.uploadpquestion.prev,
  next: state.uploadpquestion.next,
  comment: state.uploadpquestion.comment,
  singleitemid: state.uploadpquestion.singleitemid,
  singleuser: state.user.singleuser
});

export default withRouter(
  connect(
    mapStateToProps,
    {
      getsingleItem,
      getpastQuestion,
      votelikeQuestion,
      votedislikeQuestion,
      uploadparamValue,
      commentQuestion,
      uploadpquestionValue,
      getuserInfo,
      getjsvoteup,
      getjsvotedown
    }
  )(SingleItem)
);
