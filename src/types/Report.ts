import { Dispatch } from "react";
import { ComboboxData } from "@mantine/core";
type tableDataType = {
    Contact_Address: string
    Contact_Person: string
    Date_of_Final_Approval: string
    Department: string
    E_mail: string
    Mobile_Numer: string
    Name_of_Firm: string
    Reform_Number: string
    Service_Used: string
    survey_comp_status: string
}

export type filterType = {
    year: string | null;
    survey_month: [Date | null, Date | null];
    department_id: string | null;
    reform: string[] | null;
    status: string[] | null;
}

export type initialType = {
    filter: filterType,
    tableData: tableDataType[] | null,
    getReport:boolean
}

export type actionType = { type: 'year', payload: string | null } | { type: 'survey_month', payload: [Date | null, Date | null] } | { type: 'department_id', payload: string | null }
    | { type: 'reform', payload: string[] | null } | { type: 'status', payload: string[] | null } | { type: 'tableData', payload: tableDataType[] | null }
    | { type: 'getReport', payload: boolean }

export type contextType = {
    state: initialType,
    dispatch: Dispatch<actionType>
}

export type fieldErrorType = { 
    year: boolean;
    survey_month: boolean;
    department_id: boolean;
    reform: boolean;
    status: boolean;
}
