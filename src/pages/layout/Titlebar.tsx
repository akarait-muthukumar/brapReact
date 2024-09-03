import { Flex, Title,Box, UnstyledButton, Popover , Text, Avatar, NavLink} from "@mantine/core"
import { useNavigate, To} from "react-router-dom";
import { useState } from "react";
import {useLayout} from "../../contextapi/LayoutContext";

function Titlebar() {
  const navigate = useNavigate();

  const {state, dispatch} = useLayout();

  const redirectTo = (to:To) =>{
    navigate(to);
  }

  const logout = () =>{}

  const [isOpened, setOpened] = useState(false);


  return (
    <Flex component="header" justify='space-between' align='center' px='sm' py='xs'>
      <UnstyledButton onClick={()=>{dispatch({type:'panelActive',payload:!state.panelActive})}}><i className="fa fa-bars"></i></UnstyledButton>
      <Title order={5}>Business Reforms Action Plan 2024 - Customer Experience Transformation</Title>
      <Popover opened={isOpened}> 
        <Popover.Target>
            <UnstyledButton onClick={()=>setOpened(!isOpened)}>
                <Flex align='center' gap={4}>
                  <Box>
                    <Text lh='xs' ta='end' fw={500} size="sm">Admin User</Text>
                    <Text ta='end' fw={500} size="xs" c='gray.6'>Super Admin</Text>
                  </Box>
                  <Avatar miw={32} w={32} h={32} variant="light"  bg='white' color="#0d6efd" radius="xl"><i className="fa-regular fa-user"></i></Avatar>
                </Flex>
            </UnstyledButton>
        </Popover.Target>
        <Popover.Dropdown p={0}>
            <NavLink
              tt='capitalize' 
              p={0}
              pr={12}
              fw={500} 
              leftSection={<Box><i className="fa-solid fa-user"></i></Box>}
              label="user management"
              onClick={()=>{setOpened(false); redirectTo('/usermanagement');}} 
            />
            <NavLink
              tt='capitalize' 
              p={0}
              pr={12}
              fw={500} 
              leftSection={<Box c={'red.8'}><i className="fas fa-lock"></i></Box>}
              label="Logout"
              onClick={()=>{setOpened(false); logout();}} 
            />
        </Popover.Dropdown>
      </Popover>
    </Flex>
  )
}

export default Titlebar