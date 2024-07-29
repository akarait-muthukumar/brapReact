import { Paper, Grid, Text, Box, Select, useMantineTheme, ComboboxData, Table, ScrollArea, UnstyledButton, Flex } from "@mantine/core"
import { useState, useEffect, useRef, useLayoutEffect} from "react";
import GaugeChart from "./GaugeChart";
import type { filterType, dataType } from "../../types/Dashboard";
import { api } from "../../utils/ApiService";
import CountCard from "../../components/CountCard";

function Dashboard() {
  const theme = useMantineTheme();

  const [scrollHeight, setScrollHeight] = useState<number>(0)
  const topRef = useRef<HTMLDivElement>(null);
 
  const handler = () =>{
    let height = window.innerHeight - (Number(topRef.current?.clientHeight) + 90);
    setScrollHeight(height)
  }
 
  useLayoutEffect(()=>{
    handler();
  },[])


  const [year, setYear] = useState<ComboboxData | undefined>();

  const [filter, setFilter] = useState<filterType>({
    year: '2024'
  });

  const [data, setData] = useState<dataType>({
    no_of_department: 0,
    completed_survey: 0,
    department_list: null,
    overall_rating: 0
  });

  useEffect(() => {
    api.fetch({ 'type': 'getYear' }).then((res) => {
      setYear(res?.data);
    });
  }, []);

  useEffect(() => {
    let obj = {} as dataType;

    const fetchData = async () => {
      await api.fetch({ 'type': 'getCompletedSurveyCount', ...filter }).then((res) => {
        obj = { ...obj, completed_survey: res?.data['count'] };
      });
      await api.fetch({ 'type': 'getDepartmentCount', ...filter }).then((res) => {
        obj = { ...obj, no_of_department: res?.data['count'] };
      });
      await api.fetch({ 'type': 'getDepartmentPeformance', ...filter }).then((res) => {

        obj = { ...obj, department_list: res?.data?.list, overall_rating: Math.ceil(Number(res?.data?.overall_rating)) };
      });
    }
    fetchData().then(() => setData({ ...obj }));
  }, [filter]);

  const displayGroupItems = (index:number, department_id:string) =>{
    let row =  data.department_list?.filter(obj => obj.department_id === department_id)[0];
    if(row?.group !== undefined && row?.group.length > 0){
      row?.group.map((item, index)=>{
         return <>
            <Table.Tr key={item.m_group_id}>
              <Table.Td ta='center' w={60}>{index + 1}</Table.Td>
              <Table.Td >{item.group_name}</Table.Td>
              <Table.Td ta='center' w={225}><Box className="progress"><Box className="progress-bar" style={{ width: Math.ceil(Number(item.score)) + "%" }}>{Math.ceil(Number(item.score))}</Box></Box></Table.Td>
              <Table.Td ta='center' w={75}>
                {
                  Number(item.score) > 0 && <Box c={(Number(data.overall_rating) > Number(item.score)) ? 'red' : 'green'}><i className="fa-solid fa-flag"></i></Box>
                }
              </Table.Td>
              <Table.Td ta='center' w={120}>
                <Flex gap={8} align='center' justify='center'>
                  {Number(item.score) > 0 && <UnstyledButton><Text size="sm" c={theme.primaryColor}><i className='fa-solid fa-eye text-indigo'></i></Text></UnstyledButton>}
                </Flex>
              </Table.Td>
            </Table.Tr>
         </>
      });
    }
  }

  return (
    <>
      <Box ref={topRef}>
        <Paper mb={8}>
          <Text fw={500} size="sm">TamilNadu Overall Performance</Text>
        </Paper>
        <Grid gutter={8} pb='sm'>
          <Grid.Col span={{ lg: 8 }}>
            <Grid gutter={8}>
              <Grid.Col span={{ lg: 12 }}>
                <Paper>
                  <Box w={{ base: '100%', lg: '33%', md: "50%" }}>
                    <Select
                      label="Assessment Year"
                      value={filter.year}
                      withCheckIcon={false}
                      data={year}
                      onChange={(_value) => setFilter({ ...filter, "year": _value })}
                    />
                  </Box>
                </Paper>
              </Grid.Col>
              <Grid.Col span={{ lg: 6 }}>
                <CountCard title="No Of Departments" icon="fa-regular fa-building-columns" count={data.no_of_department} />
              </Grid.Col>
              <Grid.Col span={{ lg: 6 }}>
                <CountCard title="Completed Surveys" icon="fa-regular fa-square-poll-horizontal" count={data.completed_survey} />
              </Grid.Col>
            </Grid>
          </Grid.Col>
          <Grid.Col span={{ lg: 4 }}>
            {
              data.overall_rating !== 0 && <Paper p={0} h='100%'><GaugeChart score={data.overall_rating} /></Paper>
            }
          </Grid.Col>
        </Grid>
      <Table>
        <Table.Thead bg={theme.primaryColor} c='white'>
          <Table.Tr>
            <Table.Th ta='center' w={60}>S.No</Table.Th>
            <Table.Th >Department</Table.Th>
            <Table.Th ta='center' w={225}>Performance</Table.Th>
            <Table.Th ta='center' w={75}>Flag</Table.Th>
            <Table.Th ta='center' w={120}>View</Table.Th>
          </Table.Tr>
        </Table.Thead>
      </Table>
      </Box>
      <ScrollArea h={scrollHeight} w='100%'>
        <Table withColumnBorders withTableBorder>
          <Table.Tbody>
            {
              data.department_list !== null &&
              <>
                {
                  data?.department_list.map((item, index) => {
                    return <Table.Tr key={item.department_id}>
                      <Table.Td ta='center' w={60}>{index + 1}</Table.Td>
                      <Table.Td >{item.department}</Table.Td>
                      <Table.Td ta='center' w={225}><Box className="progress"><Box className="progress-bar" style={{ width: Math.ceil(Number(item.score)) + "%" }}>{Math.ceil(Number(item.score))}</Box></Box></Table.Td>
                      <Table.Td ta='center' w={75}>
                        {
                          Number(item.score) > 0 && <Box c={(Number(data.overall_rating) > Number(item.score)) ? 'red' : 'green'}><i className="fa-solid fa-flag"></i></Box>
                        }
                      </Table.Td>
                      <Table.Td ta='center' w={120}>
                        <Flex gap={8} align='center' justify='center'>
                          {Number(item.is_group) === 1 && <UnstyledButton onClick={(e)=>displayGroupItems(index, item.department_id)}><Text c='gray.7'><i className="fa-solid fa-large fa-square-g"></i></Text></UnstyledButton>}
                          {Number(item.score) > 0 && <UnstyledButton><Text size="sm" c={theme.primaryColor}><i className='fa-solid fa-eye text-indigo'></i></Text></UnstyledButton>}
                        </Flex>
                      </Table.Td>
                    </Table.Tr>
                  })
                }
              </>
            }
          </Table.Tbody>
        </Table>
      </ScrollArea>

    </>
  )
}

export default Dashboard