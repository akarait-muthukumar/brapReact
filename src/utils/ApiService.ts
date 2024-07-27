import axios from "axios";

let URL = "http://192.168.0.116/brapre/api/ajax.php";

class ApiService{

    async fetch(payload:any){

        try{
            let response =  await axios.post(URL, payload, {
                headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            if(response.data.error_code === 200){
                return response.data;
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