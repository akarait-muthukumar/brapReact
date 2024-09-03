import {Box} from "@mantine/core";
import Titlebar from "./Titlebar";
import SideBar from "./SideBar";
import { Outlet, Navigate } from "react-router-dom";
import '../../assets/css/layout.scss';
import Loader from "../../components/Loader";
import {useLayout} from "../../contextapi/LayoutContext";
import { Suspense } from "react";

function Layout() {

  const {state, mainRef} = useLayout();

  let isAuth = sessionStorage.getItem('token');
  return (
    <>
        <Box className={`panel ${state.panelActive ? 'active' : ''}`}>
            <SideBar/>
            <Box className="panel-container" bg='gray.0'>
                <Titlebar/>
                <Box component="main" className="main" p={8} ref={mainRef}>
                   {(isAuth !== null )? <Suspense fallback={<Loader/>}><Outlet/></Suspense> : <Navigate to='/login' replace={true} />}  
                </Box>
            </Box>
        </Box>
    </>
  )
}

export default Layout