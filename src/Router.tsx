import { createBrowserRouter, RouteObject } from 'react-router-dom';
import NotFound from './pages/NotFound';
import Company from './pages/company/Company';
import DailyCallReport from './pages/dailyCallReport/DailyCallReport';
import Dashboard from './pages/dashboard/Dashboard';
import DataUploaderDashboard from './pages/dataUploaderDashboard/DataUploaderDashboard';
import DataUploaderReport from './pages/dataUploaderReport/DataUploaderReport';
import Department from './pages/department/Department';
import DeptWiseReformNo from './pages/deptWiseReformNo/DeptWiseReformNo';
import InterviewerDashboard from './pages/interviewerDashboard/InterviewerDashboard';
import Layout from './pages/layout/Layout';
import Login from './pages/login/Login';
import MisStatus from './pages/misStatus/MisStatus';
import ReformNumber from './pages/reformNumber/ReformNumber';
import Report from './pages/report/Report';
import Service from './pages/service/Service';
import Survey from './pages/survey/Survey';
import SurveyYear from './pages/surveyYear/SurveyYear';
import UserRemarks from './pages/userRemarks/UserRemarks';
import DashboardView from './pages/dashboard/DashboardView';

// context
import LayoutContext from './contextapi/LayoutContext';

type navType = RouteObject & { m_user_type_id: Number[] }

const navs: navType[] = [
    {
        path: '/',
        element: <Dashboard />,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/dashboardView',
        element: <DashboardView/>,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/',
        element: <InterviewerDashboard />,
        m_user_type_id: [1]
    },
    {
        path: '/',
        element: <DataUploaderDashboard />,
        m_user_type_id: [10]
    },
    {
        path: '/department',
        element:<Department/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/reformnumber',
        element:<ReformNumber/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/service',
        element:<Service/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/surveyyear',
        element:<SurveyYear/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/deptwisereformno',
        element:<DeptWiseReformNo/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/survey',
        element:<Survey/>,
        m_user_type_id: [10000, 1000, 1]
    },
    {
        path: '/misstatus',
        element:<MisStatus/>,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/report',
        element:<Report/>,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/dailycallreport',
        element:<DailyCallReport/>,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/userremarks',
        element:<UserRemarks/>,
        m_user_type_id: [100, 10, 1]
    },
    {
        path: '/company',
        element:<Company/>,
        m_user_type_id: [10]
    },
    {
        path: '/datauploaderreport',
        element:<DataUploaderReport/>,
        m_user_type_id: [10]
    }
];

const m_user_type_id = 1000;

let navlinks:RouteObject[] = [];

navs.forEach((item)=>{
    if(item.m_user_type_id.includes(m_user_type_id)){
      navlinks.push({path:item.path, element:item.element});
    }
});


export const Router = createBrowserRouter([
    {
        path: '*',
        element: <NotFound />
    },
    {
        path: '/login',
        element: <Login />
    },
    {
        path: '/',
        element:<LayoutContext><Layout/></LayoutContext>,
        children: [...navlinks]
    },
]);