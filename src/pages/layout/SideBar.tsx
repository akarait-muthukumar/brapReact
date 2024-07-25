import { Box , Flex, Image} from "@mantine/core"
import TN_Gov  from "../../assets/images/TN_Gov.png";
import guidance_TN_gov  from "../../assets/images/guidance_TN_gov.png";

function SideBar() {
  return (
    <Box className="sidebar">
      <Flex component="header" align='center' px={16} py={8}>
        <Image width='100%' height={48} fit="contain" src={guidance_TN_gov} className="object-pos-start" />
      </Flex>
    </Box>
  )
}

export default SideBar