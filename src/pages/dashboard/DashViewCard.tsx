import { Box, Paper, Flex, Text, Tooltip, useMantineTheme, Badge } from "@mantine/core"


type cardType = {
    title:string;
    info?:string;
    formula:string;
    score:string | number; 
    children:React.ReactNode
}

function DashViewCard(props:cardType) {
    const theme = useMantineTheme();
    
  return (
    <>
        <Paper p={0} h='100%'>
            <Flex gap={8} px={8} py={4} align='center' bg={theme.primaryColor} c='white'>
                {
                    props.info && <> 
                        <Tooltip color='white' c='black' multiline w={220} withArrow transitionProps={{ duration: 200 }}
                            label={props.info}>
                                <Text size="14px"><i className="fa-solid fa-circle-info"></i></Text>
                        </Tooltip>
                    </> 
                }
                <Text size="sm" fw={500}>{props.title}</Text>
            </Flex>
            <Box p='sm' style={{height:"calc(100% - 57px)"}}>{props.children}</Box>
            <Flex gap={4} px={8} py={4} justify="space-between" align='center' bg='gray.1'>
                <Tooltip color='white' c='black' multiline w={220} withArrow transitionProps={{ duration: 200 }}
                    label={props.formula}>
                    <Badge size="xs" circle bg='dark'><Text size="xs" fw={400}>F</Text></Badge>
                </Tooltip>
                <Text size="sm" fw={600}>{Math.ceil(Number(props.score))}%</Text>
            </Flex>
        </Paper>
    </>
  )
}

export default DashViewCard