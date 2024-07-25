import {Box} from "@mantine/core";
import Titlebar from "./Titlebar";
import SideBar from "./SideBar";
import { Outlet } from "react-router-dom";
import '../../assets/css/layout.scss';

function Layout() {
  return (
    <>
        <Box className="panel">
            <SideBar/>
            <Box className="panel-container">
                <Titlebar/>
                <Box component="main">
                    <Outlet/>
                </Box>
            </Box>
        </Box>
    </>
  )
}

export default Layout