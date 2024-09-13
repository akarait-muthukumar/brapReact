import axios from "axios";
import { alert } from "./Alert";

import logout from "./logout";

export const instance = axios.create({
    baseURL: 'http://192.168.0.116/brapre/api/ajax.php',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        "X-eodb-Authorization":sessionStorage.getItem("token") == null ? "" : sessionStorage.getItem("token")
    }
});


class ApiService{

    async fetch(payload:any){

        let details = sessionStorage.getItem('details');
        if(details){
            payload = {...payload, "userDetails":details};
        }

        try{
            let response =  await instance.post('',payload);
            if(response.data.error_code === 200){
                return response.data;
            }
            else if(response.data.error_code === 440){
                await alert.error('Token Expired', true).then((res)=>{
                    if(res.isConfirmed){
                        sessionStorage.clear();
                        instance.defaults.headers['X-eodb-Authorization'] = null;
                        logout().redirectToLogin();
                    }
                });
            }
            else{
                throw new Error(response.data.message)
            }
        }
        catch(error){
            return error;
        }

    }
    
}

export const api = new ApiService();