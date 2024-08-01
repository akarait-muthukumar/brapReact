import { Select, Button, ComboboxData, Grid } from "@mantine/core"
import { MonthPickerInput } from "@mantine/dates";
import { useReport } from "../../contextapi/ReportContext";
import { api } from "../../utils/ApiService";
import { useEffect, useState } from "react";
import type { fieldErrorType, filterType} from "../../types/Report";
import '@mantine/dates/styles.css';
function ReportFilter() {

  const { state, dispatch } = useReport();

  const [year, setYear] = useState<ComboboxData | undefined>();
  const [department, setDepartment] = useState<ComboboxData | undefined>();
  const [reform, setReform] = useState<ComboboxData | undefined>();

  const [status] = useState<ComboboxData | undefined>([
    { value: 'Completed', label: 'Completed' },
    { value: 'InComplete', label: 'InComplete' },
    { value: 'Not Interested', label: 'Not Interested' }
  ]);

  const [surveyMonth, setSurveyMonth] = useState<any>({
    minDate: null,
    maxDate: null,
  });

  const [fieldError, setFieldError] = useState<fieldErrorType>({
      year:false,
      survey_month:false,
      department_id:false,
      reform:false,
      status:false,
  });

  useEffect(() => {
    api.fetch({ 'type': 'getYear' }).then((res) => {
      setYear(res?.data);
    });
  }, []);

  

  const handleChangeYear = async (_value: string | null) => {

    dispatch({ type: 'year', payload: _value });
    dispatch({ type: 'survey_month', payload: [null, null] });
    dispatch({ type: 'department_id', payload: null });
    dispatch({ type: 'reform', payload: null })

    await api.fetch({ 'type': 'getMonthRange', 'year': state.filter.year }).then((res) => {
      let minDate = res.data.min_date.split('-');
      let maxDate = res.data.max_date.split('-');
      setSurveyMonth({
        minDate: new Date(minDate[0], parseInt(minDate[1]) - 1, minDate[2]),
        maxDate: new Date(maxDate[0], parseInt(maxDate[1]) - 1, maxDate[2])
      });
    });

    await api.fetch({ 'type': 'getDepartmentList', 'year': state.filter.year, 'all_value': true }).then((res) => {
      setDepartment(res.data);
    });

  }

  const handleChangeDepartment = async (_value: string | null) => {

    dispatch({ type: 'department_id', payload: _value });
    dispatch({ type: 'reform', payload: null })

    await api.fetch({
      'type': 'getReform',
      'year': state.filter.year,
      'department_id': state.filter.department_id,
      'all_value': true
    }).then((res) => {
      setReform(res.data);
    });

  }

  const getResult = ()=>{
  
    for(let key in state.filter){
      if(key === 'survey_month'){
          if(state.filter['survey_month'][0] === null){
            setFieldError({...fieldError, survey_month:true});
          }
      }
      else if(state.filter[key as keyof filterType] == null){
        setFieldError({...fieldError, [key]:true});
      }
    }
  }

  return (
    <Grid gutter={8} align="end">
      <Grid.Col span={3}>
        <Select
          label="Year"
          value={state.filter.year}
          data={year}
          onChange={(_value) => handleChangeYear(_value)}
          error={fieldError.year}
        />
      </Grid.Col>
      {
        state.filter.year !== null && surveyMonth.minDate !== null && surveyMonth.maxDate !== null &&
        <Grid.Col span={3}>
          <MonthPickerInput
            label="Month"
            minDate={surveyMonth.minDate}
            maxDate={surveyMonth.maxDate}
            type="range"
            value={state.filter.survey_month}
            onChange={(_value) => dispatch({ type: 'survey_month', payload: _value })} 
            error={fieldError.survey_month}
            />
            
        </Grid.Col>
      }
      {
        state.filter.year !== null &&  
        <Grid.Col span={6}>
          <Select
            label="Department"
            value={state.filter.department_id}
            data={department}
            onChange={(_value) => handleChangeDepartment(_value)}
            error={fieldError.department_id}
          />
        </Grid.Col>
      }
      {
        state.filter.year !== null && state.filter.department_id !== null &&
        <Grid.Col span={3}>
          <Select
            label="Reform"
            value={state.filter.reform}
            data={reform}
            onChange={(_value) => dispatch({ type: 'reform', payload: _value })}
            error={fieldError.reform}
          />
        </Grid.Col>
      }

      <Grid.Col span={3}>
        <Select
          label="Status"
          value={state.filter.status}
          data={status}
          onChange={(_value) => dispatch({ type: 'status', payload: _value })}
          error={fieldError.status}
        />
      </Grid.Col>

      <Grid.Col span='auto'>
        <Button leftSection={<i className="fa-regular fa-filter"></i>} color="green.9" onClick={()=>getResult()}>Get Result</Button>
      </Grid.Col>
    
    </Grid>
  )
}

export default ReportFilter