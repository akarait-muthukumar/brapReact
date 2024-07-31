import { createContext, useContext, useReducer, PropsWithChildren, useEffect} from "react"
import { initialType, contextType} from "../types/Report";
import ReportAction from "../reducers/ReportAction";
import { api } from "../utils/ApiService";

const initialValue:initialType = {
    filter:{
        year: null,
        survey_month:null,
        department_id:null,
        reform: null,
        status:null
    },
    tableData:null,
    getReport:true
}

const Context = createContext({} as contextType);

export default function ReportContext({children}:PropsWithChildren) {
   const [state, dispatch] = useReducer(ReportAction, initialValue);

  return (
    <>
        <Context.Provider value={{state, dispatch}}>{children}</Context.Provider>
    </>
  )
}

export const useReport = ()=> useContext(Context);

