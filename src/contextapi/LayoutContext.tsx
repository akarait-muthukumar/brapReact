
import {createContext, useContext, PropsWithChildren, useReducer, useRef} from 'react'
import type { stateType, ContextType} from '../types/Layout';
import LayoutReducer from '../reducers/LayoutReducer';

const initialState:stateType= {
   panelActive : false
}

const Context  = createContext({} as ContextType);

export default function LayoutContext({children}:PropsWithChildren){

   const mainRef = useRef<HTMLElement | null>(null);

   const [state, dispatch] = useReducer(LayoutReducer, initialState);

   return <Context.Provider value={{state,dispatch, mainRef}}>{children}</Context.Provider>

}

export const useLayout = () => useContext(Context);