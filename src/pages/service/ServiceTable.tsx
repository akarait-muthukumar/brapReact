
import { Grid,Text, Pagination, Select, Table, UnstyledButton, useMantineTheme} from "@mantine/core"
import { useService } from "../../contextapi/ServiceContext";


export default function ServiceTable() {
  const theme = useMantineTheme();

  const {state, dispatch} = useService();

  const showData = [
    {value:'20',label:'20'},
    {value:'40',label:'40'},
    {value:'80',label:'80'},
    {value:'-1',label:'All'},
  ];

  return (
      <>
      <Table w={"100%"} withTableBorder withColumnBorders>
        <Table.Thead bg={theme.primaryColor} c={'white'}>
          <Table.Tr>
            <Table.Th ta={'center'} w={50}>S.no</Table.Th>
            <Table.Th w={'auto'}>Service</Table.Th>
            <Table.Th w={200}>Reform Number</Table.Th>
            <Table.Th ta={'center'} w={90}>Action</Table.Th>
          </Table.Tr>
        </Table.Thead>
        <Table.Tbody>
            { state.renderData !== null && 
              <>
                  {
                    state.renderData.map((td,index)=>{
                      return <Table.Tr key={index}>
                      <Table.Td ta={'center'}>{index + 1}</Table.Td>
                      <Table.Td>{td.service_name}</Table.Td>
                      <Table.Td>{JSON.parse(td.reform_number).join(', ')}</Table.Td>
                      <Table.Td ta={'center'}>
                        <UnstyledButton fz={12} c={theme.primaryColor} me={8}><i className="fa fa-edit"></i></UnstyledButton>
                        <UnstyledButton fz={12} c="red"><i className="fa fa-trash"></i></UnstyledButton>
                      </Table.Td>
                    </Table.Tr>
                    })
                  }
              </>
              
            }
        </Table.Tbody>
      </Table>
      {
        state.data !== null && 
        <>
          <Grid gutter={8} align='center' justify='space-between'>
              <Grid.Col span={'content'}>
                  <Select data={showData} defaultValue={state.show} size={"xs"} w={50}/>
              </Grid.Col>
              <Grid.Col span={'content'}><Text ta={'center'} size="sm">Showing 1 to 80 of 170 entries</Text></Grid.Col>
              <Grid.Col span={'content'}>
                  <Pagination total={state.currentPage} size={'sm'} onChange={(_value)=>  dispatch({type:'currentPage', payload:_value})}/>
              </Grid.Col>
          </Grid>
        </>
      }

      </>
  );
}
