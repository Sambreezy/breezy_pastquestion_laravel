import React, { Component } from "react";
//import ReactDatalist from "react-datalist";
//var ReactDatalist = require('react-datalist');
//import DataListInput from "react-datalist-input";
//import DataListInput from "react-datalist-input";
//import DataListInput from "react-datalist-input";
import {
  uploadpquestionValue,
  uploadPquestion,
  onFileChange,
  getjsonPlaceholder
} from "../../actions/UploadPquestionActions";
import { connect } from "react-redux";
import debounce from "lodash/debounce";
import Spinner from "../reusables/Spinner";

class UploadQuestion extends Component {
  changethename = debounce(e => {
    this.props.getjsonPlaceholder();
  }, 1000);

  submit = e => {
    e.preventDefault();
    let {
      course_name,
      school,
      year,
      course_code,
      department,
      semester,
      images,
      docs
    } = this.props;

    let form_data = new FormData();
    const imagess = [...images];
    const docss = [...docs];

    imagess.forEach((image, i) => {
      form_data.append("photos[]", image, image.name);
    });
    docss.forEach((doc, i) => {
      form_data.append("docs[]", doc, doc.name);
    });
    form_data.append("course_code", course_code);
    form_data.append("course_name", course_name);
    form_data.append("school", school);
    form_data.append("year", year);
    form_data.append("department", department);
    form_data.append("semester", semester);
    this.props.uploadPquestion(form_data);
    console.log(school);
  };

  render() {
    const {
      course_name,
      year,
      course_code,
      school,
      department,
      semester,
      schools,
      uploadquestionloading
    } = this.props;
    console.log(schools);
    return (
      <div className="landing-page sidebar-collapse">
        <div
          className="page-header header-filter"
          data-parallax="true"
          style={{
            backgroundImage: 'url("../images/city_light.jpg")',
            height: 450
          }}
        ></div>
        <div className="main main-raised">
          <div className="container">
            <div className="section section-contacts">
              <div className="row">
                <div className="col-md-8 ml-auto mr-auto">
                  <h2 className="text-center title">Upload Question Papers</h2>

                  <form className="contact-form">
                    <div className="row">
                      <div className="col-md-6">
                        <div className="form-group">
                          <label className="bmd-label-floating">
                            Course Name
                          </label>
                          <input
                            required
                            type="text"
                            name="course_name"
                            className="form-control"
                            value={course_name}
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
                            Course Code
                          </label>
                          <input
                            required
                            type="text"
                            name="course_code"
                            className="form-control"
                            value={course_code}
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
                    <div className="row">
                      <div className="col-md-6">
                        <div className="form-group">
                          <label className="bmd-label-floating">
                            Department
                          </label>
                          <input
                            required
                            type="text"
                            name="department"
                            className="form-control"
                            value={department}
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
                          <label className="bmd-label-floating">Semester</label>
                          <input
                            required
                            type="text"
                            name="semester"
                            className="form-control"
                            value={semester}
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
                    <div className="row">
                      <div className="col-md-6 mb-3">
                        <div className="form-group">
                          <label for="school" className="bmd-label-floating">
                            School
                          </label>
                          <input
                            required
                            type="text"
                            id="school"
                            name="school"
                            className="form-control"
                            value={school}
                            list="schools"
                            onChange={e => {
                              this.props.uploadpquestionValue({
                                props: e.target.name,
                                value: e.target.value
                              });
                              this.changethename();
                            }}
                          />
                          <datalist id="schools">
                            {schools.map(school => (
                              <option key={school.id} value={school.username} />
                            ))}
                          </datalist>
                        </div>
                      </div>

                      <div className="col-md-6">
                        <div className="form-group">
                          <label className="bmd-label-floating">Year</label>
                          <input
                            required
                            type="text"
                            name="year"
                            className="form-control"
                            value={year}
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
                    <div className="row">
                      <div className="col-md-6 mb-3">
                        <div className="input-group">
                          <div className="input-group-prepend">
                            <span
                              className="input-group-text"
                              style={{
                                backgroundColor: "#b256c1",
                                borderRadius: 8,
                                color: "white"
                              }}
                            >
                              Images
                            </span>
                          </div>
                          <div className="custom-file">
                            <input
                              required
                              accept="image/*"
                              type="file"
                              name="images"
                              className="file-input"
                              id="inputGroupFile01"
                              onChange={e =>
                                this.props.onFileChange({
                                  props: e.target.name,
                                  value: e.target.files
                                })
                              }
                              multiple
                            />
                          </div>
                        </div>
                      </div>
                      <div className="col-md-6">
                        <div className="input-group">
                          <div className="input-group-prepend">
                            <span
                              className="input-group-text"
                              style={{
                                backgroundColor: "#b256c1",
                                borderRadius: 8,
                                color: "white"
                              }}
                            >
                              Docs
                            </span>
                          </div>
                          <div className="custom-file">
                            <input
                              required
                              accept=".doc, .docx, .pdf, .txt, .rtf"
                              type="file"
                              name="docs"
                              className="file-input"
                              id="inputGroupFile02"
                              onChange={e =>
                                this.props.onFileChange({
                                  props: e.target.name,
                                  value: e.target.files
                                })
                              }
                              multiple
                            />
                          </div>
                        </div>
                      </div>
                    </div>

                    <div className="row">
                      <div className="col-md-4 ml-auto mr-auto text-center">
                        {uploadquestionloading ? (
                          <Spinner />
                        ) : (
                          <button
                            className="btn btn-primary btn-raised"
                            style={{ marginTop: 25 }}
                            type="button"
                            onClick={this.submit.bind(this)}
                          >
                            Upload
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
  course_name: state.uploadpquestion.course_name,
  year: state.uploadpquestion.year,
  school: state.uploadpquestion.school,
  course_code: state.uploadpquestion.course_code,
  department: state.uploadpquestion.department,
  semester: state.uploadpquestion.semester,
  images: state.uploadpquestion.images,
  docs: state.uploadpquestion.docs,
  uploadquestionloading: state.uploadpquestion.uploadquestionloading,
  schools: state.uploadpquestion.schools
});

export default connect(mapStateToProps, {
  uploadpquestionValue,
  uploadPquestion,
  onFileChange,
  getjsonPlaceholder
})(UploadQuestion);
