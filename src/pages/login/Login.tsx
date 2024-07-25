import {Center , Title, Text , Grid, GridCol, Image } from "@mantine/core"
import surveyor  from "../../assets/images/surveyor.png";
import TN_Gov  from "../../assets/images/TN_Gov.png";
import guidance_TN_gov  from "../../assets/images/guidance_TN_gov.png";
import LoginForm from "./LoginForm";
function Login() {
  return (

        <Center w='100%' p={16} className="login-page">
            <Grid gutter={8} w='100%' justify="center" align="center">
                <GridCol span={{lg:4}} >
                    <Image width={90} height={90} fit="contain" src={surveyor} />
                    <Title ta='center' order={3} c='white'  my={16}>Welcome</Title>
                    <Title order={4} c='white'>Business Reforms Action Plan 2024 - Customer Experience Transformation</Title>
                </GridCol>
                <GridCol span={{lg:8}} bg='white'  px={{base:12,lg:40}} py={12}  className="round-left-side">
                    <Grid gutter={{base:8}}>
                        <GridCol span={{md:6}}>
                            <Image width="auto" h={{base:100, md:200}} fit="contain" src={TN_Gov} />
                        </GridCol>
                        <GridCol span={{md:6}}>
                            <Image className="object-pos-start" width='auto' height={80} fit="contain"  src={guidance_TN_gov} />
                            <LoginForm/>
                            <Text size='sm'>Conceptualized By <Text component="a" href="https://akara.co.in/" target="_blank">Akara Research & Technologies Pvt Ltd</Text></Text>
                        </GridCol>
                    </Grid>
                </GridCol>
            </Grid>
        </Center> 
  )
}

export default Login