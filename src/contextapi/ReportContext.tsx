import { createContext, useContext, useReducer, PropsWithChildren} from "react"
import { initialType, contextType} from "../types/Report";
import ReportAction from "../reducers/ReportAction";

const initialValue:initialType = {
    filter:{
        year: '2024',
        survey_month:[null, null],
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

