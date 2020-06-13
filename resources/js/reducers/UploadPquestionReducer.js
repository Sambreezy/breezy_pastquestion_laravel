import {
  UPLOADPQUESTION_VALUE,
  DELETE_QUESTION_TRUE,
  DELETE_QUESTION_FALSE,
  UPLOAD_PQUESTION,
  UPLOAD_QUESTION_TRUE,
  UPLOAD_QUESTION_FALSE,
  IMGDOC_VALUE,
  GET_PASTQUESTION,
  GET_PREVNEXT,
  SEARCH_PASTQUESTION,
  SEARCH_QUESTION_TRUE,
  GET_SINGLEITEM,
  GET_FIRSTPASTQUESTION,
  PQS_ARRAY,
  DELETE_PQUESTION,
  UPLOADPARAM_VALUE,
  JS_VOTEUP,
  JS_VOTEDOWN,
  COMMENT_QUESTION,
  COMMENT_QUESTION_TRUE,
  COMMENT_QUESTION_FALSE,
  CONTACT_US,
  CONTACT_US_TRUE,
  CONTACT_US_FALSE,
  ONFILE_CHANGE,
  GET_SCHOOLS
} from "../actions/types";

const initialState = {
  course_name: "",
  year: "",
  course_code: "",
  school: "",
  department: "",
  semester: "",
  pastquestion: "",
  deleteloadng: false,
  uploading: false,
  images: [],
  docs: [],
  questions: [],
  prev: "",
  next: "",
  search: "",
  contactusloading: false,
  searchquestionloading: false,
  commentquestionloading: false,
  uploadquestionloading: false,
  results: [],
  schools: [],
  singleitem: {},
  singleimages: [],
  singledocs: [],
  singlecomments: [],
  singlecommentuser: [],
  firstquestions: [],
  deletedPqs: [],
  successdelete: "",
  comment: "",
  singleitemid: "",
  all_name: "",
  all_email: "",
  all_message: "",
  jsvoteupshow: "",
  jsvotedownshow: "",
  uploaded_by: "",
  results_state: false
};

export default function(state = initialState, action) {
  switch (action.type) {
    case UPLOADPQUESTION_VALUE:
      return {
        ...state,
        [action.payload.props]: action.payload.value
      };

    case ONFILE_CHANGE:
      return {
        ...state,
        [action.payload.props]: action.payload.value
      };

    case GET_SCHOOLS:
      return {
        ...state,
        schools: action.payload
      };

    case IMGDOC_VALUE:
      return {
        ...state,
        uploading: action.payload
      };
    case UPLOAD_PQUESTION:
      return {
        ...state,
        pastquestion: action.payload.message,
        course_name: "",
        year: "",
        course_code: "",
        school: "",
        department: "",
        semester: "",
        images: [],
        docs: [],
        uploadquestionloading: false
      };

    case GET_PASTQUESTION:
      return {
        ...state,
        questions: action.payload,
        results_state: false
      };
    case UPLOAD_QUESTION_TRUE:
      return {
        ...state,
        uploadquestionloading: true
      };
    case UPLOAD_QUESTION_FALSE:
      return {
        ...state,
        uploadquestionloading: false
      };

    case GET_FIRSTPASTQUESTION:
      return {
        ...state,
        firstquestions: action.payload
      };

    case COMMENT_QUESTION:
      return {
        ...state,
        comment: "",
        commentquestionloading: false
      };

    case COMMENT_QUESTION_TRUE:
      return {
        ...state,
        commentquestionloading: action.payload
      };

    case COMMENT_QUESTION_FALSE:
      return {
        ...state,
        commentquestionloading: action.payload
      };

    case GET_PREVNEXT:
      return {
        ...state,
        prev: action.payload.prev_page_url,
        next: action.payload.next_page_url
      };

    case SEARCH_QUESTION_TRUE:
      return {
        ...state,
        searchquestionloading: action.payload
      };

    case SEARCH_PASTQUESTION:
      return {
        ...state,
        results: action.payload,
        search: "",
        results_state: action.payload1,
        questions: [],
        searchquestionloading: false
      };

    case GET_SINGLEITEM:
      console.log(action.payload.comment, "i am pay");
      return {
        ...state,
        singleitem: action.payload,
        singleimages: action.payload.image,
        singledocs: action.payload.document,
        singlecomments: action.payload.comment,
        singlecommentuser: action.payload.comment.user_picture,
        uploaded_by: action.payload.uploaded_by
      };

    case PQS_ARRAY:
      let newArray = [...state.deletedPqs];

      if (newArray.includes(action.payload)) {
        newArray.pop(action.payload);
      } else {
        newArray.push(action.payload);
      }
      return {
        ...state,
        deletedPqs: newArray
      };
    case DELETE_QUESTION_TRUE:
      return {
        ...state,
        deleteloading: action.payload
      };
    case DELETE_QUESTION_FALSE:
      return {
        ...state,
        deleteloading: action.payload
      };

    case DELETE_PQUESTION:
      return {
        ...state,
        successdelete: action.payload,
        deleteloading: false
      };
    case JS_VOTEUP:
      return {
        ...state,
        jsvoteupshow: action.payload
      };
    case JS_VOTEDOWN:
      return {
        ...state,
        jsvotedownshow: action.payload
      };
    case UPLOADPARAM_VALUE:
      return {
        ...state,
        singleitemid: action.payload
      };
    case CONTACT_US:
      return {
        ...state,
        all_name: "",
        all_email: "",
        all_message: "",
        contactusloading: false
      };

    case CONTACT_US_TRUE:
      return {
        ...state,
        contactusloading: action.payload
      };

    case CONTACT_US_FALSE:
      return {
        ...state,
        contactusloading: action.payload
      };

    default:
      return state;
  }
}
