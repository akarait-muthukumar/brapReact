import {Box, TextInput, Button, useMantineTheme, Group} from '@mantine/core';
import { useNavigate } from 'react-router-dom';
function LoginForm() {
   const navigate = useNavigate();
    const theme = useMantineTheme();
    const redirect = () =>{
      navigate('/dashboard');
    }
  return (
    <>
        <Box component='form' my={{base:20, lg:40}}>
            <TextInput size="sm" label="User ID" placeholder="Enter"/>
            <TextInput  type='password' my={16} size="sm" label="Password" placeholder="Enter"/>
            <Group justify='end'>
                <Button onClick={()=>redirect()} leftSection={<i className='fas fa-key'></i>} size='xs' variant="filled" color={theme.primaryColor}>Login</Button>
            </Group>
        </Box>
    </>
  )
}

export default LoginForm