import{ lazy} from 'react';
import { createBrowserRouter, RouteObject} from 'react-router-dom';
// context
import LayoutContext from './contextapi/LayoutContext';
import ReportContext from './contextapi/ReportContext';
import ServiceContext from './contextapi/ServiceContext';

import Login from './pages/login/Login';
import Layout from './pages/layout/Layout';

const NotFound = lazy(()=> import('./pages/NotFound'));
const Company = lazy(()=> import('./pages/company/Company'));
const DailyCallReport = lazy(()=> import('./pages/dailyCallReport/DailyCallReport'));
const Dashboard = lazy(()=> import('./pages/dashboard/Dashboard'));
const DataUploaderDashboard = lazy(()=> import('./pages/dataUploaderDashboard/DataUploaderDashboard'));
const DataUploaderReport = lazy(()=> import('./pages/dataUploaderReport/DataUploaderReport'));
const Department = lazy(()=> import('./pages/department/Department'));
const DeptWiseReformNo = lazy(()=> import('./pages/deptWiseReformNo/DeptWiseReformNo'));
const InterviewerDashboard = lazy(()=> import('./pages/interviewerDashboard/InterviewerDashboard'));

const MisStatus = lazy(()=> import('./pages/misStatus/MisStatus'));
const ReformNumber = lazy(()=> import('./pages/reformNumber/ReformNumber'));
const Report = lazy(()=> import('./pages/report/Report'));
const Service = lazy(()=> import('./pages/service/Service'));
const Survey = lazy(()=> import('./pages/survey/Survey'));
const SurveyYear = lazy(()=> import('./pages/surveyYear/SurveyYear'));
const UserRemarks = lazy(()=> import('./pages/userRemarks/UserRemarks'));
const DashboardView = lazy(()=> import('./pages/dashboard/DashboardView'));
const UserManagement = lazy(()=> import('./pages/userManagement/UserManagement'));


type navType = RouteObject & { m_user_type_id: Number[] }

const navs: navType[] = [
    {
        path: '/dashboard',
        element: <Dashboard />,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/dashboardView',
        element: <DashboardView/>,
        m_user_type_id: [10000, 1000, 100]
    },
    {
        path: '/userManagement',
        element: <UserManagement/>,
        m_user_type_id: [10000, 1000]
    },
    {
        path: '/interviewerdashboard',
        element: <InterviewerDashboard />,
        m_user_type_id: [1]
    },
    {
        path: '/datauploaderdashboard',
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
        element:<ServiceContext><Service/></ServiceContext>,
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
        element:<ReportContext><Report/></ReportContext>,
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
        path: '/',
        element: <Login />
    },
    {
        element:<LayoutContext><Layout/></LayoutContext>,
        children: [...navlinks]
    }
]);