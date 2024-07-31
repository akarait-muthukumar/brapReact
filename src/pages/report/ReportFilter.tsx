import { Select, Button, Box} from "@mantine/core"
import { useReport } from "../../contextapi/ReportContext";
import { api } from "../../utils/ApiService";
import { filterType } from "../../types/Report";
function ReportFilter() {

    const {state, dispatch} = useReport();

    const submitForm = async (value:filterType) => {
        let res =  await api.fetch({'type':'getReportList', ...state.filter});
        let payload = ( res.data.length === 0 ) ? null : res.data;
        dispatch({'type':'tableData', "payload":payload});
    }
    

    
  return (
    <Box component="form" onSubmit={(value)=>{}}>
      <Select />
      <Button>sdsadsad</Button>
    </Box>
  )
}

export default ReportFilter