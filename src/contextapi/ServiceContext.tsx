import { createContext, useContext, useReducer, PropsWithChildren, useRef } from "react";
import type { initialValueType, contextType } from "../types/Service";
import ServiceAction from "../reducers/ServiceAction";


const initialValue:initialValueType = {
    show:"20",
    entries:'',
    search:'',
    pageTotal:1,
    pageValue:1,
    data:null,
    searchData:null,
    renderData:null
}

const Context = createContext({} as contextType);

export default function ServiceContext({children}:PropsWithChildren){

    const pageTitleBarRef = useRef<HTMLDivElement | null>(null);
    const tableFooterRef = useRef<HTMLTableElement | null>(null);
    const tableHeaderRef = useRef<HTMLTableElement | null>(null);

    const [state, dispatch] = useReducer(ServiceAction , initialValue);

   return (
      <Context.Provider value={{state, dispatch, pageTitleBarRef,  tableHeaderRef, tableFooterRef}}>{children}</Context.Provider>
   )
}

export const useService = () => useContext(Context);