import {Box} from "@mantine/core";
import Titlebar from "./Titlebar";
import SideBar from "./SideBar";
import {Outlet } from "react-router-dom";
import '../../assets/css/layout.scss';
import Loader from "../../components/Loader";
import {useLayout} from "../../contextapi/LayoutContext";
import { Suspense } from "react";

function Layout() {

  const {state, mainRef} = useLayout();

  return (

    <>
        <Box className={`panel ${state.panelActive ? 'active' : ''}`}>
            <SideBar/>
            <Box className="panel-container" bg='gray.0'>
                <Titlebar/>
                <Box component="main" className="main" p={8} ref={mainRef}>
                   <Suspense fallback={<Loader/>}><Outlet/></Suspense>
                </Box>
            </Box>
        </Box>
    </>
  )
}

export default Layout