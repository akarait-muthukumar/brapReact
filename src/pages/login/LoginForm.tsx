import {Box, TextInput, Button, useMantineTheme, Group} from '@mantine/core';
import { useNavigate } from 'react-router-dom';
import { useForm , isNotEmpty } from '@mantine/form';
import { api } from '../../utils/ApiService';
import { formType } from '../../types/Login';
import { alert } from '../../utils/Alert';
import { instance } from '../../utils/ApiService';

function LoginForm() {

  const navigate = useNavigate();

  const theme = useMantineTheme();

  const form = useForm<formType>({
    mode: 'uncontrolled',
    initialValues: {
      userID: '',
      user_password: '',
    },

    validate: {
      userID:isNotEmpty('Required'),
      user_password:isNotEmpty('Required'),
    },
  });

  const handleSubmit = (values:formType) =>{
    let promise = api.fetch({type:'auth',...values});
    promise.then((res)=>{
      if(res.error_code === 200){
        if(res.data.hasOwnProperty("already_login")){
          alert.error("already_login");
        }
        else{
          alert.success(res.message).then(()=>{
            instance.defaults.headers['X-eodb-Authorization'] = res.data.token;
         
            sessionStorage.setItem("details", btoa(JSON.stringify(res.data.details)));
            sessionStorage.setItem("token", res.data.token);
            navigate('/dashboard', {replace:true});
          });
        }
    }
    else{
      alert.error(res.message);
    }
    });
  }

  return (
    <>
        <Box component='form' onSubmit={form.onSubmit((values) => handleSubmit(values))}  my={{base:20, lg:40}}>
            <TextInput label="User ID"  key={form.key('userID')} {...form.getInputProps('userID')}/>
            <TextInput  type='password' autoComplete='off' my={16} label="Password" key={form.key('user_password')} {...form.getInputProps('user_password')}/>
            <Group justify='end'>
                <Button type='submit' leftSection={<i className='fas fa-key'></i>} color={theme.primaryColor}>Login</Button>
            </Group>
        </Box>
    </>
  )
}

export default LoginForm