import type {TitleWithProgressBarType} from "../pages/dashboard/TitleWithProgressBar";

export type filterType = {
    year:string | null;
}

export type dataType = {
    no_of_department: string | 0,
    completed_survey: string | 0,
    department_list: {
        DeptID : string;
        department: string;
        department_id : string;
        is_group : string;
        group?:{
            group_name:string;
            m_group_id:string;
            score:string;
        }[]
        m_department_id : string;
        m_parent_department_id: string | null;
        m_year :string;
        score :string;
    }[] | null,
    overall_rating:number
}

export type chartPropsType = {
    score:number;
    label?:string;
    height?:number,
    thickness?:number,
    centerY?:number,
}

export type dashboardViewDataType = {
    application_convenience: TitleWithProgressBarType[];
    tracking_convenience: TitleWithProgressBarType[];
    process_convenience: TitleWithProgressBarType[];
    tooltip:{
        application_convenience:number;
        overall_score:number;
        performance_rating:number;
        process_convenience:number;
        timeline_compliance:number;
        tracking_convenience:number;
    };
    performance_rating:{
        qf1:number;
        qf2:number;
        qf3:number;
        qf4:number;
    }
    completed_survey:string;
    overall_score:number;
    timeline_compliance:number;
}