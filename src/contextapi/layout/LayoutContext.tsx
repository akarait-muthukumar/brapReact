
import {createContext, useContext, PropsWithChildren, useReducer} from 'react'
import type { stateType, ContextType} from '../../types/layout/Layout';
import LayoutReducer from '../../reducers/layout/LayoutReducer';

const initialState:stateType= {
   panelActive : false
}

const Context  = createContext({} as ContextType);

export default function LayoutContext({children}:PropsWithChildren){

   const [state, dispatch] = useReducer(LayoutReducer, initialState);

   return <Context.Provider value={{state,dispatch}}></Context.Provider>

}

export const useLayout = () =>useContext(Context);