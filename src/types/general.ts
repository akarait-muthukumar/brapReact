export type AuthUserType = {
  DeptID: string;
  deptName: string;
  deviceID: string;
  login_Key: string;
  m_user_type_id: number;
  redirect_url: string;
  user_id: number;
  user_type: string;
  username: string;
} | null;

export type AuthContextType = {
    auth : AuthUserType;
    setAuth:React.Dispatch<React.SetStateAction<AuthUserType>>
}