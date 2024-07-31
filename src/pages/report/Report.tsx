import {useRef } from "react";
import { Paper, Text, Flex, Button } from "@mantine/core";
import ReportFilter from "./ReportFilter";
function Report() {

  const topref = useRef<HTMLDivElement>(null);


  return (
    <>
      <Paper mb={16} ref={topref} py={4}>
        <Flex align='center' justify='space-between' gap={8}>
          <Text fw={500} size="sm">Report</Text>
          <Button leftSection={<i className="fa-regular fa-file-excel"></i>} color="green.9" onClick={() => { window.history.go(-1) }}>Excel</Button>
        </Flex>
      </Paper>

      <Paper>
        <ReportFilter />
      </Paper>

    </>
  )
}

export default Report