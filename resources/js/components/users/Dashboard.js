import React, { Component } from "react";
import { Link } from "react-router-dom";
import {
  uploadpquestionValue,
  imgdocValue,
  getpastQuestion,
  searchpastQuestion,
  getprevpastQuestion
} from "../../actions/UploadPquestionActions";
import { connect } from "react-redux";
import { isNull } from "util";
import Spinner from "../reusables/Spinner";

class Dashboard extends Component {
  componentWillMount() {
    this.props.getpastQuestion();
  }

  search = e => {
    const { search, next } = this.props;
    this.props.searchpastQuestion(search);
    e.preventDefault();
  };

  prev = e => {
    const { prev } = this.props;
    this.props.getprevpastQuestion(prev);
    e.preventDefault();
  };

  next = e => {
    const { next } = this.props;
    this.props.getprevpastQuestion(next);
    e.preventDefault();
  };
  render() {
    const {
      questions,
      prev,
      next,
      search,
      results,
      searchquestionloading,
      results_state
    } = this.props;

    console.log(questions);
    console.log(results);
    console.log(next);

    return (
      <div className="landing-page sidebar-collapse">
        <div
          className="page-header header-filter"
          data-parallax="true"
          style={{
            backgroundImage: 'url("../images/city_light.jpg")',
            height: 450
          }}
        >
          <div className="container">
            <div className="row">
              <div className="col-md-6">
                <div id="buttons" className="cd-section">
                  <form>
                    <div className="input-group input-group-lg">
                      <input
                        type="text"
                        name="search"
                        className="form-control"
                        value={search}
                        required
                        placeholder="Search Question Papers..."
                        onChange={e =>
                          this.props.uploadpquestionValue({
                            props: e.target.name,
                            value: e.target.value
                          })
                        }
                      />
                    </div>
                    <br />
                    <button
                      className="btn btn-primary btn-lg"
                      onClick={this.search.bind(this)}
                    >
                      Search
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="main main-raised">
          {searchquestionloading ? (
            <div
              className="container"
              style={{ paddingTop: 100, paddingBottom: 100 }}
            >
              <Spinner />
            </div>
          ) : (
            <div className="container">
              {results_state === true && results.length > 0 ? (
                <div className="section text-center">
                  <div className="row">
                    <div className="col-md-8 ml-auto mr-auto">
                      <h2 className="title" style={{ paddingBottom: 15 }}>
                        Search Result{" "}
                      </h2>
                      <div className="description">
                        <div className="list-group">
                          {results.map(result => (
                            <Link
                              to={`dashboard/singleitem/${result.id}`}
                              style={{ textDecoration: "none" }}
                            >
                              <div className="list-group-item" key={result.id}>
                                <h4 className="list-group-item-heading">
                                  {result.course_name}
                                </h4>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {result.course_code}
                                </p>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {result.semester} semester
                                </p>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {result.year}
                                </p>
                              </div>
                            </Link>
                          ))}
                        </div>
                        <div id="progress">
                          <ul className="pagination pagination-info mt-3">
                            <li className="page-item">
                              {isNull(prev) ? (
                                <span>
                                  <i className="fa fa-chevron-left" /> prev
                                </span>
                              ) : (
                                <span
                                  onClick={this.prev.bind(this)}
                                  className="page-link"
                                  style={{ color: "#187bff" }}
                                >
                                  <span className="fa fa-chevron-left" /> prev
                                </span>
                              )}
                            </li>
                            <li className="page-item">
                              {isNull(next) ? (
                                <span>
                                  next <i className="fa fa-chevron-right" />
                                </span>
                              ) : (
                                <span
                                  onClick={this.next.bind(this)}
                                  className="page-link"
                                  style={{ color: "#187bff" }}
                                >
                                  next <span className="fa fa-chevron-right" />
                                </span>
                              )}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ) : results_state === true && results.length === 0 ? (
                <div className="section">
                  <div className="row">
                    <div className="col-md-8 ml-auto mr-auto">
                      <h2 className="text-center title">
                        Search Result Not Found
                      </h2>
                    </div>
                  </div>
                </div>
              ) : (
                <div className="section text-center">
                  <div className="row">
                    <div className="col-md-8 ml-auto mr-auto">
                      <h2 className="title" style={{ paddingBottom: 15 }}>
                        Recent Upload{" "}
                      </h2>
                      <div className="description">
                        <div className="list-group">
                          {questions.map(question => (
                            <Link
                              to={`dashboard/singleitem/${question.id}`}
                              style={{ textDecoration: "none" }}
                            >
                              <div
                                className="list-group-item "
                                key={question.id}
                              >
                                <h4 className="list-group-item-heading">
                                  {question.course_name}
                                </h4>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {question.course_code}
                                </p>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {question.semester} semester
                                </p>
                                <p
                                  className="list-group-item-text"
                                  style={{ fontSize: 18 }}
                                >
                                  {question.year}
                                </p>
                              </div>
                            </Link>
                          ))}
                        </div>
                        <div id="progress">
                          <ul className="pagination pagination-info mt-3">
                            <li className="page-item">
                              {isNull(prev) ? (
                                <span>
                                  <i className="fa fa-chevron-left" /> prev
                                </span>
                              ) : (
                                <span
                                  onClick={this.prev.bind(this)}
                                  className="page-link"
                                  style={{ color: "#187bff" }}
                                >
                                  <span className="fa fa-chevron-left" /> prev
                                </span>
                              )}
                            </li>
                            <li className="page-item">
                              {isNull(next) ? (
                                <span>
                                  next <i className="fa fa-chevron-right" />
                                </span>
                              ) : (
                                <span
                                  onClick={this.next.bind(this)}
                                  className="page-link"
                                  style={{ color: "#187bff" }}
                                >
                                  next <span className="fa fa-chevron-right" />
                                </span>
                              )}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}
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
  questions: state.uploadpquestion.questions,
  results_state: state.uploadpquestion.results_state,
  prev: state.uploadpquestion.prev,
  next: state.uploadpquestion.next,
  search: state.uploadpquestion.search,
  searchquestionloading: state.uploadpquestion.searchquestionloading,
  results: state.uploadpquestion.results
});

export default connect(mapStateToProps, {
  uploadpquestionValue,
  imgdocValue,
  getpastQuestion,
  searchpastQuestion,
  getprevpastQuestion
})(Dashboard);
