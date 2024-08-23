import type { initialValueType, actionType } from "../types/Service";

export default function ServiceAction(state:initialValueType, action:actionType){
   
    // Show Entries 
    const _fnEntries = (_start:number = 0, _end:number = 0, _total:number = 0) => {
        return `Showing ${_start} to ${_end} of ${_total} entries`;
    }

    // show 
    const _fnShow = (payload:number) =>{
        let renderData = null, pageTotal = state.pageTotal, entries = state.entries;
            if(state.searchData != null){
                renderData = (payload === -1) ? state.searchData : state.searchData.slice(0, payload);
                pageTotal =  (payload === -1) ? 1 : Math.ceil(state.searchData.length / payload);
                entries = _fnEntries(1,(payload === -1) ? state.searchData.length : payload, state.searchData.length);
            }
        return {...state, show:String(payload), renderData:renderData, pageValue:1, pageTotal:pageTotal, entries:entries};
    }

    // pagination value oncahnge
    const _fnPageValue = (payload:number) =>{
        let renderData = null, entries = state.entries;
        if(state.searchData != null){
            let start = (payload - 1) * parseInt(state.show);
            let _end = payload * parseInt(state.show);
            let end = _end > state.searchData.length ? state.searchData.length : _end;

            renderData =  state.searchData.slice(start, end);

            entries = _fnEntries(start + 1 ,end, state.searchData.length);
        }
        return {...state, renderData:renderData, pageValue:payload, entries:entries};
    }

   // search 
   const _fnSearch = (payload:string) => {

    let x = payload.trim(), searchData = state.data, entries = _fnEntries(), renderData = null;
    
    if(x.length > 0 && state.data !== null){
        searchData = state.data.filter((item)=>{
            return item.service_name.toLowerCase().includes(payload.toLowerCase()) || 
             item.reform_number.toLowerCase().includes(payload.toLowerCase())
        });
    }

    if(searchData !== null){
        renderData = searchData.slice(0, parseInt(state.show));
        entries = _fnEntries(1 , parseInt(state.show) > searchData.length ? searchData.length : parseInt(state.show), searchData.length)
    }

    return {...state, search:payload, searchData:searchData,
         pageTotal:searchData == null ? 1 : Math.ceil(searchData.length / parseInt(state.show)),
         renderData: renderData,
         pageValue:1,
         entries:entries
        };
    }

    switch(action.type){
        case 'show':{
            return _fnShow(parseInt(action.payload));
        }
        case 'entries':
            return {...state, entries:action.payload};
        case 'pageTotal':
            return {...state, pageTotal:action.payload};
        case 'pageValue':{
            return _fnPageValue(action.payload);
        }
        case 'data':
            return {...state, data:action.payload, searchData:action.payload};
        case 'search':{
           return _fnSearch(action.payload);
        }
        case "renderData":
            return {...state, renderData:action.payload};
        default :
            return state;
    }
}