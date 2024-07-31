import { useEffect, useState, useRef, useLayoutEffect } from "react";
import { useLocation } from "react-router-dom"
import { api } from "../../utils/ApiService";
import { Paper, Text, Grid, ScrollArea , Flex, Button} from "@mantine/core";
import DashViewCard from "./DashViewCard";
import TitleWithProgressBar from "./TitleWithProgressBar";
import GaugeChart from "./GaugeChart";
import type { dashboardViewDataType } from "../../types/Dashboard";


function DashboardView() {
   const location = useLocation();

   const topref = useRef<HTMLDivElement>(null);
   const [scrollHeight, setScrollHeight] = useState<number>(300);

   let obj = JSON.parse(atob(location.state.data));
   let m_group_id = obj.hasOwnProperty("m_group_id") ? obj.m_group_id : -1;

   const [data, setData] = useState<dashboardViewDataType | null>(null);

   useEffect(()=>{

       let _local_variable:any = {};

      async function fetchData(){
 
        let res = await api.fetch({'type':'departmentWiseReport', 'department_id':obj.department_id, 'year':obj.m_year, 'm_group_id':m_group_id, 'sub_department_id':-1});
         
        let allData = res.data;
        _local_variable = {...allData};

        _local_variable['application_convenience'] = [
            {title:'Percentage of Users Reporting Smooth Application Experience', score:allData['application_convenience']['none']},
            {info:'Registering for an account or service should be straightforward and hassle-free. Application convenience encompasses a user-friendly registration flow with minimal required fields.',title:'Percentage of Users Encountering Registration Challenges', score:allData['application_convenience']['1']},
            {info:'Application convenience involves designing forms that are easy to understand and complete. This includes logical form layouts, autofill capabilities, and contextual help prompts to guide users through the process efficiently',title:'Percentage of Users Encountering Form Filling Challenges', score:allData['application_convenience']['2']},
            {info:'Application convenience means providing users with a seamless process for uploading documents',title:'Percentage of Users Experiencing Document Upload Issues', score:allData['application_convenience']['3']},
            {info:'Users should experience minimal friction when submitting documents or forms. Application convenience entails clear instructions, intuitive form fields, and error validation to ensure smooth submission without unnecessary delays or errors',title:'Percentage of Users Experiencing Form Submission Issues', score:allData['application_convenience']['4']},
            {title:'Percentage of Users Experiencing Other Challenges', score:allData['application_convenience']['other']}
        ];

        _local_variable['tracking_convenience'] = [
            {title:'Percentage of Users Reporting Smooth Application Experience', score:allData['tracking_convenience']['none']},
            {info:'A specific feature within application convenience that enables users to make payments electronically through the application or platform, typically using credit/debit cards, online banking, or digital wallets.',title:'Percentage of Users Encountering difficulties with Online Payments', score:allData['tracking_convenience']['1']},
            {info:'The ability for users to monitor the progress or status of their submitted applications within the application or platform, providing transparency and updates on the process',title:'Percentage of Users Experiencing Tracking Issues in Application Status', score:allData['tracking_convenience']['2']},
            {info:'Communication channels integrated into the application to notify users about important updates, such as application status changes, reminders, or alerts, via SMS messages, emails, or in-app notifications',title:'Percentage of Users Encountering Issues with SMS/Email Alerts', score:allData['tracking_convenience']['3']},
            {info:'A feature allowing users to access and download licenses or permits directly from the application once their applications have been approved, providing immediate access to authorized content or services',title:'Percentage of Users Encountering Download License Issues', score:allData['tracking_convenience']['4']},
            {title:'Percentage of Users Experiencing Other Challenges', score:allData['tracking_convenience']['other']}
        ];

        _local_variable['process_convenience'] = [
            {info:'All necessary information required for the application process is readily available within the platform, streamlining user interactions and reducing the need for external data sources.',title:'Percentage of users who successfully utilized the available basic information', score:allData['process_convenience']['qc1']},
            {info:'Users are able to submit their applications electronically through the provided online system, eliminating the need for physical visits to department offices and promoting a more convenient and accessible application process',title:'Percentage of users reporting manual submission', score:allData['process_convenience']['qd1']},
            {info:'The application process progresses smoothly within the platform, with minimal involvement from department officials. Automated workflows and systems ensure that tasks are completed efficiently without unnecessary manual intervention, enhancing overall process efficiency and user experience',title:'Percentage of users reporting manual intervention', score:allData['process_convenience']['qd2']}
        ];

        setData(_local_variable);

      }
      fetchData();
  
   },[obj.department_id, obj.m_year, m_group_id]);

   useLayoutEffect(()=>{
        if(topref?.current){
            let h = window.innerHeight - (topref.current?.clientHeight + 40 + 56);
            setScrollHeight(h);
        }
   },[]);

  return (
     <>
        <Paper mb={16} ref={topref} py={4}>
            <Flex align='center' justify='space-between' gap={8}>
            <Text fw={500} size="sm">{obj.department} - {obj.m_year}</Text>
            <Button leftSection={<i className="fa-regular fa-angles-left"></i>} color="red.8" onClick={()=>{window.history.go(-1)}}>Back</Button>
          </Flex>
        </Paper>
        <ScrollArea h={scrollHeight} scrollbars="y" offsetScrollbars>
            {
                data !== null && 
                <Grid gutter={8} align="stretch">
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            info='Application convenience refers to the ease, efficiency, and user-friendly 
                                experience provided by a software application or platform. It encompasses 
                                streamlined processes such as document upload, submission, form filling, 
                                registration, and other interactions, ensuring minimal friction and 
                                enhancing overall usability for users.' 
                            title= "Application Convenience" 
                            formula='Percentage of Users Reporting Smooth Application Experience' 
                            score={data.tooltip.application_convenience}>
                            <>
                            {
                                data['application_convenience'].map((item, index)=>(
                                    <TitleWithProgressBar key={index} title={item.title} score={item.score} info={item.info} />
                                ))
                            }
                            </>
                        </DashViewCard>
                    </Grid.Col>
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            info='Payment and application status tracking convenience encompasses the seamless ability 
                            for users to both make transactions and monitor the progress of their submitted applications 
                            within the software platform, ensuring a streamlined and transparent experience.'
                            title= "Payment and Application Status Tracking Convenience" 
                            formula='Percentage of Users Reporting Smooth Application Experience in Payment and Application Status Tracking Convenience' 
                            score={data.tooltip.tracking_convenience}>
                            <>
                            {
                                data['tracking_convenience'].map((item, index)=>(
                                    <TitleWithProgressBar  key={index} title={item.title} score={item.score} info={item.info} />
                                ))
                            }
                            </>
                        </DashViewCard>
                    </Grid.Col>
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            info='Process convenience refers to the efficiency and ease of completing tasks or procedures 
                            within a system or platform, emphasizing the utilization of basic information, avoidance of 
                            manual submission through physical visits, and minimization of manual intervention by department
                            officials.' 
                            title= "Process Convenience" 
                            formula='Percentage Average of Basic information, Non Manual Submission and Non Manual Intervention' 
                            score={data.tooltip.process_convenience}>
                            <>
                            {
                                data['process_convenience'].map((item, index)=>(
                                    <TitleWithProgressBar key={index} title={item.title} score={item.score} info={item.info} />
                                ))
                            }
                            </>
                        </DashViewCard>
                    </Grid.Col>
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            title= "Timeline Compliance" 
                            formula='Percentage of users indicating timely acquisition of necessary approvals' 
                            score={data.tooltip.timeline_compliance}>
                            <>
                                <GaugeChart score={data.timeline_compliance}/>
                                <Text size="sm" ta='center'>Percentage of users indicating timely acquisition of necessary approvals</Text>
                            </>
                        </DashViewCard>
                    </Grid.Col>
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            title= "Performance Rating of the Online System" 
                            info='Average percentage rating for service availability, user friendliness, delivery process,
							and adequacy of information, contributing to the overall performance score of the online system.' 
                            formula="Percentage Average of Service Availability, User Friendliness, Delivery Process,and Adequate Information"
                            score={data.tooltip.timeline_compliance}>
                            <>
                                <Grid gutter={8}>
                                    <Grid.Col span={6}>
                                        <GaugeChart score={data.performance_rating.qf1} label="Service Availabilty"  height={120} thickness={10} centerY={80}/>
                                    </Grid.Col>
                                    <Grid.Col span={6}>
                                        <GaugeChart score={data.performance_rating.qf2} label="User Friendliness"  height={120}  thickness={10} centerY={80}/>
                                    </Grid.Col>
                                    <Grid.Col span={6}>
                                        <GaugeChart score={data.performance_rating.qf3} label="Delivery Process"  height={120} thickness={10} centerY={80}/>
                                    </Grid.Col>
                                    <Grid.Col span={6}>
                                        <GaugeChart score={data.performance_rating.qf4} label="Adequate Information" height={120}  thickness={10} centerY={80}/>
                                    </Grid.Col>
                                </Grid>
                            </>
                        </DashViewCard>
                    </Grid.Col>
                    <Grid.Col span={{base:12, lg:4, md:6}}>
                        <DashViewCard 
                            title= {`Overall Score ( Completed surveys: ${data.completed_survey} )`}
                            info='Overall score representing the Percentage Average of application convenience, payment and 
                                application status tracking convenience, process convenience, timeline compliance and performance 
                                rating of the online system.' 
                            formula="Percentage Average of application convenience, payment and application status tracking convenience, process convenience, timeline compliance and performance rating of the online system"
                            score={data.tooltip.overall_score}>
                            <Flex align='center' justify='center' h='100%'>
                                <GaugeChart score={data.overall_score}/>
                            </Flex>
                        </DashViewCard>
                    </Grid.Col>
                </Grid>
            }
        </ScrollArea>
     </>
  )
}

export default DashboardView