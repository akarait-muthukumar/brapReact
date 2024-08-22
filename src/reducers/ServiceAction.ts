import type { initialValueType, actionType } from "../types/Service";

export default function ServiceAction(state:initialValueType, action:actionType){


    // search 
    function search(payload:string){
        let searchData = null;
        if(state.data !== null){
            let x = state.data.filter((item)=>{
                return item.service_name.toLowerCase().includes(payload.toLowerCase()) ||
                 item.reform_number.toLowerCase().includes(payload.toLowerCase())
            });
            searchData = x;
        }
        return searchData;
    }

    // RenderData
    function rd(payload:string){
        if(state.searchData != null){
            return (payload === '-1') ? state.data : state.searchData.slice(0, parseInt(payload));
        }
        return null;
    }



    switch(action.type){
        case 'show':{
            let renderData = null, pageTotal = state.pageTotal, entries = state.entries;
            if(state.searchData != null){
                renderData = rd(action.payload);
                pageTotal =  (action.payload === '-1') ? 1 : Math.ceil(state.searchData.length / parseInt(action.payload));
                entries = `Showing 1 to ${(action.payload === '-1') ? state.searchData.length : action.payload} of ${state.searchData.length} entries`;
            }
            return {...state, show:action.payload, renderData:renderData, pageValue:1, pageTotal:pageTotal, entries:entries};
        }
        case 'entries':
            return {...state, entries:action.payload};
        case 'pageTotal':
            return {...state, pageTotal:action.payload};
        case 'pageValue':{
            let renderData = null, entries = state.entries;
            if(state.searchData != null){
                let start = (action.payload - 1) * parseInt(state.show);
                let _end = action.payload * parseInt(state.show);
                let end = _end > state.searchData.length ? state.searchData.length : _end;

                renderData =  state.searchData.slice(start, end);

                entries = `Showing ${start + 1} to ${end} of ${state.searchData.length} entries`;
            }
            return {...state, renderData:renderData, pageValue:action.payload, entries:entries};
        }
        case 'data':
            return {...state, data:action.payload, searchData:action.payload};
        case 'search':{
            let x = action.payload.trim(), searchData = state.data;
            if(x.length > 0){
                searchData = search(x);
            }
            return {...state, search:action.payload, searchData:searchData,
                 pageTotal:searchData == null ? 1 : Math.ceil(searchData.length / parseInt(state.show)),
                 renderData: rd(state.show),
                 pageValue:1
                };
        }
        case "renderData":
            return {...state, renderData:action.payload};
        default :
            return state;
    }
}