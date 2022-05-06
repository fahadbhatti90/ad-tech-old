import { createStore, applyMiddleware, compose } from "redux";
import rootReducer from "./../rootReducer";
import thunk from "redux-thunk";
import { createLogger } from "redux-logger";
import initialState from "./initialState";

const middleware = [thunk];

middleware.push(createLogger({
  duration: true,
  diff: true
}));

let composedMiddleWare = compose(applyMiddleware(...middleware));

if (!!window.__REDUX_DEVTOOLS_EXTENSION__) {
	composedMiddleWare = compose(
		applyMiddleware(...middleware),
		window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
	);
}

const store = createStore(rootReducer, initialState, composedMiddleWare);

export default store;
