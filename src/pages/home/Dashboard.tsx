import { Paper, Grid, Title, Box, Select, useMantineTheme} from "@mantine/core"
import { useState } from "react";
function Dashboard() {
  const theme = useMantineTheme();
  const [year, setYear] = useState<string | null>("2024");
  const data_year = [
    {
      label:'2024',
      value:"2024"
    },
    {
      label:'2022',
      value:"2022"
    },
  ];

  return (
    <>
      <Paper mb={8}>
        <Title order={6} fw={500}>TamilNadu Overall Performance</Title>
      </Paper>
      <Grid gutter={8}>
          <Grid.Col span={{lg:8}}>
            <Grid gutter={8}>
                <Grid.Col span={{lg:12}}>
                  <Paper>
                    <Box w={{base:'100%', lg:'33%', md:"50%"}}>
                      <Select 
                        label = "Assessment Year"
                        value={year}
                        withCheckIcon={false}
                        data={data_year}
                        onChange={(_value)=>setYear(_value)}
                      />
                    </Box>
                  </Paper>
                </Grid.Col>
                <Grid.Col span={{lg:6}}>
                  <Paper>
                    <Title order={5} fw={500} c={theme.primaryColor} mb={8}>
                      <i className="fa-regular fa-building-columns"></i> No Of Departments
                    </Title>
                    <Title order={2}>18</Title>
                  </Paper>
                </Grid.Col>
                <Grid.Col span={{lg:6}}>
                  <Paper>
                    <Title order={5} fw={500} c={theme.primaryColor} mb={8}>
                      <i className="fa-regular fa-square-poll-horizontal"></i> Completed Surveys
                    </Title>
                    <Title order={2}>3,381</Title>
                  </Paper>
                </Grid.Col>
            </Grid>
          </Grid.Col>
          <Grid.Col span={{lg:4}}></Grid.Col>
      </Grid>
    </>
  )
}

export default Dashboard