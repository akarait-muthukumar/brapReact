import { initialType, actionType} from "../types/Report";

export default function ReportAction(state:initialType, action:actionType) {
    switch(action.type){
        case 'year':
            return {...state, filter:{...state.filter, year:action.payload}};
        case 'survey_month':
            return {...state, filter:{...state.filter, survey_month:action.payload}};
        case 'department_id':
            return {...state, filter:{...state.filter, department_id:action.payload}};
        case 'reform':
            return {...state, filter:{...state.filter, reform:action.payload}};
        case 'status':
            return {...state, filter:{...state.filter, status:action.payload}};
        case 'tableData':
            return {...state, tableData:action.payload};
        case 'getReport':
            return {...state, getReport:!state.getReport};
        default:
            return state;
    }
}