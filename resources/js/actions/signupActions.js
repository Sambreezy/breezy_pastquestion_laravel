import { SIGNUP_USER, SIGNUP_USER_TRUE } from './types';
import { SIGNUP_VALUE } from './types';
import Swal from 'sweetalert2';
import axios from 'axios';

export const signupUser = (
  name,
  email,
  phone,
  password,
  password_confirmation
) => {
  let data = {
    name,
    email,
    phone,
    password,
    password_confirmation
  };
  return dispatch => {
    dispatch({
      type: SIGNUP_USER_TRUE,
      payload: true
    });
    axios
      .post('https://pastquestions.xyz/api/v1/auth/register', data)
      .then(res => {
        dispatch({
          type: SIGNUP_USER,
          payload: res.data.message
        });
        //Swal.fire({
        //type: 'success',
        //text: res.data ? res.data.message : 'Signed Up Successfully'
        //});
        window.location.replace('/login');
      })
      .catch(err => {
        Swal.fire({
          type: 'error',
          title: 'Oops...',
          text: err.response
            ? err.response.data.message
            : 'Something went wrong',
          confirmButtonText: 'Ok'
        });
      });
  };
};

export const signupValue = payload => {
  return dispatch => {
    dispatch({
      type: SIGNUP_VALUE,
      payload: payload
    });
  };
};
