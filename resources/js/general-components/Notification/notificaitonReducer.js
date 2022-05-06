import {
    ACTION_ON_NEW_NOTIFICATION, 
    ACTION_RESET_REDUX_NOTIFICATION_STATE,
    ACTION_SET_NOTIFICATION_COUNT
} from "./NotificationConstants";

const initialState ={
    notifications: {
        blacklist:{
            data:[]
        },
        buybox:{
            data:[]
        },
        settings:{
            data:[]
        },
        key: null,
        index: null,
    },
    totalNewNotification:0,
}
export const HandleNewNotificaitonReducer = (state = initialState, action)=> {
    switch (action.type) {
        case ACTION_ON_NEW_NOTIFICATION: {
            let data = state.notifications[action.payload.key].data;
            data.push(action.payload.data); 
            return { 
                ...state, 
                notifications: {
                    ...state.notifications,
                    [action.payload.key]:{
                        data 
                    },
                    key: action.payload.key,
                    index:(action.payload.data.type - 1)
                },
                totalNewNotification: (state.totalNewNotification + 1)
            };
        }
        case ACTION_SET_NOTIFICATION_COUNT: {
            return { 
                ...state, 
                totalNewNotification : (action.payload.notiCount)
            };
        }
        case ACTION_RESET_REDUX_NOTIFICATION_STATE: {
            return { 
                notifications: {
                    blacklist:{
                        data:[]
                    },
                    buybox:{
                        data:[]
                    },
                    settings:{
                        data:[]
                    },
                    key:null,
                    index: null,
                },
                totalNewNotification: 0
            };
        }
        default: {
            return {
                ...state
            };
        }
    }
}