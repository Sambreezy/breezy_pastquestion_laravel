import { LOGIN_USER } from "./types";
import { LOGIN_USER_TRUE } from "./types";
import { LOGIN_USER_FALSE } from "./types";
import {
  LOGOUT_USER,
  FORGOTLOGIN_USER,
  FORGOT_USER_TRUE,
  FORGOT_USER_FALSE
} from "./types";
import { LOGIN_VALUE } from "./types";
import {
  INITIALIZE_USER,
  UPDATE_PASSWORD,
  UPDATE_PASSWORD_TRUE,
  UPDATE_PASSWORD_FALSE
} from "./types";
import axios from "axios";
import Swal from "sweetalert2";

export const loginUser = (email, password) => {
  let data = {
    email,
    password
  };
  return dispatch => {
    dispatch({
      type: LOGIN_USER_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/auth/login", data)
      .then(res => {
        dispatch({
          type: LOGIN_USER,
          payload: res.data.data
        });
      })
      .catch(err => {
        dispatch({
          type: LOGIN_USER_FALSE,
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

export const loginValue = payload => {
  return dispatch => {
    dispatch({
      type: LOGIN_VALUE,
      payload: payload
    });
  };
};

export const logoutUser = () => {
  return dispatch => {
    window.location.replace("/");
    localStorage.clear();
    dispatch({
      type: LOGOUT_USER,
      payload: true
    });
  };
};

export const initializeUser = () => {
  return dispatch => {
    let token = localStorage.getItem("token");
    let user = localStorage.getItem("user");
    if (token && user) {
      dispatch({
        type: INITIALIZE_USER,
        payload: JSON.parse(token),
        payload1: JSON.parse(user)
      });
    }
  };
};

export const forgotloginUser = email => {
  let data = {
    email
  };
  return dispatch => {
    dispatch({
      type: FORGOT_USER_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/auth/forgot", data)
      .then(res => {
        dispatch({
          type: FORGOTLOGIN_USER,
          payload: res.data.message
        });
        Swal.fire({
          type: "success",
          text: res.data ? res.data.message : "Link Sent To Email"
        });
      })
      .catch(err => {
        dispatch({
          type: FORGOT_USER_FALSE,
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

export const updatePassword = (
  old_password,
  new_password,
  new_password_confirmation,
  id
) => {
  let data = {
    old_password,
    new_password,
    new_password_confirmation,
    id
  };

  return dispatch => {
    dispatch({
      type: UPDATE_PASSWORD_TRUE,
      payload: true
    });
    axios
      .post("https://pastquestions.xyz/api/v1/auth/change", data)
      .then(res => {
        dispatch({
          type: UPDATE_PASSWORD,
          payload: res.data
        });
        Swal.fire({
          type: "success",
          text: res.data ? res.data.message : "Password Updated Successfully"
        });
      })
      .catch(err => {
        dispatch({
          type: UPDATE_PASSWORD_FALSE,
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
