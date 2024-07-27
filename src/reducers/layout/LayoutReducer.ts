import {stateType , actionType} from "../../types/layout/Layout";

export default function LayoutReducer(state:stateType, action:actionType):stateType{
    switch(action.type){
        case 'panelActive':
            return {...state, panelActive:action.payload}
        default:
            return state
    }
}