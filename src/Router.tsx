import {createBrowserRouter} from 'react-router-dom';
import NotFound from './pages/NotFound';
import Login from './pages/login/Login';
import Dashboard from './pages/home/Dashboard';
import Layout from './pages/layout/Layout';

export const Router = createBrowserRouter([
    {
        path:'*',
        element:<NotFound/>
    },
    {
        path:'/',
        element:<Login/>
    },
    {
        path:'/',
        element:<Layout/>,
        children:[
            {
                path:'/Dashboard',
                element:<Dashboard/>
            }
        ]
    },
]);