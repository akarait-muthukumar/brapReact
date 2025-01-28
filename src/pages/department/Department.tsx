import { useState } from "react"
import CustomSelect from "../../components/CustomSelect"
import { Button } from "@mantine/core";

function Department() {
  const [year, setYear] = useState<number | null>(null);
  return (
    <>
      <CustomSelect value={year} data={[{label:'2012', value:'2012'},{label:'2013', value:'2013'}]} 
      onChange={(value, row)=>{setYear(value); console.log(row)}} clearable={true}/>

      <Button onClick={()=>setYear(null)}>sdsd</Button>
    </>
  )
}

export default Department