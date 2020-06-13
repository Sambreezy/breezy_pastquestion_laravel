import {
  GET_USER,
  USEREDIT_VALUE,
  UPDATE_USER,
  USER_EDITPIX,
  UPDATE_PIX,
  UPDATE_USER_TRUE,
  UPDATE_USER_FALSE
} from "./types";
import axios from "axios";
import Swal from "sweetalert2";

export const getuserInfo = id => {
  console.log(id);
  return dispatch => {
    axios
      .get(`https://pastquestions.xyz/api/v1/user/show?id=${id}`)
      .then(res => {
        dispatch({
          type: GET_USER,
          payload: res.data.data
        });
        console.log(res.data.data);
      })
      .catch(err => console.log(err, "i am err"));
  };
};

export const usereditValue = payload => {
  return dispatch => {
    dispatch({
      type: USEREDIT_VALUE,
      payload: payload
    });
  };
};

export const updateUser = (name, phone, description, id) => {
  let data = {
    name,
    phone,
    description,
    id
  };

  return dispatch => {
    dispatch({
      type: UPDATE_USER_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/user/edit", data)
      .then(res => {
        dispatch({
          type: UPDATE_USER,
          payload: res.data
        });
        Swal.fire({
          type: "success",
          text: res.data ? res.data.message : "Profile Updated Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: UPDATE_USER_FALSE,
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

export const usereditPix = files => {
  return dispatch => {
    dispatch({
      type: USER_EDITPIX,
      payload: files
    });
  };
};

export const updatePix = (form_data, id) => {
  return dispatch => {
    axios
      .post("https://pastquestions.xyz/api/v1/user/edit", form_data, {
        headers: {
          "content-type": "multipart/form-data"
        }
      })
      .then(res => {
        dispatch({
          type: UPDATE_PIX,
          payload: null
        });
        dispatch(getuserInfo(id));
        Swal.fire({
          type: "success",
          text: res.data
            ? res.data.message
            : "Profile Picture updated Successfully"
        });
      })
      .catch(err => {
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
