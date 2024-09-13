import React from "react";
import { useAuth } from "../contextapi/AuthContext"
import { Navigate,  } from "react-router-dom";

type RequiredAuthType = {
  children:React.ReactNode,
  m_user_type_id?:number[]
}

function RequiredAuth({m_user_type_id, children}:RequiredAuthType) {

    const {auth} = useAuth();

    console.log(auth);

    const _token = sessionStorage.getItem('token');

    if(auth != null){
      return (
        (m_user_type_id?.includes(Number(auth?.m_user_type_id))) ? <>{children}</> : <Navigate to='/notfound' />
      )
    }
    else{
      return (
        (_token == null) ? <Navigate to='/' replace/> : <>{children}</>
      )
    }


}

export default RequiredAuth