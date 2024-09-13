import{ lazy} from 'react';
import { Route, Routes} from 'react-router-dom';
// context
import LayoutContext from './contextapi/LayoutContext';
import ReportContext from './contextapi/ReportContext';
import ServiceContext from './contextapi/ServiceContext';

import Login from './pages/login/Login';
import Layout from './pages/layout/Layout';

import RequiredAuth from './utils/RequiredAuth';

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


export default function Router(){

    return (
        <Routes>
            <Route path='*' element={<NotFound />} />
            <Route path='/' element={<Login />} />

            <Route element={<LayoutContext><Layout/></LayoutContext>} >
                <Route path='/dashboard' element={<RequiredAuth m_user_type_id={[10000,1000]}><Dashboard /></RequiredAuth>} />
                <Route path='/dashboardView' element={<RequiredAuth m_user_type_id={[10000,1000]}><DashboardView /></RequiredAuth>} />
                <Route path='/userManagement' element={<RequiredAuth m_user_type_id={[10000,1000]}><UserManagement /></RequiredAuth>} />
                <Route path='/interviewerdashboard' element={<RequiredAuth m_user_type_id={[1]}><InterviewerDashboard /></RequiredAuth>} />
                <Route path='/datauploaderdashboard' element={<RequiredAuth m_user_type_id={[10]}><DataUploaderDashboard /></RequiredAuth>} />
                <Route path='/department' element={<RequiredAuth m_user_type_id={[10000,1000]}><Department /></RequiredAuth>} />
                <Route path='/reformnumber' element={<RequiredAuth m_user_type_id={[10000,1000]}><ReformNumber /></RequiredAuth>} />
                <Route path='/service' element={<RequiredAuth m_user_type_id={[10000,1000]}><ServiceContext><Service/></ServiceContext></RequiredAuth>} />
                <Route path='/surveyyear' element={<RequiredAuth m_user_type_id={[10000,1000]}><SurveyYear /></RequiredAuth>} />
                <Route path='/deptwisereformno' element={<RequiredAuth m_user_type_id={[10000,1000]}><DeptWiseReformNo/></RequiredAuth>} />
                <Route path='/survey' element={<RequiredAuth m_user_type_id={[10000,1000,1]}><Survey/></RequiredAuth>} />
                <Route path='/misstatus' element={<RequiredAuth m_user_type_id={[10000,1000,100]}><MisStatus/></RequiredAuth>} />
                <Route path='/report' element={<RequiredAuth m_user_type_id={[10000,1000,100]}><ReportContext><Report/></ReportContext></RequiredAuth>} />
                <Route path='/dailycallreport' element={<RequiredAuth m_user_type_id={[10000,1000,100]}><DailyCallReport/></RequiredAuth>} />
                <Route path='/userremarks' element={<RequiredAuth m_user_type_id={[100,10,1]}><UserRemarks/></RequiredAuth>} />
                <Route path='/company' element={<RequiredAuth m_user_type_id={[10]}><Company/></RequiredAuth>} />
                <Route path='/datauploaderreport' element={<RequiredAuth m_user_type_id={[10]}><DataUploaderReport/></RequiredAuth>} />
            </Route>

        </Routes>
    );
}





