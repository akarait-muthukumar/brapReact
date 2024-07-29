import { Paper, Title, useMantineTheme} from "@mantine/core"

 type CountCardType = {
    title:string,
    count:string | 0,
    icon:string
 }

function CountCard(props:CountCardType) {
    const theme = useMantineTheme();

  return (
    <Paper>
        <Title order={5} fw={500} c={theme.primaryColor} mb={8}>
            <i className={props.icon}></i> {props.title}
        </Title>
        <Title order={2}>{Number(props.count).toLocaleString()}</Title>
    </Paper>
  )
}

export default CountCard