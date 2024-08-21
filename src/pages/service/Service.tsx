import { Paper, Grid,Text, Button , useMantineTheme, Input,} from "@mantine/core"
import { useEffect } from "react";
import { api } from "../../utils/ApiService";
import ServiceTable from "./ServiceTable";
import { useService } from "../../contextapi/ServiceContext";

function Service() {
  const theme = useMantineTheme();

  const {state, dispatch} = useService();

  useEffect(()=>{
    api.fetch({'type':'getServiceList'}).then((res)=>{
        dispatch({type:'data', payload: res?.data});
        dispatch({type:'pageTotal', payload: Math.ceil(res?.data.length / 20)});
    });
  },[dispatch]);

  return (
    <>
      <Paper mb={8} py={4}>
        <Grid gutter={8} align='center' justify='space-between'>
          <Grid.Col span={'auto'} flex={1}><Text fw={500} size="sm">Service</Text></Grid.Col>
          <Grid.Col span={{lg:3, md:4}}>
            <Input placeholder="search"/>
          </Grid.Col>
          <Grid.Col span={'content'}>
            <Button leftSection={<i className="fas fa-plus"></i>} color={theme.primaryColor} >Add</Button>
            </Grid.Col>
        </Grid>
      </Paper>
      {
        state.data !== null &&  <ServiceTable/>
      }
     
      
    </>

  )
}

export default Service