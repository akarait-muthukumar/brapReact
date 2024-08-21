import { Dispatch } from "react";

export type initialValueType = {
    show: string;
    pageTotal: number;
    pageValue: number;
    data:tableDataType[] | null;
    renderData:tableDataType[] | null;
}

export type actionType = {type:'show', payload:string} | {type:'pageTotal', payload:number} | {type:'pageValue', payload:number}
 | {type:'data', payload:tableDataType[]} | {type:'renderData', payload:tableDataType[]}

export type contextType = {
    state:initialValueType;
    dispatch:Dispatch<actionType>;
}

export type tableDataType = {
    m_service_id: string,
    service_name: string,
    reform_number: string
};