import { createStore, applyMiddleware } from "redux";
import rootReducer from "./../rootReducer";
import thunk from "redux-thunk";

const initialState = {};
const store = createStore(rootReducer, initialState, applyMiddleware(thunk));

export default store;
