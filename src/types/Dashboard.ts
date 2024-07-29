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
    score:number
}