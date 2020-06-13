import { UPLOAD_PQUESTION } from "./types";
import { UPLOADPQUESTION_VALUE } from "./types";
import { ONFILE_CHANGE } from "./types";
import { IMGDOC_VALUE } from "./types";
import { GET_PASTQUESTION } from "./types";
import { GET_PREVNEXT } from "./types";
import { SEARCH_PASTQUESTION } from "./types";
import { GET_SCHOOLS } from "./types";
import { GET_SINGLEITEM } from "./types";
import { GET_FIRSTPASTQUESTION } from "./types";
import { VOTELIKE_QUESTION } from "./types";
import { DELETE_QUESTION_TRUE } from "./types";
import { DELETE_QUESTION_FALSE } from "./types";
import { UPLOAD_QUESTION_TRUE } from "./types";
import { UPLOAD_QUESTION_FALSE } from "./types";
import { SEARCH_QUESTION_TRUE } from "./types";
import { COMMENT_QUESTION_TRUE } from "./types";
import { COMMENT_QUESTION_FALSE } from "./types";
import { CONTACT_US_TRUE } from "./types";
import { CONTACT_US_FALSE } from "./types";
import Swal from "sweetalert2";

import {
  VOTEDISLIKE_QUESTION,
  PQS_ARRAY,
  DELETE_PQUESTION,
  UPLOADPARAM_VALUE,
  COMMENT_QUESTION,
  CONTACT_US,
  JS_VOTEUP,
  JS_VOTEDOWN
} from "./types";
import { getuserInfo } from "./UserActions";

import axios from "axios";

export const uploadPquestion = form_data => {
  return dispatch => {
    dispatch({
      type: UPLOAD_QUESTION_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/pastquestion/create", form_data, {
        headers: {
          "content-type": "multipart/form-data"
        }
      })
      .then(res => {
        dispatch({
          type: UPLOAD_PQUESTION,
          payload: res.data.message
        });
        dispatch(getpastQuestion());
        Swal.fire({
          type: "success",
          text: res.data
            ? res.data.message
            : "Past Question uploaded Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: UPLOAD_QUESTION_FALSE,
          payload: false
        });
        Swal.fire({
          type: "error",
          title: "Oops...",
          text: err.response
            ? err.response.data.message
            : "Something went wrong",
          confirmButtonText: "Ok"
        });
      });
  };
};

