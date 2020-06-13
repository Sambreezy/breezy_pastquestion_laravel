import {
  LOGIN_VALUE,
  LOGIN_USER,
  LOGIN_USER_TRUE,
  LOGIN_USER_FALSE,
  LOGOUT_USER,
  INITIALIZE_USER,
  UPDATE_PASSWORD,
  UPDATE_PASSWORD_TRUE,
  UPDATE_PASSWORD_FALSE,
  FORGOTLOGIN_USER,
  FORGOT_USER_TRUE,
  FORGOT_USER_FALSE
} from "../actions/types";
import axios from "axios";

const initialState = {
  email: "",
  password: "",
  auth: false,
  loggedOut: true,
  loading: false,
  updatepasswordloading: false,
  token: {},
  user: {},
  forgotemail: "",
  current_password: "",
  new_password: "",
  confirm_password: ""
};

export default function(state = initialState, action) {
  switch (action.type) {
    case LOGIN_VALUE:
      return {
        ...state,
        [action.payload.props]: action.payload.value
      };
    case LOGIN_USER_TRUE:
      return {
        loading: action.payload
      };
    case LOGIN_USER_FALSE:
      return {
        loading: action.payload,
        email: "",
        password: ""
      };
    case LOGIN_USER:
      let logtoken = {
        accessToken: action.payload.jwt.original.access_token,
        expires: action.payload.jwt.original.expires_in
      };

      let loguser = {
        id: action.payload.id,
        name: action.payload.name,
        email: action.payload.email,
        phone: action.payload.phone,
        votes: action.payload.votes,
        picture: action.payload.picture,
        description: action.payload.description
      };
      storeToken(logtoken);
      storeUser(loguser);
      return {
        ...state,
        auth: true,
        loading: false,
        loggedOut: false,
        token: logtoken,
        user: loguser
      };
    case INITIALIZE_USER:
      storeToken(action.payload);
      storeUser(action.payload1);
      return {
        ...state,
        token: action.payload,
        user: action.payload1,
        auth: true,
        loggedOut: false
      };
    case LOGOUT_USER:
      return {
        ...state,
        loggedOut: action.payload
      };
    case FORGOTLOGIN_USER:
      return {
        ...state,
        forgotemail: "",
        loading: false
      };
    case FORGOT_USER_TRUE:
      return {
        loading: action.payload
      };
    case FORGOT_USER_FALSE:
      return {
        loading: action.payload,
        forgotemail: ""
      };
    case UPDATE_PASSWORD:
      return {
        ...state,
        current_password: "",
        new_password: "",
        confirm_password: "",
        updatepasswordloading: false
      };
    case UPDATE_PASSWORD_TRUE:
      return {
        ...state,
        updatepasswordloading: action.payload
      };
    case UPDATE_PASSWORD_FALSE:
      return {
        ...state,
        updatepasswordloading: action.payload
      };

    default:
      return state;
  }
}

const storeToken = payload => {
  localStorage.setItem("token", JSON.stringify(payload));

  axios.defaults.headers.common["Authorization"] =
    "Bearer " + payload.accessToken;
};

const storeUser = payload => {
  localStorage.setItem("user", JSON.stringify(payload));
};
