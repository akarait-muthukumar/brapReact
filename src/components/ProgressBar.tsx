import { Box } from "@mantine/core"

type progressBarType = {
  width?: number;
  score:string | Number;
  bg?:string
}

function ProgressBar(props:progressBarType) {

  let score = Math.ceil(Number(props.score));

  return (
    <>
        <Box className="progress" bg={props.bg ? props.bg : 'gray.2'} w={props.width ? props.width : '100%'}> 
            <Box className="progress-bar" style={{width: (score > 100) ? "100%" : (score + "%") }}>{score}</Box>
        </Box>
    </>
  )
}

export default ProgressBar