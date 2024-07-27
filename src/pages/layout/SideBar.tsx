import { Box , Flex, Image, NavLink, Text} from "@mantine/core"
import { useNavigate, To} from "react-router-dom";
import { useState, Fragment} from "react";
import type { navsType } from "../../types/layout/Sidebar";
// import TN_Gov  from "../../assets/images/TN_Gov.png";
import guidance_TN_gov  from "../../assets/images/guidance_TN_gov.png";

function SideBar() {
  const navigate = useNavigate();
  const [active, setActive] = useState<Number>(0);

  const navs:navsType[] = [
    {
      text:null,
      line:false,
      children:[
        {
          id:1,
          icon:"fa-solid fa-chart-pie",
          label:'dashboard',
          path:'/',
          m_user_type_id:[10000,1000,100]
        },
        {
          id:2,
          icon:"fa-solid fa-chart-pie",
          label:'dashboard',
          path:'/',
          m_user_type_id:[1]
        },
        {
          id:3,
          icon:"fa-solid fa-chart-pie",
          label:'dashboard',
          path:'/',
          m_user_type_id:[10]
        },
      ]
    },
    {
      text:"Master",
      line:true,
      children:[
        {
          id:4,
          icon:"fa-solid fa-circle",
          label:'department',
          path:'/department',
          m_user_type_id:[10000,1000]
        },
        {
          id:5,
          icon:"fa-solid fa-circle",
          label:'reform number',
          path:'/reformnumber',
          m_user_type_id:[10000,1000]
        },
        {
          id:6,
          icon:"fa-solid fa-circle",
          label:'service',
          path:'/service',
          m_user_type_id:[10000,1000]
        },
        {
          id:7,
          icon:"fa-solid fa-circle",
          label:'survey year',
          path:'/surveyyear',
          m_user_type_id:[10000,1000]
        },
        {
          id:8,
          icon:"fa-solid fa-circle",
          label:'dept wise reform no',
          path:'/deptwisereformno',
          m_user_type_id:[10000,1000]
        }
      ]
    },
    {
      text:null,
      line:false,
      children:[
        {
          id:9,
          icon:"fa-solid fa-clipboard-list-check",
          label:'survey',
          path:'/survey',
          m_user_type_id:[10000,1000,1]
        },
        {
          id:10,
          icon:"fa-solid fa-grid-2",
          label:'MIS status',
          path:'/misstatus',
          m_user_type_id:[10000,1000,100]
        },
        {
          id:11,
          icon:"fa-solid fa-file",
          label:'report',
          path:'/report',
          m_user_type_id:[10000,1000,100]
        },
        {
          id:12,
          icon:"fa-solid fa-file",
          label:'daily call report',
          path:'/dailycallreport',
          m_user_type_id:[10000,1000,100]
        },
        {
          id:13,
          icon:"fa-solid fa-file",
          label:'User Remarks',
          path:'/userremarks',
          m_user_type_id:[100,10,1]
        },
        {
          id:14,
          icon:"fa-solid fa-file",
          label:'Company',
          path:'/company',
          m_user_type_id:[10]
        },
        {
          id:15,
          icon:"fa-solid fa-file",
          label:'Data Uploader Report',
          path:'/datauploaderreport',
          m_user_type_id:[10]
        }
      ]
    }
  ];

  let m_user_type_id = 1000;

  let navlinks:navsType[] = [];

  navs.forEach((obj)=>{
    let _childrens = obj.children.filter(child => child.m_user_type_id.includes(m_user_type_id));
    if(_childrens.length > 0){
      navlinks.push({...obj, children:_childrens});
    }
  });

  const redirectTo = (to:To, index:Number) =>{
    navigate(to);
    setActive(index);
  }

  return (
    <Box className="sidebar">
      <Flex component="header" align='center' px={16} py={8}>
        <Image width='100%' height={48} fit="contain" src={guidance_TN_gov} className="object-pos-start" />
      </Flex>
      <Flex className="navbar" direction='column' py={8} px={16}>
        {
          navlinks.map((parent:navsType, index:number)=>(
            
            <Fragment key={index}>

             {parent.text != null && <Text ps={8} my={8} size="xs" fw={500}  c='gray.6' tt="uppercase">{parent.text}</Text>} 
              <Box>
              {
                parent.children.map((item, key)=>{
                  return <NavLink key={item.id}
                  tt='capitalize' 
                  p={0}
                  fw={500} 
                  leftSection={<i className={item?.icon}></i>}
                  label={item?.label}
                  active = {item.id === active}
                  onClick={()=>redirectTo(item.path, item.id)}/>
                })
              }
              </Box>
              {parent.line && <Box my={8} style={{borderBottom:'.5px solid #dee2e6'}} />} 

            </Fragment>

          ))
        }
      </Flex>
    </Box>
  )
}

export default SideBar