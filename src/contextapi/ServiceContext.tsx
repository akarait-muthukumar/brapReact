import { createContext, useContext, useReducer, PropsWithChildren } from "react";
import type { initialValueType, contextType } from "../types/Service";
import ServiceAction from "../reducers/ServiceAction";

const initialValue:initialValueType = {
    show:"20",
    pageTotal:1,
    pageValue:1,
    data:null,
    renderData:null
}

const Context = createContext({} as contextType);

export default function ServiceContext({children}:PropsWithChildren){
    const [state, dispatch] = useReducer(ServiceAction , initialValue);
   return (
      <Context.Provider value={{state, dispatch}}>{children}</Context.Provider>
   )
}

export const useService = () => useContext(Context);