import { Paper, Grid, Text, Button, useMantineTheme, Input, } from "@mantine/core"
import { useEffect } from "react";
import { api } from "../../utils/ApiService";
import ServiceTable from "./ServiceTable";
import { useService } from "../../contextapi/ServiceContext";

function Service() {
  const theme = useMantineTheme();

  const { state, dispatch , pageTitleBarRef} = useService();

  useEffect(() => {
    if (state.data == null) {
      api.fetch({ 'type': 'getServiceList' }).then((res) => {
        dispatch({ type: 'data', payload: res?.data });
        dispatch({ type: 'entries', payload: `Showing 1 to ${state.show} of ${res?.data.length} entries`});
        dispatch({ type: 'pageTotal', payload: Math.ceil(res?.data.length / parseInt(state.show)) });
        dispatch({ type: 'renderData', payload: res?.data.slice(0, parseInt(state.show))});
      });
    }
  }, [state.show, state.data, dispatch]);

  return (
    <>
      <Paper mb={8} py={4} ref={pageTitleBarRef}>
        <Grid gutter={8} align='center' justify='space-between'>
          <Grid.Col span={'auto'} flex={1}><Text fw={500} size="sm">Service</Text></Grid.Col>
          <Grid.Col span={{ lg: 3, md: 4 }}>
            <Input placeholder="search"  value={state.search} onChange={(_value)=>{dispatch({type:'search', payload:_value.target.value == null ? '' : _value.target.value.trim()})}}/>
          </Grid.Col>
          <Grid.Col span={'content'}>
            <Button leftSection={<i className="fas fa-plus"></i>} color={theme.primaryColor} >Add</Button>
          </Grid.Col>
        </Grid>
      </Paper>
      {
        state.data !== null && <ServiceTable />
      }


    </>

  )
}

export default Service