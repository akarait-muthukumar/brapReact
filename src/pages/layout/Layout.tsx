import {Box} from "@mantine/core";
import Titlebar from "./Titlebar";
import SideBar from "./SideBar";
import { Outlet, Navigate } from "react-router-dom";
import '../../assets/css/layout.scss';

import {useLayout} from "../../contextapi/LayoutContext";

function Layout() {

  const {state} = useLayout();

  let isAuth = true;
  return (
    <>
        <Box className={`panel ${state.panelActive ? 'active' : ''}`}>
            <SideBar/>
            <Box className="panel-container" bg='gray.0'>
                <Titlebar/>
                <Box component="main" p={8}>
                   {isAuth ? <Outlet/> : <Navigate to='/login' replace={true} />}  
                </Box>
            </Box>
        </Box>
    </>
  )
}

export default Layout