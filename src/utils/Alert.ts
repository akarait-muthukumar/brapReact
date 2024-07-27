import Swal from 'sweetalert2';

class Alert {
    error = async (values:string)=>{
        return Swal.fire({
            icon:'error',
            title: values,
            allowOutsideClick:false,
            allowEscapeKey:false,
        }).then((result)=>{
            if(result.isConfirmed){
                if(values === 'Token has expired'){
                    return 'signOut'
                }
                else{
                    return true
                }
            }

        })
    }
    success = async (values:string)=>{
        return Swal.fire({
            icon:'success',
            title: values,
            allowOutsideClick:false,
            allowEscapeKey:false,
        }).then((result)=>{
            if(result.isConfirmed){
                return true
            }
        })
    }
    question = async (values:string)=>{
        return Swal.fire({
            icon:'question',
            title: values,
            allowOutsideClick:false,
            allowEscapeKey:false,
            showCancelButton:true,
        }).then((result)=>{
            if(result.isConfirmed){
                return true
            }
            if(result.isDismissed){
                return false
            }
        })
    }
}

export const alert = new Alert();