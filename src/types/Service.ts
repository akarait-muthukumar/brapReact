import { Dispatch } from "react";

export type initialValueType = {
    show: string;
    entries:string;
    search:string;
    pageTotal: number;
    pageValue: number;
    data:tableDataType[] | null;
    searchData:tableDataType[] | null;
    renderData:tableDataType[] | null;
}

export type actionType = {type:'show', payload:string} | {type:'entries', payload:string} | {type:'search', payload:string} | 
{type:'pageTotal', payload:number} | {type:'pageValue', payload:number} | {type:'data', payload:tableDataType[]} 
| {type:'searchData', payload:tableDataType[]} | {type:'renderData', payload:tableDataType[]}

export type contextType = {
    state:initialValueType;
    dispatch:Dispatch<actionType>;
    pageTitleBarRef: React.MutableRefObject<HTMLDivElement | null>
    tableHeaderRef: React.MutableRefObject<HTMLTableElement | null>
    tableFooterRef: React.MutableRefObject<HTMLTableElement | null>
}

export type tableDataType = {
    m_service_id: string,
    service_name: string,
    reform_number: string
};