export const getjsonPlaceholder = () => {
  return dispatch => {
    axios
      .get("https://jsonplaceholder.typicode.com/users")
      .then(res => {
        dispatch({
          type: GET_SCHOOLS,
          payload: res.data
        });
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const uploadpquestionValue = payload => {
  return dispatch => {
    dispatch({
      type: UPLOADPQUESTION_VALUE,
      payload: payload
    });
  };
};

export const onFileChange = payload => {
  return dispatch => {
    dispatch({
      type: ONFILE_CHANGE,
      payload: payload
    });
  };
};

export const getjsvoteup = payload => {
  return dispatch => {
    dispatch({
      type: JS_VOTEUP,
      payload: payload
    });
  };
};

export const getjsvotedown = payload => {
  return dispatch => {
    dispatch({
      type: JS_VOTEDOWN,
      payload: payload
    });
  };
};

export const imgdocValue = payload => {
  return dispatch => {
    dispatch({
      type: IMGDOC_VALUE,
      payload: payload
    });
  };
};

export const getpastQuestion = () => {
  return dispatch => {
    axios
      .get(
        "https://pastquestions.xyz/api/v1/pastquestion/index?properties=true"
      )
      .then(res => {
        dispatch({
          type: GET_PASTQUESTION,
          payload: res.data.data.data
        });
        if (res.data.data.data) {
          dispatch({
            type: GET_PREVNEXT,
            payload: res.data.data
          });
        }
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const getprevpastQuestion = myurl => {
  console.log(myurl);
  return dispatch => {
    axios
      .get(`${myurl}&properties=true`)
      .then(res => {
        dispatch({
          type: GET_PASTQUESTION,
          payload: res.data.data.data
        });
        if (res.data.data.data) {
          dispatch({
            type: GET_PREVNEXT,
            payload: res.data.data
          });
        }
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const searchpastQuestion = data => {
  return dispatch => {
    dispatch({
      type: SEARCH_QUESTION_TRUE,
      payload: true
    });
    axios
      .get(
        `https://pastquestions.xyz/api/v1/pastquestion/singlesearch?search=${data}`
      )
      .then(res => {
        console.log(res.data.status_code, "i am res");
        if (res.data.status_code === 200) {
          dispatch({
            type: SEARCH_PASTQUESTION,
            payload: res.data.data.data,
            payload1: true
          });
        }
      })
      .catch(err => {
        if (err.response.status !== 200) {
          dispatch({
            type: SEARCH_PASTQUESTION,
            payload: [],
            payload1: true
          });
        }
      });
  };
};

export const getsingleItem = id => {
  return dispatch => {
    axios
      .get(`https://pastquestions.xyz/api/v1/pastquestion/show?id=${id}`)
      .then(res => {
        dispatch({
          type: GET_SINGLEITEM,
          payload: res.data.data
        });
        dispatch(getuserInfo(res.data.data.uploaded_by));
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const getfirstpastQuestion = () => {
  return dispatch => {
    axios
      .get("https://pastquestions.xyz/api/v1/general/index")
      .then(res => {
        dispatch({
          type: GET_FIRSTPASTQUESTION,
          payload: res.data.data
        });
        // console.log(res.data.data);
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const votelikeQuestion = past_question_id => {
  let data = {
    past_question_id
  };

  return dispatch => {
    axios
      .post("https://pastquestions.xyz/api/v1/social/upvote", data)
      .then(res => {
        dispatch({
          type: VOTELIKE_QUESTION,
          payload: res.data.message
        });
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const votedislikeQuestion = past_question_id => {
  let data = {
    past_question_id
  };

  return dispatch => {
    axios
      .post("https://pastquestions.xyz/api/v1/social/downvote", data)
      .then(res => {
        dispatch({
          type: VOTEDISLIKE_QUESTION,
          payload: res.data.message
        });
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const deletepqsArray = id => {
  return dispatch => {
    dispatch({
      type: PQS_ARRAY,
      payload: id
    });
  };
};

export const deletePastquestion = (data, userId) => {
  console.log(data, "i am");
  return dispatch => {
    dispatch({
      type: DELETE_QUESTION_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/pastquestion/batchdelete", data)
      .then(res => {
        dispatch({
          type: DELETE_PQUESTION,
          payload: res.data.message
        });
        dispatch(getuserInfo(userId));
        Swal.fire({
          type: "success",
          text: res.data
            ? res.data.message
            : "Past Question(s) deleted Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: DELETE_QUESTION_FALSE,
          payload: false
        });
        Swal.fire({
          type: "error",
          title: "Oops...",
          text: err.response
            ? err.response.data.message
            : "Something went wrong",
          confirmButtonText: "Ok"
        });
      });
  };
};

export const uploadparamValue = payload => {
  return dispatch => {
    dispatch({
      type: UPLOADPARAM_VALUE,
      payload: payload
    });
  };
};

export const commentQuestion = (comment, past_question_id) => {
  let data = {
    comment,
    past_question_id
  };
  return dispatch => {
    dispatch({
      type: COMMENT_QUESTION_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/comment/create", data)
      .then(res => {
        dispatch({
          type: COMMENT_QUESTION,
          payload: res.data.message
        });
        dispatch(getsingleItem(past_question_id));
        Swal.fire({
          type: "success",
          text: res.data ? res.data.message : "Comment Sent Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: COMMENT_QUESTION_FALSE,
          payload: false
        });
        Swal.fire({
          type: "error",
          title: "Oops...",
          text: err.response
            ? err.response.data.message
            : "Something went wrong",
          confirmButtonText: "Ok"
        });
      });
  };
};

export const sendusMessage = (name, email, message) => {
  let data = {
    name,
    email,
    message
  };
  return dispatch => {
    dispatch({
      type: CONTACT_US_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/general/contactus", data)
      .then(res => {
        dispatch({
          type: CONTACT_US,
          payload: res.data.message
        });
        Swal.fire({
          type: "success",
          text: res.data ? res.data.message : "Message Sent Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: CONTACT_US_FALSE,
          payload: false
        });
        Swal.fire({
          type: "error",
          title: "Oops...",
          text: err.response
            ? err.response.data.message
            : "Something went wrong",
          confirmButtonText: "Ok"
        });
      });
  };
};

/** export const getonlyLike = id => {
  console.log(id);
  return dispatch => {
    axios
      .get(`http://qpast.ng/api/v1/pastquestion/show?id=${id}`)
      .then(res => {
        dispatch({
          type: GET_ONLYLIKE,
          payload: res.data.data.vote_up
        });
      })
      .catch(err => console.log(err, "i am err"));
  };
};*/
