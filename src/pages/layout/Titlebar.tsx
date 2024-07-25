import { Flex, Title,Box, UnstyledButton, Popover , Text} from "@mantine/core"
function Titlebar() {
  return (
    <Flex component="header" justify='space-between' align='center' px='sm' py='xs' bg='gray.0'>
      <UnstyledButton><i className="fa fa-bars"></i></UnstyledButton>
      <Title order={5}>Business Reforms Action Plan 2024 - Customer Experience Transformation</Title>
      <Popover>
        <Popover.Target>
            <UnstyledButton>
                <Flex align='center' gap={8}>
                    <Text></Text>
                </Flex>
            </UnstyledButton>
        </Popover.Target>
      </Popover>
    </Flex>
  )
}

export default Titlebar