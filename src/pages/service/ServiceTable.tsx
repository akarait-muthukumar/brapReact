
import { Grid,Text, Pagination, Select, Table, UnstyledButton, useMantineTheme, ScrollArea} from "@mantine/core"
import { useService } from "../../contextapi/ServiceContext";
import { useEffect, useState } from "react";
import { useLayout } from "../../contextapi/LayoutContext";


export default function ServiceTable() {
  const theme = useMantineTheme();

  const {mainRef} = useLayout();

  const {state, dispatch, pageTitleBarRef, tableHeaderRef, tableFooterRef} = useService();

  const [scrollHeight, setScrollHeight] = useState(300);

  const showData = [
    {value:'20',label:'20'},
    {value:'40',label:'40'},
    {value:'80',label:'80'},
    {value:'-1',label:'All'},
  ];

  useEffect(()=>{

     const _fnScrollHeight = () =>{
        let h1 = pageTitleBarRef.current?.clientHeight;
        let h2 = tableHeaderRef.current?.clientHeight;
        let h3 = tableFooterRef.current?.clientHeight;
        let mainHeight = mainRef.current?.clientHeight;

        let h = (mainHeight ?? 0) - ((h1 ?? 0) + (h2 ?? 0) + (h3 ?? 0)) - 20;

        setScrollHeight(h);
     }

     _fnScrollHeight();

      window.addEventListener('resize', _fnScrollHeight);

      return ()=> window.removeEventListener('resize', _fnScrollHeight);

  },[pageTitleBarRef, tableHeaderRef, tableFooterRef, mainRef]);

  return (
      <>
      <ScrollArea scrollbars='x' miw='100%'>
      <Table w={"100%"} ref={tableHeaderRef}>
        <Table.Thead bg={theme.primaryColor} c={'white'}>
          <Table.Tr>
            <Table.Th ta={'center'} w={50}>S.no</Table.Th>
            <Table.Th w={'auto'}>Service</Table.Th>
            <Table.Th w={200}>Reform Number</Table.Th>
            <Table.Th ta={'center'} w={90}>Action</Table.Th>
          </Table.Tr>
        </Table.Thead>
      </Table>
      </ScrollArea>
      <ScrollArea h={scrollHeight} miw='100%'>
        <Table w={"100%"} withTableBorder withColumnBorders>
          <Table.Tbody>
              { state.renderData !== null && 
                <>
                    {
                      state.renderData.map((td,index)=>{
                        return <Table.Tr key={index}>
                        <Table.Td ta={'center'} w={50}>{((state.pageValue - 1) * parseInt(state.show)) + (index + 1)}</Table.Td>
                        <Table.Td w={'auto'}>{td.service_name}</Table.Td>
                        <Table.Td w={200}>{JSON.parse(td.reform_number).join(', ')}</Table.Td>
                        <Table.Td ta={'center'} w={90}>
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
      </ScrollArea>

      {
        state.data !== null && 
        <>
          <Grid gutter={8} align='center' justify='space-between' ref={tableFooterRef}>
              <Grid.Col span={'content'}>
                  <Select data={showData} defaultValue={state.show} size={"xs"} w={50} onChange={(_value)=>  dispatch({type:'show', payload:_value == null ? state.show : _value})}/>
              </Grid.Col>
              <Grid.Col span={'content'}><Text ta={'center'} size="sm">{state.entries}</Text></Grid.Col>
              <Grid.Col span={'content'}>
                  <Pagination total={state.pageTotal} siblings={window.innerWidth < 600 ? 0 : 1} size={'sm'} value={state.pageValue} onChange={(_value)=>  dispatch({type:'pageValue', payload:_value})}/>
              </Grid.Col>
          </Grid>
        </>
      }

      </>
  );
}
