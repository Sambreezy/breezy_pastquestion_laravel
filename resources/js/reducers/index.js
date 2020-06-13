import { combineReducers } from "redux";
import contactReducer from "./contactReducer";
import signupReducer from "./signupReducer";
import loginReducer from "./loginReducer";
import UploadPquestionReducer from "./UploadPquestionReducer";
import UserReducer from "./UserReducer";

export default combineReducers({
  contact: contactReducer,
  signup: signupReducer,
  login: loginReducer,
  uploadpquestion: UploadPquestionReducer,
  user: UserReducer
});
