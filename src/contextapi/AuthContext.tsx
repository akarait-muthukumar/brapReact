import {createContext, PropsWithChildren, useContext, useState} from 'react'
import type { AuthUserType, AuthContextType } from '../types/general';

const Context = createContext({} as AuthContextType);

export default  function AuthContext({children}:PropsWithChildren) {

    let _token = sessionStorage.getItem('token');

    let x:AuthUserType = null;

    if(_token !== null){
        x = JSON.parse(atob(_token.split('.')[1]))['data'];
    }

    const [auth, setAuth] = useState(x);

  return (
    <Context.Provider value={{auth, setAuth}}>{children}</Context.Provider>
  )
}

export const useAuth = () => useContext(Context);