import { Text, Box, Tooltip} from "@mantine/core"
import ProgressBar from "../../components/ProgressBar"
import "../../assets/css/dashboard.scss";

export type TitleWithProgressBarType = {
    title:string;
    score:string | number;
    info?:string;
}

function TitleWithProgressBar(props:TitleWithProgressBarType) {
  return (
    <Box className="list-progress" mb={8}>  
        {
            props.info && <Tooltip color='white' c='black' multiline w={220} withArrow transitionProps={{ duration: 200 }}
            label={props.info}>
                <Text className="list-icon" size="14px"><i className="fa-solid fa-circle-info"></i></Text>
            </Tooltip>
        }

        <Text size="sm" mb={8}>{props.title}</Text>
        <ProgressBar score={props.score}/>
    </Box>
  )
}

export default TitleWithProgressBar