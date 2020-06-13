import {
  GET_USER,
  USEREDIT_VALUE,
  USER_EDITPIX,
  UPDATE_PIX,
  UPDATE_USER_TRUE,
  UPDATE_USER_FALSE,
  UPDATE_USER
} from "../actions/types";

const initialState = {
  singleusername: "",
  singleuserphone: "",
  singleuserdesc: "",
  singleuserid: "",
  updateuserloading: false,
  singleuser: {},
  singleuserdocs: [],
  userpix: null,
  singleuserpicturename: ""
};

export default function(state = initialState, action) {
  switch (action.type) {
    case GET_USER:
      return {
        ...state,
        singleuser: action.payload,
        singleuserpicturename: action.payload.picture,
        singleuserid: action.payload.id,
        singleusername: action.payload.name,
        singleuserphone: action.payload.phone,
        singleuserdesc: action.payload.description,
        singleuserdocs: action.payload.past_question
      };

    case USEREDIT_VALUE:
      return {
        ...state,
        [action.payload.props]: action.payload.value
      };

    case USER_EDITPIX:
      return {
        ...state,
        userpix: action.payload
      };
    case UPDATE_USER_TRUE:
      return {
        ...state,
        updateuserloading: action.payload
      };
    case UPDATE_USER_FALSE:
      return {
        ...state,
        updateuserloading: action.payload
      };
    case UPDATE_USER:
      return {
        ...state,
        updateuserloading: false
      };

    case UPDATE_PIX:
      return {
        ...state,
        userpix: action.payload,
        singleuserpicturename: action.payload
      };
    default:
      return state;
  }
}
