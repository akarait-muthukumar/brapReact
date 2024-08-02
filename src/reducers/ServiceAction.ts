import type { initialValueType, actionType } from "../types/Service";

export default function ServiceAction(state:initialValueType, action:actionType){
    switch(action.type){
        case 'show':
            return {...state, show:action.payload};
        case 'currentPage':
            return {...state, currentPage:action.payload};
        case 'data':
            return {...state, data:action.payload};
        case "renderData":
            return {...state, data:action.payload};
        default :
            return state;
    }
}