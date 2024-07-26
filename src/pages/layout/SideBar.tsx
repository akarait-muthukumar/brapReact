import { Box , Flex, Image, NavLink} from "@mantine/core"
import { useNavigate, To} from "react-router-dom";
import { useState } from "react";
// import TN_Gov  from "../../assets/images/TN_Gov.png";
import guidance_TN_gov  from "../../assets/images/guidance_TN_gov.png";

function SideBar() {
  const navigate = useNavigate();
  const [active, setActive] = useState<Number>(0);


  const navlinks = [
    {
      icon:"fa-solid fa-chart-pie",
      label:'dashboard',
      path:'/dashboard'
    },
    {
      title:'Master',
      path:''
    },
    {
      icon:"fa-solid fa-circle",
      label:'department',
      path:'/department'
    },
    {
      icon:"fa-solid fa-circle",
      label:'reform number',
      path:'/reformnumber'
    },
    {
      icon:"fa-solid fa-circle",
      label:'service',
      path:'/service'
    },
    {
      icon:"fa-solid fa-circle",
      label:'survey year',
      path:'/surveyyear'
    },
    {
      icon:"fa-solid fa-circle",
      label:'dept wise reform no',
      path:'/deptwisereformno'
    },
    {
      icon:"fa-solid fa-clipboard-list-check",
      label:'survey',
      path:'/survey'
    },
    {
      icon:"fa-solid fa-grid-2",
      label:'MIS status',
      path:'/misstatus'
    },
    {
      icon:"fa-solid fa-file",
      label:'report',
      path:'/report'
    },
    {
      icon:"fa-solid fa-file",
      label:'daily call report',
      path:'/dailycallreport'
    }
  ];

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
          navlinks.map((item, index)=>{
            return (item.hasOwnProperty('title') ? <></> : <NavLink key={index}
              tt='capitalize' 
              p={0}
              fw={500} 
              leftSection={<i className={item?.icon}></i>}
              label={item?.label}
              active = {index === active}
              onClick={()=>redirectTo(item.path, index)} 
            />)
          }

            
          )
        }
      </Flex>
    </Box>
  )
}

export default SideBar