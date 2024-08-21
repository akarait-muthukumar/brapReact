import type { initialValueType, actionType } from "../types/Service";

export default function ServiceAction(state:initialValueType, action:actionType){
    switch(action.type){
        case 'show':
            return {...state, show:action.payload};
        case 'pageTotal':
            return {...state, pageTotal:action.payload};
        case 'pageValue':
            return {...state, pageValue:action.payload};
        case 'data':
            return {...state, data:action.payload};
        case "renderData":
            return {...state, data:action.payload};
        default :
            return state;
    }
}